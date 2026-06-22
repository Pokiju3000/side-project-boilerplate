<x-layouts.guest :title="'Sign in | ' . config('app.name')">
    <main class="min-h-screen bg-[#f3f7fc] px-4 py-6 text-[#16324f] sm:px-6 lg:px-8">
        <div class="mx-auto flex min-h-[calc(100vh-3rem)] w-full max-w-6xl items-center">
            <section class="grid w-full overflow-hidden rounded-lg border border-[#d8e4f1] bg-white shadow-[0_24px_80px_-56px_rgba(47,88,232,0.35)] lg:grid-cols-[1.05fr_0.95fr]">
                <div class="bg-[#eef4ff] p-7 sm:p-10 lg:p-12">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#2f58e8] sm:text-sm">
                        {{ __('portfolio.app_name') }}
                    </p>

                    <h1 class="mt-5 max-w-xl text-3xl font-semibold leading-tight text-[#16324f] sm:text-4xl lg:text-5xl">
                        {{ __('portfolio.login.hero') }}
                    </h1>

                    <p class="mt-5 max-w-lg text-sm leading-7 text-[#5f7083] sm:text-base">
                        {{ __('portfolio.login.intro') }}
                    </p>

                    <div class="mt-8 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg border border-[#bdd0ea] bg-white/75 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#2f58e8]">{{ __('portfolio.login.workflow') }}</p>
                            <p class="mt-3 text-sm leading-6 text-[#244566]">
                                {{ __('portfolio.login.workflow_detail') }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-[#bdd0ea] bg-white/75 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#2f58e8]">{{ __('portfolio.login.operations') }}</p>
                            <p class="mt-3 text-sm leading-6 text-[#244566]">
                                {{ __('portfolio.login.operations_detail') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col justify-center p-7 sm:p-10 lg:p-12">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#2f58e8] sm:text-sm">
                            {{ __('portfolio.login.title') }}
                        </p>

                        <h2 class="mt-4 text-3xl font-semibold leading-tight text-[#16324f] sm:text-4xl">
                            Microsoft Azure AD
                        </h2>

                        <p class="mt-4 max-w-md text-sm leading-7 text-[#5f7083]">
                            {{ __('portfolio.login.help') }}
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="mt-6 rounded-lg border border-[#f0c4cc] bg-[#fff1f3] px-4 py-3 text-sm text-[#a33a52]">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mt-6 rounded-lg border border-[#b8e4cb] bg-[#eefaf4] px-4 py-3 text-sm text-[#236748]">
                            {{ session('status') }}
                        </div>
                    @endif

                    <a href="{{ route('azure.login') }}" class="mt-8 inline-flex w-full max-w-sm items-center justify-center rounded-lg bg-[#2f58e8] px-5 py-4 text-center text-sm font-semibold leading-5 text-white shadow-lg shadow-[#2f58e8]/20 transition hover:bg-[#284dca]">
                        {{ __('portfolio-locale::ui.sign_in_microsoft') }}
                    </a>

                    @if (config('services.demo_access.enabled'))
                        <form method="POST" action="{{ route('demo.login') }}" class="mt-3 w-full max-w-sm">
                            @csrf
                            <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg border border-[#bdd0ea] bg-white px-5 py-4 text-center text-sm font-semibold leading-5 text-[#2f58e8] transition hover:bg-[#f8fbff]">
                                {{ __('portfolio-locale::ui.explore_demo') }}
                            </button>
                        </form>
                    @endif

                    <p class="mt-5 max-w-sm text-xs leading-5 text-[#6b7c90]">
                        {{ __('portfolio.login.privacy') }}
                    </p>
                </div>
            </section>
        </div>
    </main>
</x-layouts.guest>
