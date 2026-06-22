<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ClientProject;
use App\Models\DeliveryPlan;
use App\Models\ExternalImportRun;
use App\Models\ProjectBudget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\Process\Process;
use Throwable;

class HealthDiagnosticController extends Controller
{
    public function index(): View
    {
        $checks = [
            $this->checkPhp(),
            $this->checkDatabase(),
            $this->checkCache(),
            $this->checkQueueWorker(),
            $this->checkScheduler(),
            $this->checkProjectPortfolio(),
            $this->checkPlanningGate(),
            $this->checkBudgetReadiness(),
            $this->checkImportFreshness(),
            $this->checkActivityTrail(),
        ];

        return view('admin.diagnostics', [
            'diagnosticPayload' => [
                'eyebrow' => __('portfolio.diagnostic.eyebrow'),
                'title' => __('portfolio.diagnostic.title'),
                'intro' => __('portfolio.diagnostic.intro'),
                'summary' => $this->summary($checks),
                'checks' => $checks,
                'labels' => [
                    'refresh' => __('portfolio.diagnostic.refresh'),
                    'status' => [
                        'up' => __('portfolio.diagnostic.status.up'),
                        'warning' => __('portfolio.diagnostic.status.warning'),
                        'down' => __('portfolio.diagnostic.status.down'),
                    ],
                ],
            ],
        ]);
    }

    private function checkPhp(): array
    {
        return version_compare(PHP_VERSION, '8.3.0', '>=')
            ? $this->up('php', 'platform', 'php', __('portfolio.diagnostic.checks.php.ok', ['version' => PHP_VERSION]))
            : $this->down('php', 'platform', 'php', __('portfolio.diagnostic.checks.php.down', ['version' => PHP_VERSION]));
    }

    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();

