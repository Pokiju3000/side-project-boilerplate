<x-layouts.app :title="__('portfolio.nav.projects') . ' | ' . __('portfolio.app_name')">
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#2f58e8]">{{ __('portfolio.projects.eyebrow') }}</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#16324f]">{{ __('portfolio.projects.title') }}</h1>
        </div>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($serviceLines as $line)
                <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">{{ $line->code }}</p>
                    <h2 class="mt-2 font-semibold text-[#16324f]">{{ portfolio_text('records.service_lines.'.$line->code, $line->name) }}</h2>
                    <p class="mt-2 text-sm text-[#5f7083]">{{ $line->client_projects_count }} {{ __('portfolio.projects.count') }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-4">
            @foreach ($projects as $project)
                <a href="{{ route('projects.show', $project) }}" class="rounded-lg border border-[#d8e4f1] bg-white p-5 transition hover:border-[#bdd0ea]">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">{{ $project->reference }} - {{ portfolio_text('records.service_lines.'.$project->serviceLine->code, $project->serviceLine->name) }}</p>
                            <h2 class="mt-2 text-xl font-semibold text-[#16324f]">{{ portfolio_text('records.projects.'.$project->reference.'.client', $project->client_name) }}: {{ portfolio_text('records.projects.'.$project->reference.'.name', $project->name) }}</h2>
                            <p class="mt-2 text-sm text-[#5f7083]">{{ __('portfolio.projects.owner') }}: {{ $project->owner?->name ?? __('portfolio.dashboard.unassigned') }}</p>
                        </div>
                        <div class="grid gap-2 text-sm text-[#244566] sm:grid-cols-3 md:min-w-[28rem]">
                            <span class="rounded-lg bg-[#f3f7fc] px-3 py-2">{{ __('portfolio.projects.status') }}: {{ __('portfolio.status.'.($project->status)) }}</span>
                            <span class="rounded-lg bg-[#f3f7fc] px-3 py-2">{{ __('portfolio.projects.plan') }}: {{ __('portfolio.status.'.($project->latestDeliveryPlan?->status ?? 'missing')) }}</span>
                            <span class="rounded-lg bg-[#f3f7fc] px-3 py-2">{{ __('portfolio.projects.budget') }}: {{ __('portfolio.status.'.($project->budget?->status ?? 'missing')) }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </section>
    </div>
</x-layouts.app>
