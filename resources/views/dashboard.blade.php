<x-layouts.app :title="'Dashboard | ' . config('app.name')">
    <div class="mx-auto w-full max-w-6xl space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-orange-600">Workspace</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-950">Dashboard</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                Un point de départ sobre pour une application interne: authentification Azure, navigation, queue worker,
                scheduler et page diagnostic sont déjà en place.
            </p>
        </div>

        <section class="grid gap-4 md:grid-cols-3">
            <article class="rounded-3xl border border-white/70 bg-white p-5 shadow-sm shadow-orange-200/40">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Auth</p>
                <h2 class="mt-3 text-lg font-semibold text-slate-950">Azure AD</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Accès protégé par groupes configurables.</p>
            </article>

            <article class="rounded-3xl border border-white/70 bg-white p-5 shadow-sm shadow-orange-200/40">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Jobs</p>
                <h2 class="mt-3 text-lg font-semibold text-slate-950">Queue worker</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Un heartbeat permet de confirmer que le worker est actif.</p>
            </article>

            <article class="rounded-3xl border border-white/70 bg-white p-5 shadow-sm shadow-orange-200/40">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Ops</p>
                <h2 class="mt-3 text-lg font-semibold text-slate-950">Diagnostic</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Une page admin vérifie les dépendances de base.</p>
            </article>
        </section>
    </div>
</x-layouts.app>
