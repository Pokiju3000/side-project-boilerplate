<x-layouts.app :title="$project->reference . ' | ' . config('app.name')">
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#2f58e8]">{{ $project->reference }}</p>
                <h1 class="mt-2 text-3xl font-semibold text-[#16324f]">{{ $project->client_name }}: {{ $project->name }}</h1>
                <p class="mt-2 text-sm text-[#5f7083]">{{ $project->serviceLine->name }} - {{ $project->deliveryTemplate?->name }}</p>
            </div>
            <span class="w-fit rounded-full bg-[#eef4ff] px-4 py-2 text-sm font-semibold text-[#2f58e8]">{{ str_replace('_', ' ', $project->status) }}</span>
        </div>

        <section class="grid gap-4 md:grid-cols-4">
            <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Owner</p>
                <p class="mt-2 font-semibold text-[#16324f]">{{ $project->owner?->name ?? 'Unassigned' }}</p>
            </article>
            <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Target</p>
                <p class="mt-2 font-semibold text-[#16324f]">{{ $project->target_delivery_on?->format('M d, Y') }}</p>
            </article>
            <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Hours</p>
                <p class="mt-2 font-semibold text-[#16324f]">{{ $plan?->allocated_hours ?? 0 }} / {{ $plan?->planned_hours ?? 0 }}</p>
            </article>
            <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Margin</p>
                <p class="mt-2 font-semibold text-[#16324f]">{{ $project->budget?->margin_percent ?? 'n/a' }}%</p>
            </article>
        </section>

        @if ($plan)
            <section class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Planning matrix</p>
                        <h2 class="mt-2 text-xl font-semibold text-[#16324f]">{{ $plan->name }}</h2>
                    </div>
                    <span class="rounded-full bg-[#f3f7fc] px-3 py-1 text-xs font-semibold text-[#244566]">{{ str_replace('_', ' ', $plan->status) }}</span>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-[#f3f7fc] text-xs uppercase tracking-[0.14em] text-[#6b7c90]">
                            <tr>
                                <th class="sticky left-0 bg-[#f3f7fc] px-4 py-3">Activity</th>
                                @foreach ($plan->weeks as $week)
                                    <th class="px-3 py-3 text-center">W{{ $week->week_number }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#d8e4f1]">
                            @foreach ($activities as $activity)
                                <tr>
                                    <td class="sticky left-0 bg-white px-4 py-3">
                                        <p class="font-semibold text-[#16324f]">{{ $activity->name }}</p>
                                        <p class="text-xs text-[#6b7c90]">{{ $activity->phase }} - {{ $activity->planned_hours }}h</p>
                                    </td>
                                    @foreach ($plan->weeks as $week)
                                        @php $entry = $matrix->get($activity->id)?->get($week->id); @endphp
                                        <td class="px-3 py-3 text-center">
                                            <span class="inline-flex h-8 min-w-10 items-center justify-center rounded {{ (float) ($entry?->hours ?? 0) > 0 ? 'bg-[#eef4ff] text-[#2f58e8]' : 'bg-[#f3f7fc] text-[#9aa8b7]' }}">
                                                {{ (float) ($entry?->hours ?? 0) > 0 ? rtrim(rtrim((string) $entry->hours, '0'), '.') : '-' }}
                                            </span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        <section class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Budget</p>
                @if ($project->budget)
                    <p class="mt-2 text-2xl font-semibold text-[#16324f]">${{ number_format((float) $project->budget->revenue_amount) }}</p>
                    <p class="mt-1 text-sm text-[#5f7083]">Cost ${{ number_format((float) $project->budget->cost_amount) }} - Margin ${{ number_format((float) $project->budget->margin_amount) }}</p>
                    <div class="mt-4 space-y-2">
                        @foreach ($project->budget->lines as $line)
                            <div class="flex justify-between rounded-lg bg-[#f3f7fc] px-3 py-2 text-sm">
                                <span class="text-[#244566]">{{ $line->label }}</span>
                                <span class="font-semibold text-[#16324f]">${{ number_format((float) $line->amount) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="mt-3 text-sm text-[#5f7083]">No budget generated yet. This mirrors the gap surfaced in the validation center.</p>
                @endif
            </div>

            <div class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Audit trail</p>
                <div class="mt-4 space-y-3">
                    @foreach ($project->activityLogs as $log)
                        <div class="border-l-2 border-[#bdd0ea] pl-3">
                            <p class="text-sm font-semibold text-[#16324f]">{{ $log->title }}</p>
                            <p class="text-xs text-[#6b7c90]">{{ $log->description }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
