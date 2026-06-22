<x-layouts.app :title="__('portfolio.nav.validation') . ' | ' . __('portfolio.app_name')">
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#2f58e8]">{{ __('portfolio.validation.eyebrow') }}</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#16324f]">{{ __('portfolio.validation.title') }}</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-[#5f7083]">
                {{ __('portfolio.validation.intro') }}
            </p>
        </div>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($summaryCards as $card)
                <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">{{ $card['label'] }}</p>
                    <p class="mt-3 text-3xl font-semibold {{ $card['tone'] === 'danger' ? 'text-[#a33a52]' : ($card['tone'] === 'warning' ? 'text-[#8a5a00]' : 'text-[#2f58e8]') }}">{{ $card['value'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="rounded-lg border border-[#d8e4f1] bg-white p-5">
            <div class="overflow-hidden rounded-lg border border-[#d8e4f1]">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#f3f7fc] text-xs uppercase tracking-[0.14em] text-[#6b7c90]">
                        <tr>
                            <th class="px-4 py-3">{{ __('portfolio.validation.project') }}</th>
                            <th class="px-4 py-3">{{ __('portfolio.validation.owner') }}</th>
                            <th class="px-4 py-3">{{ __('portfolio.validation.issue') }}</th>
                            <th class="px-4 py-3">{{ __('portfolio.validation.hours') }}</th>
                            <th class="px-4 py-3">{{ __('portfolio.validation.budget') }}</th>
                            <th class="px-4 py-3">{{ __('portfolio.validation.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#d8e4f1]">
                        @foreach ($rows as $row)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-[#16324f]">{{ $row['project']->reference }}</p>
                                    <p class="text-xs text-[#6b7c90]">{{ portfolio_text('records.projects.'.$row['project']->reference.'.client', $row['project']->client_name) }}</p>
                                </td>
                                <td class="px-4 py-3 text-[#244566]">{{ $row['owner'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $row['tone'] === 'danger' ? 'bg-[#fff1f3] text-[#a33a52]' : 'bg-[#fff8e8] text-[#8a5a00]' }}">{{ $row['issue'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-[#244566]">{{ $row['allocated_hours'] }} / {{ $row['planned_hours'] }}h</td>
                                <td class="px-4 py-3 text-[#244566]">{{ __('portfolio.status.'.$row['budget_status']) }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('projects.show', $row['project']) }}" class="font-semibold text-[#2f58e8]">{{ __('portfolio.validation.open') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-layouts.app>
