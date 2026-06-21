<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_lines', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('market_segment')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_line_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedInteger('default_duration_weeks')->default(8);
            $table->decimal('target_margin_percent', 5, 2)->default(30);
            $table->timestamps();
        });

        Schema::create('template_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_template_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->string('phase');
            $table->decimal('planned_hours', 8, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('client_projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_template_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference')->unique();
            $table->string('client_name');
            $table->string('name');
            $table->string('status')->default('intake');
            $table->date('starts_on')->nullable();
            $table->date('target_delivery_on')->nullable();
            $table->decimal('contract_value', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('delivery_plans', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default('draft');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->decimal('planned_hours', 8, 2)->default(0);
            $table->decimal('allocated_hours', 8, 2)->default(0);
            $table->timestamp('validated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_weeks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_plan_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('week_number');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('kind')->default('delivery');
            $table->string('label')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_matrix_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_activity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_week_id')->constrained()->cascadeOnDelete();
            $table->decimal('hours', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['template_activity_id', 'delivery_week_id']);
        });

        Schema::create('project_budgets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_project_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('draft');
            $table->decimal('revenue_amount', 12, 2)->default(0);
            $table->decimal('cost_amount', 12, 2)->default(0);
            $table->decimal('margin_amount', 12, 2)->default(0);
            $table->decimal('margin_percent', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('budget_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_budget_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->string('label');
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('external_import_runs', function (Blueprint $table): void {
            $table->id();
            $table->string('source');
            $table->string('entity');
            $table->string('status')->default('success');
            $table->unsignedInteger('records_count')->default(0);
            $table->unsignedInteger('warnings_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category');
            $table->string('title');
            $table->string('tone')->default('info');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['client_project_id', 'title']);
        });

        Schema::create('support_resources', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->unique();
            $table->string('category');
            $table->string('url')->nullable();
            $table->text('summary')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        Schema::create('service_line_owners', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('service_line_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('owner');
            $table->timestamps();

            $table->unique(['service_line_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_line_owners');
        Schema::dropIfExists('support_resources');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('external_import_runs');
        Schema::dropIfExists('budget_lines');
        Schema::dropIfExists('project_budgets');
        Schema::dropIfExists('delivery_matrix_entries');
        Schema::dropIfExists('delivery_weeks');
        Schema::dropIfExists('delivery_plans');
        Schema::dropIfExists('client_projects');
        Schema::dropIfExists('template_activities');
        Schema::dropIfExists('delivery_templates');
        Schema::dropIfExists('service_lines');
    }
};
