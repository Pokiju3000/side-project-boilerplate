<x-layouts.app :title="__('portfolio.nav.imports') . ' | ' . __('portfolio.app_name')">
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#2f58e8]">{{ __('portfolio.imports.eyebrow') }}</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#16324f]">{{ __('portfolio.imports.title') }}</h1>
            <p class="mt-2 max-w-3xl text-sm leading-6 text-[#5f7083]">
                {{ __('portfolio.imports.intro') }}
            </p>
        </div>

        <section class="grid gap-4 md:grid-cols-3">
            <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">{{ __('portfolio.imports.latest') }}</p>
                <p class="mt-3 text-xl font-semibold text-[#16324f]">{{ $latestImportAt ? \Illuminate\Support\Carbon::parse($latestImportAt)->translatedFormat('d M, H:i') : __('portfolio.imports.not_available') }}</p>
            </article>
            <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">{{ __('portfolio.imports.records') }}</p>
                <p class="mt-3 text-xl font-semibold text-[#16324f]">{{ number_format($recordsCount) }}</p>
            </article>
            <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">{{ __('portfolio.imports.warnings') }}</p>
                <p class="mt-3 text-xl font-semibold text-[#8a5a00]">{{ $warningsCount }}</p>
            </article>
        </section>

        <section class="grid gap-4">
            @foreach ($imports as $import)
                <article class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">{{ $import->source }}</p>
                            <h2 class="mt-2 text-xl font-semibold text-[#16324f]">{{ portfolio_text('records.imports.'.$import->entity, $import->entity) }}</h2>
                            <p class="mt-2 text-sm text-[#5f7083]">{{ portfolio_text('records.imports.'.$import->notes, $import->notes) }}</p>
                        </div>
                        <div class="grid gap-2 text-sm sm:grid-cols-3 md:min-w-[25rem]">
                            <span class="rounded-lg bg-[#f3f7fc] px-3 py-2 text-[#244566]">{{ number_format($import->records_count) }} {{ __('portfolio.imports.record_count') }}</span>
                            <span class="rounded-lg bg-[#f3f7fc] px-3 py-2 text-[#244566]">{{ $import->warnings_count }} {{ __('portfolio.imports.warning_count') }}</span>
                            <span class="rounded-lg px-3 py-2 font-semibold {{ $import->status === 'success' ? 'bg-[#eefaf4] text-[#236748]' : 'bg-[#fff8e8] text-[#8a5a00]' }}">{{ __('portfolio.status.'.$import->status) }}</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    </div>
</x-layouts.app>
