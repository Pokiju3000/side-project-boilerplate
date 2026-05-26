<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="portal-page portal-scope-starter min-h-screen bg-[#fcf9f6] text-[#4c301e] antialiased">
        <div class="min-h-screen">
            <header class="sticky top-0 z-40 bg-[#fcf9f6]/92 backdrop-blur">
                <div class="mx-auto max-w-7xl px-4 pb-3 pt-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between gap-4 rounded-[1.6rem] border border-white/70 bg-white/78 px-4 py-3 shadow-[0_12px_30px_-24px_rgba(124,58,16,0.15)] backdrop-blur sm:px-5">
                        <div class="flex min-w-0 items-center gap-3">
                            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#fff2e6] text-sm font-bold text-[#f97316]">
                                    SP
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-[#4c301e]">{{ config('app.name', 'Side Project') }}</p>
                                    <p class="truncate text-xs text-[#6f5b4e]">Internal app starter</p>
                                </div>
                            </a>
                        </div>

                        <div class="flex items-center gap-3">
                            @auth
                                <nav class="hidden items-center gap-2 lg:flex">
                                    <a
                                        href="{{ route('dashboard') }}"
                                        class="rounded-xl px-4 py-2 text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-[#fff2e6] text-[#f97316]' : 'text-[#6f5b4e] hover:bg-[#fffaf5] hover:text-[#4c301e]' }}"
                                    >
                                        Dashboard
                                    </a>
                                    <a
                                        href="{{ route('admin.diagnostics') }}"
                                        class="rounded-xl px-4 py-2 text-sm font-medium transition {{ request()->routeIs('admin.diagnostics') ? 'bg-[#fff2e6] text-[#f97316]' : 'text-[#6f5b4e] hover:bg-[#fffaf5] hover:text-[#4c301e]' }}"
                                    >
                                        Diagnostic
                                    </a>
                                </nav>

                                <span class="portal-user-pill hidden rounded-full border border-white/80 bg-[#fffaf5] px-4 py-2 text-sm text-orange-500 font-medium shadow-sm shadow-orange-900/5 sm:inline-flex">
                                    {{ auth()->user()->name }}
                                    @if (session('demo_access'))
                                        <span class="ml-2 rounded-full bg-white/70 px-2 py-0.5 text-xs uppercase tracking-[0.14em]">
                                            Demo
                                        </span>
                                    @endif
                                </span>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="rounded-xl border border-[#f3e0cf] bg-white px-4 py-2 text-sm font-semibold text-[#4c301e] transition hover:bg-[#fffaf5]"
                                    >
                                        Déconnexion
                                    </button>
                                </form>
                            @else
                                <a
                                    href="{{ route('azure.login') }}"
                                    class="rounded-xl bg-[#f97316] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#ea580c]"
                                >
                                    Connexion Azure
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 pb-6 pt-2 sm:px-6 sm:pb-8 sm:pt-3 lg:px-8 lg:pb-10">
                <div class="space-y-4">
                    @if (session('status'))
                        <div class="portal-status-flash rounded-[1.35rem] border border-[#f3e0cf] bg-white px-5 py-4 text-sm text-[#4c301e] shadow-[0_12px_30px_-24px_rgba(124,58,16,0.12)]">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
