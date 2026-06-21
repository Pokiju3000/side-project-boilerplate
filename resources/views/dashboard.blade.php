<x-layouts.app :title="'Dashboard | ' . config('app.name')">
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <section class="grid gap-6 lg:grid-cols-[1.5fr_0.9fr]">
            <div class="space-y-3">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#2f58e8]">Delivery operations</p>
                <h1 class="text-3xl font-semibold text-[#16324f]">Client delivery control room</h1>
                <p class="max-w-3xl text-sm leading-6 text-[#5f7083]">
                    A portfolio-safe Laravel demo showing the process engine behind complex internal operations: project intake,
                    planning matrix, validation gates, budget readiness, external imports and audit history.
                </p>
            </div>

            <div class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Process flow</p>
                <div class="mt-4 grid grid-cols-2 gap-2 text-sm text-[#244566]">
                    <span>1. Intake</span>
                    <span>2. Planning</span>
                    <span>3. Validation</span>
                    <span>4. Budget</span>
                    <span>5. Imports</span>
                    <span>6. Audit</span>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($metrics as $metric)
                <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">{{ $metric['label'] }}</p>
                    <p class="mt-3 text-3xl font-semibold text-[#16324f]">{{ $metric['value'] }}</p>
                    <p class="mt-2 text-sm leading-6 text-[#5f7083]">{{ $metric['detail'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <div class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Priority queue</p>
                        <h2 class="mt-2 text-xl font-semibold text-[#16324f]">Projects needing attention</h2>
                    </div>
                    <a href="{{ route('projects.index') }}" class="text-sm font-semibold text-[#2f58e8]">View all</a>
                </div>

                <div class="mt-5 overflow-hidden rounded-lg border border-[#d8e4f1]">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-[#f3f7fc] text-xs uppercase tracking-[0.14em] text-[#6b7c90]">
                            <tr>
                                <th class="px-4 py-3">Project</th>
                                <th class="px-4 py-3">Owner</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Target</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d8e4f1]">
                            @foreach ($priorityProjects as $project)
                                <tr>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('projects.show', $project) }}" class="font-semibold text-[#16324f]">{{ $project->reference }}</a>
                                        <p class="text-xs text-[#6b7c90]">{{ $project->client_name }} - {{ $project->name }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-[#244566]">{{ $project->owner?->name ?? 'Unassigned' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-[#eef4ff] px-3 py-1 text-xs font-semibold text-[#2f58e8]">{{ str_replace('_', ' ', $project->status) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-[#244566]">{{ $project->target_delivery_on?->format('M d') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Validation center</p>
                    <div class="mt-4 space-y-3">
                        @foreach ($validationRows as $row)
                            <a href="{{ route('projects.show', $row['project']) }}" class="block rounded-lg border border-[#d8e4f1] p-4 transition hover:border-[#bdd0ea]">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-semibold text-[#16324f]">{{ $row['project']->reference }}</p>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $row['tone'] === 'danger' ? 'bg-[#fff1f3] text-[#a33a52]' : 'bg-[#fff8e8] text-[#8a5a00]' }}">{{ $row['issue'] }}</span>
                                </div>
                                <p class="mt-2 text-sm text-[#5f7083]">{{ $row['project']->client_name }} - {{ $row['owner'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Latest activity</p>
                    <div class="mt-4 space-y-3">
                        @foreach ($activityLogs as $log)
                            <div class="border-l-2 border-[#bdd0ea] pl-3">
                                <p class="text-sm font-semibold text-[#16324f]">{{ $log->title }}</p>
                                <p class="text-xs text-[#6b7c90]">{{ $log->clientProject?->reference }} - {{ $log->created_at?->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