            return $this->up('database', 'platform', 'database', __('portfolio.diagnostic.checks.database.ok', ['connection' => config('database.default')]));
        } catch (Throwable $exception) {
            return $this->down('database', 'platform', 'database', $exception->getMessage());
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'diagnostics.cache_probe';
            Cache::put($key, now()->toDateTimeString(), now()->addMinute());

            return Cache::has($key)
                ? $this->up('cache', 'platform', 'cache', __('portfolio.diagnostic.checks.cache.ok', ['store' => config('cache.default')]))
                : $this->down('cache', 'platform', 'cache', __('portfolio.diagnostic.checks.cache.down'));
        } catch (Throwable $exception) {
            return $this->down('cache', 'platform', 'cache', $exception->getMessage());
        }
    }

    private function checkQueueWorker(): array
    {
        if (config('queue.default') === 'sync') {
            return $this->up('queue', 'platform', 'queue', __('portfolio.diagnostic.checks.queue.sync'));
        }

        $heartbeat = Cache::get('diagnostics.queue_worker_heartbeat');

        return is_array($heartbeat) && isset($heartbeat['at'])
            ? $this->up('queue', 'platform', 'queue', __('portfolio.diagnostic.checks.queue.ok', ['date' => $heartbeat['at']]))
            : $this->warning('queue', 'platform', 'queue', __('portfolio.diagnostic.checks.queue.warning'));
    }

    private function checkScheduler(): array
    {
        try {
            $process = new Process(['php', 'artisan', 'schedule:list']);
            $process->setWorkingDirectory(base_path());
            $process->setTimeout(10);
            $process->run();

            return $process->isSuccessful()
                ? $this->up('scheduler', 'platform', 'scheduler', __('portfolio.diagnostic.checks.scheduler.ok'))
                : $this->warning('scheduler', 'platform', 'scheduler', trim($process->getErrorOutput() ?: $process->getOutput()));
        } catch (Throwable $exception) {
            return $this->warning('scheduler', 'platform', 'scheduler', $exception->getMessage());
        }
    }

    private function checkProjectPortfolio(): array
    {
        $count = ClientProject::count();

        return $count > 0
            ? $this->up('projects', 'workflow', 'projects', __('portfolio.diagnostic.checks.projects.ok', ['count' => $count]))
            : $this->warning('projects', 'workflow', 'projects', __('portfolio.diagnostic.checks.projects.warning'));
    }

    private function checkPlanningGate(): array
    {
        $ready = DeliveryPlan::where('status', 'ready_for_validation')->count();
        $draft = DeliveryPlan::where('status', 'draft')->count();

        return $ready > 0
            ? $this->up('planning', 'workflow', 'planning', __('portfolio.diagnostic.checks.planning.ok', ['ready' => $ready, 'draft' => $draft]))
            : $this->warning('planning', 'workflow', 'planning', __('portfolio.diagnostic.checks.planning.warning', ['draft' => $draft]));
    }

    private function checkBudgetReadiness(): array
    {
        $budgets = ProjectBudget::count();
        $incomplete = ProjectBudget::where('margin_amount', '<=', 0)->count();

        return $budgets > 0 && $incomplete === 0
            ? $this->up('budgets', 'workflow', 'budgets', __('portfolio.diagnostic.checks.budgets.ok', ['count' => $budgets]))
            : $this->warning('budgets', 'workflow', 'budgets', __('portfolio.diagnostic.checks.budgets.warning', ['count' => $budgets, 'incomplete' => $incomplete]));
    }

    private function checkImportFreshness(): array
    {
        $latest = ExternalImportRun::latest('finished_at')->first();

        if (! $latest) {
            return $this->warning('imports', 'workflow', 'imports', __('portfolio.diagnostic.checks.imports.missing'));
        }

        return $latest->warnings_count > 0
            ? $this->warning('imports', 'workflow', 'imports', __('portfolio.diagnostic.checks.imports.warning', ['source' => $latest->source, 'warnings' => $latest->warnings_count]))
            : $this->up('imports', 'workflow', 'imports', __('portfolio.diagnostic.checks.imports.ok', ['source' => $latest->source, 'count' => $latest->records_count]));
    }

    private function checkActivityTrail(): array
    {
        $count = ActivityLog::count();

        return $count > 0
            ? $this->up('activity', 'workflow', 'activity', __('portfolio.diagnostic.checks.activity.ok', ['count' => $count]))
            : $this->warning('activity', 'workflow', 'activity', __('portfolio.diagnostic.checks.activity.warning'));
    }

    private function summary(array $checks): array
    {
        return [
            ['label' => __('portfolio.diagnostic.summary.ready'), 'value' => collect($checks)->where('status', 'up')->count(), 'detail' => __('portfolio.diagnostic.summary.ready_detail')],
            ['label' => __('portfolio.diagnostic.summary.watch'), 'value' => collect($checks)->where('status', 'warning')->count(), 'detail' => __('portfolio.diagnostic.summary.watch_detail')],
            ['label' => __('portfolio.diagnostic.summary.blocked'), 'value' => collect($checks)->where('status', 'down')->count(), 'detail' => __('portfolio.diagnostic.summary.blocked_detail')],
        ];
    }

    private function up(string $key, string $group, string $area, string $detail): array
    {
        return $this->check($key, $group, $area, 'up', $detail);
    }

    private function warning(string $key, string $group, string $area, string $detail): array
    {
        return $this->check($key, $group, $area, 'warning', $detail);
    }

    private function down(string $key, string $group, string $area, string $detail): array
    {
        return $this->check($key, $group, $area, 'down', $detail);
    }

    private function check(string $key, string $group, string $area, string $status, string $detail): array
    {
        return [
            'key' => $key,
            'group' => __('portfolio.diagnostic.groups.'.$group),
            'area' => __('portfolio.diagnostic.areas.'.$area),
            'label' => __('portfolio.diagnostic.labels.'.$key),
            'status' => $status,
            'detail' => $detail,
        ];
    }
}
