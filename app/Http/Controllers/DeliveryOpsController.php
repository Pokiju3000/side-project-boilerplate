<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ClientProject;
use App\Models\ExternalImportRun;
use App\Models\ServiceLine;
use App\Models\SupportResource;
use Illuminate\Contracts\View\View;

class DeliveryOpsController extends Controller
{
    public function dashboard(): View
    {
        $projects = ClientProject::query()
            ->with(['serviceLine', 'owner', 'latestDeliveryPlan', 'budget'])
            ->latest('updated_at')
            ->get();

        $validationRows = $this->validationRows();

        return view('dashboard', [
            'metrics' => [
                ['label' => __('portfolio.metrics.active_projects.label'), 'value' => $projects->count(), 'detail' => __('portfolio.metrics.active_projects.detail')],
                ['label' => __('portfolio.metrics.plans_to_validate.label'), 'value' => $validationRows->where('can_validate', true)->count(), 'detail' => __('portfolio.metrics.plans_to_validate.detail')],
                ['label' => __('portfolio.metrics.budget_gaps.label'), 'value' => $validationRows->filter(fn (array $row): bool => str_contains($row['issue_key'], 'Budget'))->count(), 'detail' => __('portfolio.metrics.budget_gaps.detail')],
                ['label' => __('portfolio.metrics.import_warnings.label'), 'value' => ExternalImportRun::query()->sum('warnings_count'), 'detail' => __('portfolio.metrics.import_warnings.detail')],
            ],
            'projectStates' => [
                ['label' => __('portfolio.status.intake'), 'value' => $projects->where('status', 'intake')->count()],
                ['label' => __('portfolio.status.planning'), 'value' => $projects->where('status', 'planning')->count()],
                ['label' => __('portfolio.status.ready_for_validation'), 'value' => $projects->where('status', 'ready_for_validation')->count()],
                ['label' => __('portfolio.status.validated'), 'value' => $projects->where('status', 'validated')->count()],
            ],
            'priorityProjects' => $projects->take(6),
            'validationRows' => $validationRows->take(5),
            'latestImports' => ExternalImportRun::query()->latest('last_synced_at')->take(4)->get(),
            'activityLogs' => ActivityLog::query()->with('clientProject')->latest()->take(5)->get(),
        ]);
    }

    public function projects(): View
    {
        return view('projects.index', [
            'projects' => ClientProject::query()
                ->with(['serviceLine', 'owner', 'latestDeliveryPlan', 'budget'])
                ->orderByRaw("case status when 'ready_for_validation' then 1 when 'planning' then 2 when 'intake' then 3 else 4 end")
                ->orderBy('target_delivery_on')
                ->get(),
            'serviceLines' => ServiceLine::query()->withCount('clientProjects')->orderBy('name')->get(),
        ]);
    }

    public function project(ClientProject $project): View
    {
        $project->load([
            'serviceLine',
            'deliveryTemplate.activities',
            'owner',
            'latestDeliveryPlan.weeks',
            'latestDeliveryPlan.matrixEntries.templateActivity',
            'latestDeliveryPlan.matrixEntries.deliveryWeek',
            'budget.lines',
            'activityLogs' => fn ($query) => $query->latest(),
        ]);

        $plan = $project->latestDeliveryPlan;
        $activities = $project->deliveryTemplate?->activities ?? collect();
        $matrix = $plan?->matrixEntries
            ->groupBy('template_activity_id')
            ->map(fn ($entries) => $entries->keyBy('delivery_week_id'))
            ?? collect();

        return view('projects.show', [
            'project' => $project,
            'plan' => $plan,
            'activities' => $activities,
            'matrix' => $matrix,
        ]);
    }

    public function validation(): View
    {
        $rows = $this->validationRows();

        return view('operations.validation', [
            'summaryCards' => [
                ['label' => __('portfolio.validation.summary.ready'), 'value' => $rows->where('can_validate', true)->count(), 'tone' => 'info'],
                ['label' => __('portfolio.validation.summary.blocked'), 'value' => $rows->where('tone', 'danger')->count(), 'tone' => 'danger'],
                ['label' => __('portfolio.validation.summary.budget'), 'value' => $rows->filter(fn (array $row): bool => str_contains($row['issue_key'], 'Budget'))->count(), 'tone' => 'warning'],
                ['label' => __('portfolio.validation.summary.planning'), 'value' => $rows->filter(fn (array $row): bool => str_contains($row['issue_key'], 'Plan'))->count(), 'tone' => 'warning'],
            ],
            'rows' => $rows,
        ]);
    }

    public function imports(): View
    {
        return view('operations.imports', [
            'imports' => ExternalImportRun::query()->latest('last_synced_at')->get(),
            'latestImportAt' => ExternalImportRun::query()->max('last_synced_at'),
            'recordsCount' => ExternalImportRun::query()->sum('records_count'),
            'warningsCount' => ExternalImportRun::query()->sum('warnings_count'),
        ]);
    }

    public function resources(): View
    {
        return view('operations.resources', [
            'resources' => SupportResource::query()->where('is_published', true)->orderBy('category')->orderBy('title')->get(),
            'activityLogs' => ActivityLog::query()->with('clientProject')->latest()->take(12)->get(),
        ]);
    }

    private function validationRows()
    {
        return ClientProject::query()
            ->with(['serviceLine', 'owner', 'latestDeliveryPlan', 'budget.lines'])
            ->get()
            ->map(function (ClientProject $project): array {
                $plan = $project->latestDeliveryPlan;
                $budget = $project->budget;
                $plannedHours = (float) ($plan?->planned_hours ?? 0);
                $allocatedHours = (float) ($plan?->allocated_hours ?? 0);
                $gap = round($plannedHours - $allocatedHours, 2);

                $issue = match (true) {
                    $plan === null => 'Plan missing',
                    abs($gap) > 0.01 => 'Plan hours mismatch',
                    $plan->status === 'ready_for_validation' && $budget === null => 'Budget missing',
                    $budget !== null && ($budget->status === 'draft' || $budget->lines->isEmpty()) => 'Budget incomplete',
                    $plan->status === 'ready_for_validation' => 'Validation pending',
                    default => 'Monitor',
                };

                $tone = match ($issue) {
                    'Plan missing', 'Budget missing' => 'danger',
                    'Plan hours mismatch', 'Budget incomplete', 'Validation pending' => 'warning',
                    default => 'info',
                };

                return [
                    'project' => $project,
                    'owner' => $project->owner?->name ?? __('portfolio.dashboard.unassigned'),
                    'issue_key' => $issue,
                    'issue' => __('portfolio.validation.issues.'.$issue),
                    'tone' => $tone,
                    'planned_hours' => $plannedHours,
                    'allocated_hours' => $allocatedHours,
                    'gap' => $gap,
                    'budget_status' => $budget?->status ?? 'missing',
                    'can_validate' => $plan?->status === 'ready_for_validation' && abs($gap) < 0.01,
                ];
            })
            ->filter(fn (array $row): bool => $row['issue'] !== 'Monitor')
            ->sortBy([
                fn (array $row): int => match ($row['tone']) {
                    'danger' => 1,
                    'warning' => 2,
                    default => 3,
                },
                fn (array $row): string => (string) $row['project']->target_delivery_on,
            ])
            ->values();
    }
}
