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
                ['label' => 'Active projects', 'value' => $projects->count(), 'detail' => 'Client mandates tracked end to end.'],
                ['label' => 'Plans to validate', 'value' => $validationRows->where('can_validate', true)->count(), 'detail' => 'Delivery plans waiting at a quality gate.'],
                ['label' => 'Budget gaps', 'value' => $validationRows->filter(fn (array $row): bool => str_contains($row['issue'], 'Budget'))->count(), 'detail' => 'Projects ready for financial review.'],
                ['label' => 'Import warnings', 'value' => ExternalImportRun::query()->sum('warnings_count'), 'detail' => 'External records needing attention.'],
            ],
            'projectStates' => [
                ['label' => 'Intake', 'value' => $projects->where('status', 'intake')->count()],
                ['label' => 'Planning', 'value' => $projects->where('status', 'planning')->count()],
                ['label' => 'Ready for validation', 'value' => $projects->where('status', 'ready_for_validation')->count()],
                ['label' => 'Validated', 'value' => $projects->where('status', 'validated')->count()],
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
                ['label' => 'Ready for validation', 'value' => $rows->where('can_validate', true)->count(), 'tone' => 'info'],
                ['label' => 'Blocked projects', 'value' => $rows->where('tone', 'danger')->count(), 'tone' => 'danger'],
                ['label' => 'Budget issues', 'value' => $rows->filter(fn (array $row): bool => str_contains($row['issue'], 'Budget'))->count(), 'tone' => 'warning'],
                ['label' => 'Planning gaps', 'value' => $rows->filter(fn (array $row): bool => str_contains($row['issue'], 'Plan'))->count(), 'tone' => 'warning'],
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
                    'owner' => $project->owner?->name ?? 'Unassigned',
                    'issue' => $issue,
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
