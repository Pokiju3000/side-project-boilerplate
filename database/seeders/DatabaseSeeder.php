<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\BudgetLine;
use App\Models\ClientProject;
use App\Models\DeliveryMatrixEntry;
use App\Models\DeliveryPlan;
use App\Models\DeliveryTemplate;
use App\Models\DeliveryWeek;
use App\Models\ExternalImportRun;
use App\Models\ProjectBudget;
use App\Models\ServiceLine;
use App\Models\SupportResource;
use App\Models\TemplateActivity;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $demoUser = User::query()->updateOrCreate(
            ['email' => 'demo@example.test'],
            [
                'name' => 'Alex Demo',
                'guid' => 'demo-user',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $advisory = ServiceLine::query()->updateOrCreate(
            ['code' => 'ADV'],
            [
                'name' => 'Advisory Programs',
                'market_segment' => 'Mid-market',
                'description' => 'Mandats de cadrage, ateliers et accompagnement operationnel.',
            ],
        );

        $implementation = ServiceLine::query()->updateOrCreate(
            ['code' => 'IMP'],
            [
                'name' => 'Implementation Services',
                'market_segment' => 'Enterprise',
                'description' => 'Livraisons structurees pour deploiements logiciels et migration de processus.',
            ],
        );

        $advisory->owners()->syncWithoutDetaching([$demoUser->id => ['role' => 'owner']]);
        $implementation->owners()->syncWithoutDetaching([$demoUser->id => ['role' => 'backup']]);

        $template = DeliveryTemplate::query()->updateOrCreate(
            ['code' => 'ONB-8W'],
            [
                'service_line_id' => $implementation->id,
                'name' => '8-week client onboarding',
                'default_duration_weeks' => 8,
                'target_margin_percent' => 34,
            ],
        );

        $activities = collect([
            ['DISC', 'Discovery workshop', 'Discovery', 18],
            ['MAP', 'Process mapping', 'Design', 24],
            ['CFG', 'Platform configuration', 'Build', 56],
            ['DATA', 'Data preparation', 'Build', 32],
            ['UAT', 'User acceptance testing', 'Validation', 28],
            ['GO', 'Go-live support', 'Launch', 22],
        ])->map(function (array $row, int $index) use ($template): TemplateActivity {
            return TemplateActivity::query()->updateOrCreate(
                ['delivery_template_id' => $template->id, 'code' => $row[0]],
                [
                    'name' => $row[1],
                    'phase' => $row[2],
                    'planned_hours' => $row[3],
                    'sort_order' => ($index + 1) * 10,
                ],
            );
        });

        $projects = collect([
            ['PRJ-2401', 'Northwind Labs', 'Customer portal rollout', 'ready_for_validation', '2026-07-06', '2026-08-28', 84500],
            ['PRJ-2402', 'Summit Retail', 'Operations workflow redesign', 'planning', '2026-07-20', '2026-09-11', 62000],
            ['PRJ-2403', 'Boreal Energy', 'ERP integration readiness', 'validated', '2026-06-08', '2026-07-31', 118000],
        ])->map(function (array $row) use ($demoUser, $implementation, $template): ClientProject {
            return ClientProject::query()->updateOrCreate(
                ['reference' => $row[0]],
                [
                    'service_line_id' => $implementation->id,
                    'delivery_template_id' => $template->id,
                    'owner_id' => $demoUser->id,
                    'client_name' => $row[1],
                    'name' => $row[2],
                    'status' => $row[3],
                    'starts_on' => $row[4],
                    'target_delivery_on' => $row[5],
                    'contract_value' => $row[6],
                ],
            );
        });

        $projects->each(function (ClientProject $project) use ($activities): void {
            $startsOn = Carbon::parse($project->starts_on);
            $plan = DeliveryPlan::query()->updateOrCreate(
                ['client_project_id' => $project->id, 'name' => 'Primary delivery plan'],
                [
                    'status' => match ($project->status) {
                        'validated' => 'validated',
                        'ready_for_validation' => 'ready_for_validation',
                        default => 'draft',
                    },
                    'starts_on' => $startsOn->toDateString(),
                    'ends_on' => $startsOn->copy()->addWeeks(8)->subDay()->toDateString(),
                    'planned_hours' => $activities->sum(fn (TemplateActivity $activity): float => (float) $activity->planned_hours),
                    'allocated_hours' => $project->reference === 'PRJ-2402' ? 142 : 180,
                    'validated_at' => $project->status === 'validated' ? now()->subDays(3) : null,
                ],
            );

            $weeks = collect(range(1, 8))->map(function (int $weekNumber) use ($plan, $startsOn): DeliveryWeek {
                $weekStart = $startsOn->copy()->addWeeks($weekNumber - 1);

                return DeliveryWeek::query()->updateOrCreate(
                    ['delivery_plan_id' => $plan->id, 'week_number' => $weekNumber],
                    [
                        'starts_on' => $weekStart->toDateString(),
                        'ends_on' => $weekStart->copy()->addDays(4)->toDateString(),
                        'kind' => $weekNumber === 5 ? 'checkpoint' : 'delivery',
                        'label' => $weekNumber === 5 ? 'Client checkpoint' : null,
                    ],
                );
            });

            $activities->each(function (TemplateActivity $activity) use ($plan, $weeks, $project): void {
                $activeWeeks = match ($activity->code) {
                    'DISC' => [1, 2],
                    'MAP' => [2, 3],
                    'CFG' => [3, 4, 5, 6],
                    'DATA' => [4, 5],
                    'UAT' => [6, 7],
                    default => [8],
                };

                foreach ($activeWeeks as $weekNumber) {
                    $week = $weeks->firstWhere('week_number', $weekNumber);
                    $baseHours = round(((float) $activity->planned_hours) / count($activeWeeks), 2);
                    $hours = $project->reference === 'PRJ-2402' && $weekNumber > 5 ? 0 : $baseHours;

                    DeliveryMatrixEntry::query()->updateOrCreate(
                        [
                            'template_activity_id' => $activity->id,
                            'delivery_week_id' => $week->id,
                        ],
                        [
                            'delivery_plan_id' => $plan->id,
                            'hours' => $hours,
                        ],
                    );
                }
            });

            if ($project->reference !== 'PRJ-2402') {
                $cost = $project->reference === 'PRJ-2403' ? 73500 : 60300;
                $budget = ProjectBudget::query()->updateOrCreate(
                    ['client_project_id' => $project->id],
                    [
                        'status' => $project->status === 'validated' ? 'approved' : 'draft',
                        'revenue_amount' => $project->contract_value,
                        'cost_amount' => $cost,
                        'margin_amount' => $project->contract_value - $cost,
                        'margin_percent' => round((($project->contract_value - $cost) / $project->contract_value) * 100, 2),
                    ],
                );

                foreach ([
                    ['Delivery labor', 'Consulting delivery', $cost * 0.72],
                    ['Project management', 'Coordination and governance', $cost * 0.18],
                    ['Tools and travel', 'Enablement expenses', $cost * 0.10],
                ] as $line) {
                    BudgetLine::query()->updateOrCreate(
                        ['project_budget_id' => $budget->id, 'label' => $line[1]],
                        ['category' => $line[0], 'amount' => $line[2]],
                    );
                }
            }
        });

        foreach ([
            ['CRM', 'Client projects', 'success', 38, 0, 'Pipeline and client ownership imported.'],
            ['ERP', 'Financial accounts', 'warning', 214, 3, 'Three account mappings need review.'],
            ['Timesheets', 'Actual delivery hours', 'success', 1264, 0, 'Weekly actuals refreshed for active projects.'],
        ] as $index => $run) {
            ExternalImportRun::query()->updateOrCreate(
                ['source' => $run[0], 'entity' => $run[1]],
                [
                    'status' => $run[2],
                    'records_count' => $run[3],
                    'warnings_count' => $run[4],
                    'started_at' => now()->subHours($index + 2),
                    'finished_at' => now()->subHours($index + 2)->addMinutes(8),
                    'last_synced_at' => now()->subHours($index + 2)->addMinutes(8),
                    'notes' => $run[5],
                ],
            );
        }

        SupportResource::query()->upsert([
            ['title' => 'Delivery plan review checklist', 'category' => 'Validation', 'url' => null, 'summary' => 'Questions a verifier avant de valider un plan client.', 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Budget margin playbook', 'category' => 'Finance', 'url' => null, 'summary' => 'Repere les marges basses et les lignes de cout a reviser.', 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Import troubleshooting guide', 'category' => 'Operations', 'url' => null, 'summary' => 'Services et journaux a verifier quand une synchronisation bloque.', 'is_published' => true, 'created_at' => now(), 'updated_at' => now()],
        ], ['title'], ['category', 'url', 'summary', 'is_published', 'updated_at']);

        ActivityLog::query()->upsert([
            ['user_id' => $demoUser->id, 'client_project_id' => $projects[0]->id, 'category' => 'validation', 'title' => 'Plan submitted for validation', 'tone' => 'info', 'description' => 'The delivery plan reached the review gate.', 'metadata' => json_encode(['status' => 'ready_for_validation']), 'created_at' => now()->subHours(3), 'updated_at' => now()->subHours(3)],
            ['user_id' => $demoUser->id, 'client_project_id' => $projects[1]->id, 'category' => 'planning', 'title' => 'Matrix still incomplete', 'tone' => 'warning', 'description' => 'Several late delivery weeks have no allocated work.', 'metadata' => json_encode(['missing_weeks' => [6, 7, 8]]), 'created_at' => now()->subHours(6), 'updated_at' => now()->subHours(6)],
            ['user_id' => $demoUser->id, 'client_project_id' => $projects[2]->id, 'category' => 'budget', 'title' => 'Budget approved', 'tone' => 'success', 'description' => 'Margin and cost lines were approved after final review.', 'metadata' => json_encode(['margin' => 37.71]), 'created_at' => now()->subDay(), 'updated_at' => now()->subDay()],
        ], ['title', 'client_project_id'], ['user_id', 'category', 'tone', 'description', 'metadata', 'updated_at']);
    }
}
