<x-layouts.app :title="'Accès refusé | ' . config('app.name')">
    <div class="mx-auto flex min-h-[62vh] w-full max-w-3xl items-center justify-center">
        <section class="w-full rounded-3xl border border-rose-200 bg-white p-8 text-center shadow-sm shadow-rose-100/80">
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-rose-600">Accès refusé</p>
            <h1 class="mt-4 text-3xl font-semibold text-slate-950">Vous n'avez pas accès à cette page.</h1>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                {{ $exception->getMessage() ?: "Votre compte Microsoft est authentifié, mais il n'est pas autorisé pour cette section." }}
            </p>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a
                    href="{{ route('dashboard') }}"
                    class="rounded-2xl bg-orange-500 px-5 py-3 text-sm font-semibold text-white transition hover:bg-orange-600"
                >
                    Retour au portail
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    >
                        Changer de compte
                    </button>
                </form>
            </div>
        </section>
    </div>
</x-layouts.app>
