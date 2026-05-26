<x-layouts.app :title="'Diagnostic | ' . config('app.name')">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-600">Administration</p>
                <h1 class="mt-2 text-3xl font-semibold text-slate-950">Santé et diagnostic</h1>
            </div>

            <a
                href="{{ route('admin.diagnostics') }}"
                class="inline-flex items-center justify-center rounded-2xl bg-orange-50 px-4 py-3 text-sm font-semibold text-orange-600 transition hover:bg-orange-100"
            >
                Rafraîchir
            </a>
        </div>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($checks as $key => $check)
                @php
                    $classes = match ($check['status']) {
                        'up' => [
                            'card' => 'border-emerald-200 bg-emerald-50/60',
                            'pill' => 'bg-emerald-100 text-emerald-700',
                            'label' => 'OK',
                        ],
                        'warning' => [
                            'card' => 'border-amber-200 bg-amber-50/60',
                            'pill' => 'bg-amber-100 text-amber-700',
                            'label' => 'Attention',
                        ],
                        default => [
                            'card' => 'border-rose-200 bg-rose-50/60',
                            'pill' => 'bg-rose-100 text-rose-700',
                            'label' => 'Erreur',
                        ],
                    };

                    $titles = [
                        'php' => 'PHP',
                        'database' => 'Base de données',
                        'cache' => 'Cache',
                        'queue' => 'Worker de queue',
                        'scheduler' => 'Scheduler',
                    ];
                @endphp

                <article class="rounded-3xl border p-5 shadow-sm shadow-slate-200/60 {{ $classes['card'] }}">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">{{ $titles[$key] ?? $key }}</p>
                            <h2 class="mt-3 text-lg font-semibold text-slate-950">{{ $check['label'] }}</h2>
                        </div>

                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.14em] {{ $classes['pill'] }}">
                            {{ $classes['label'] }}
                        </span>
                    </div>

                    <p class="mt-4 break-words text-sm leading-6 text-slate-600">{{ $check['detail'] }}</p>
                </article>
            @endforeach
        </section>
    </div>
</x-layouts.app>