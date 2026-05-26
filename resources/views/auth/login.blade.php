<x-layouts.guest :title="'Connexion | ' . config('app.name')">
    <main class="min-h-screen bg-[#fcf9f6] px-4 py-6 text-[#4c301e] sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-6xl items-center">
            <section class="grid w-full overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-[0_24px_80px_-50px_rgba(124,58,16,0.35)] lg:grid-cols-[1.05fr_0.95fr]">
                <div class="bg-[#fff7ed] p-7 sm:p-10 lg:p-12">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#c25e2e] sm:text-sm">
                        {{ config('app.name', 'Side Project') }}
                    </p>

                    <h1 class="mt-5 max-w-xl text-3xl font-semibold leading-tight text-[#4c301e] sm:text-4xl lg:text-5xl">
                        Un starter propre pour tes outils internes.
                    </h1>

                    <p class="mt-5 max-w-lg text-sm leading-7 text-[#6f5b4e] sm:text-base">
                        Authentification Azure, groupes d'accès, dashboard, worker de queue, scheduler et diagnostic admin,
                        prêts à être adaptés à ton prochain projet.
                    </p>

                    <div class="mt-8 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl border border-orange-100 bg-white/75 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-orange-500">Auth</p>
                            <p class="mt-3 text-sm leading-6 text-[#4c301e]">
                                Connexion Microsoft et contrôle par groupes.
                            </p>
                        </div>

                        <div class="rounded-2xl border border-orange-100 bg-white/75 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-orange-500">Ops</p>
                            <p class="mt-3 text-sm leading-6 text-[#4c301e]">
                                Queue, scheduler et diagnostic intégrés.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col justify-center p-7 sm:p-10 lg:p-12">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-orange-500 sm:text-sm">
                            Connexion
                        </p>

                        <h2 class="mt-4 text-3xl font-semibold leading-tight text-[#4c301e] sm:text-4xl">
                            Azure Active Directory
                        </h2>

                        <p class="mt-4 max-w-md text-sm leading-7 text-[#6f5b4e]">
                            Connecte-toi avec ton compte Microsoft autorisé pour accéder au portail.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a
                        href="{{ route('azure.login') }}"
                        class="mt-8 inline-flex w-full max-w-sm items-center justify-center rounded-2xl bg-orange-500 px-5 py-4 text-center text-sm font-semibold leading-5 text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600"
                    >
                        Se connecter avec Microsoft
                    </a>

                    @if (config('services.demo_access.enabled'))
                        <form method="POST" action="{{ route('demo.login') }}" class="mt-3 w-full max-w-sm">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-2xl border border-orange-200 bg-white px-5 py-4 text-center text-sm font-semibold leading-5 text-orange-600 transition hover:bg-orange-50"
                            >
                                Explorer en mode démo
                            </button>
                        </form>
                    @endif

                    <p class="mt-5 max-w-sm text-xs leading-5 text-[#8a7567]">
                        Les accès sont validés avec les groupes Azure configurés dans l'environnement.
                        @if (config('services.demo_access.enabled'))
                            Le mode démo est actif pour simuler une session sans Azure.
                        @endif
                    </p>
                </div>
            </section>
        </div>
    </main>
</x-layouts.guest>