@php($currentLocale = app()->getLocale())

<div class="flex items-center gap-1 rounded-lg border border-[#d8e4f1] bg-white p-1 text-xs font-semibold text-[#5f7083]" aria-label="{{ __('portfolio-locale::ui.language') }}">
    @foreach (config('portfolio-locale.locales', []) as $locale => $label)
        <a
            href="{{ route('locale.switch', $locale) }}"
            class="rounded-md px-2.5 py-1.5 transition {{ $currentLocale === $locale ? 'bg-[#eef4ff] text-[#2f58e8]' : 'hover:bg-[#f8fbff] hover:text-[#16324f]' }}"
            lang="{{ $locale }}"
        >
            {{ strtoupper($locale) }}
        </a>
    @endforeach
</div>
