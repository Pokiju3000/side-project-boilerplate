<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\Process\Process;
use Throwable;

class HealthDiagnosticController extends Controller
{
    public function index(): View
    {
        return view('admin.diagnostics', [
            'checks' => [
                'php' => $this->checkPhp(),
                'database' => $this->checkDatabase(),
                'cache' => $this->checkCache(),
                'queue' => $this->checkQueueWorker(),
                'scheduler' => $this->checkScheduler(),
            ],
        ]);
    }

    /**
     * @return array{status:string,label:string,detail:string}
     */
    private function checkPhp(): array
    {
        return version_compare(PHP_VERSION, '8.3.0', '>=')
            ? $this->up('PHP OK', PHP_VERSION)
            : $this->down('PHP trop ancien', PHP_VERSION.' détecté, PHP 8.3+ attendu.');
    }

    /**
     * @return array{status:string,label:string,detail:string}
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();

            return $this->up('Base de données OK', config('database.default'));
        } catch (Throwable $exception) {
            return $this->down('Base de données indisponible', $exception->getMessage());
        }
    }

    /**
     * @return array{status:string,label:string,detail:string}
     */
    private function checkCache(): array
    {
        try {
            $key = 'diagnostics.cache_probe';
            Cache::put($key, now()->toDateTimeString(), now()->addMinute());

            return Cache::has($key)
                ? $this->up('Cache OK', 'Store : '.config('cache.default'))
                : $this->down('Cache indisponible', 'La valeur de test est introuvable.');
        } catch (Throwable $exception) {
            return $this->down('Cache en erreur', $exception->getMessage());
        }
    }

    /**
     * @return array{status:string,label:string,detail:string}
     */
    private function checkQueueWorker(): array
    {
        if (config('queue.default') === 'sync') {
            return $this->up('Queue synchrone', "QUEUE_CONNECTION=sync ; aucun worker séparé requis.");
        }

        $heartbeat = Cache::get('diagnostics.queue_worker_heartbeat');
        if (is_array($heartbeat) && isset($heartbeat['at'])) {
            return $this->up(
                'Worker actif',
                sprintf(
                    'Dernier signal Laravel : %s%s.',
                    $heartbeat['at'],
                    isset($heartbeat['pid']) ? ' - PID '.$heartbeat['pid'] : '',
                ),
            );
        }

        return $this->warning('Worker non confirmé', 'Aucun heartbeat de queue worker détecté.');
    }

    /**
     * @return array{status:string,label:string,detail:string}
     */
    private function checkScheduler(): array
    {
        try {
            $process = new Process(['php', 'artisan', 'schedule:list']);
            $process->setWorkingDirectory(base_path());
            $process->setTimeout(10);
            $process->run();

            return $process->isSuccessful()
                ? $this->up('Scheduler lisible', 'php artisan schedule:list OK')
                : $this->warning('Scheduler non confirmé', trim($process->getErrorOutput() ?: $process->getOutput()));
        } catch (Throwable $exception) {
            return $this->warning('Scheduler indéterminé', $exception->getMessage());
        }
    }

    /**
     * @return array{status:string,label:string,detail:string}
     */
    private function up(string $label, string $detail): array
    {
        return ['status' => 'up', 'label' => $label, 'detail' => $detail];
    }

    /**
     * @return array{status:string,label:string,detail:string}
     */
    private function warning(string $label, string $detail): array
    {
        return ['status' => 'warning', 'label' => $label, 'detail' => $detail];
    }

    /**
     * @return array{status:string,label:string,detail:string}
     */
    private function down(string $label, string $detail): array
    {
        return ['status' => 'down', 'label' => $label, 'detail' => $detail];
    }
}