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
    <body class="portal-page portal-scope-starter min-h-screen bg-[#f3f7fc] text-[#16324f] antialiased">
        <div class="min-h-screen">
            <header class="sticky top-0 z-40 border-b border-[#d8e4f1] bg-[#f3f7fc]/95 backdrop-blur">
                <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex min-w-0 items-center justify-between gap-3">
                            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#2f58e8] text-sm font-bold text-white">
                                    DO
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-[#16324f]">{{ config('app.name', 'DeliveryOps') }}</p>
                                    <p class="truncate text-xs text-[#6b7c90]">Portfolio-safe operations platform</p>
                                </div>
                            </a>

                            @auth
                                <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                                    @csrf
                                    <button type="submit" class="rounded-lg border border-[#bdd0ea] bg-white px-3 py-2 text-sm font-semibold text-[#244566]">
                                        Logout
                                    </button>
                                </form>
                            @endauth
                        </div>

                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                            @auth
                                <nav class="flex flex-wrap gap-2">
                                    @foreach ([
                                        ['Dashboard', 'dashboard'],
                                        ['Projects', 'projects.index'],
                                        ['Validation', 'operations.validation'],
                                        ['Imports', 'operations.imports'],
                                        ['Resources', 'operations.resources'],
                                        ['Diagnostic', 'admin.diagnostics'],
                                    ] as [$label, $route])
                                        <a
                                            href="{{ route($route) }}"
                                            class="rounded-lg px-3 py-2 text-sm font-medium transition {{ request()->routeIs($route) || ($route === 'projects.index' && request()->routeIs('projects.*')) ? 'bg-[#eef4ff] text-[#2f58e8]' : 'text-[#5f7083] hover:bg-white hover:text-[#16324f]' }}"
                                        >
                                            {{ $label }}
                                        </a>
                                    @endforeach
                                </nav>

                                <div class="hidden items-center gap-3 lg:flex">
                                    <span class="rounded-full bg-[#eef4ff] px-4 py-2 text-sm font-medium text-[#2f58e8]">
                                        {{ auth()->user()->name }}
                                        @if (session('demo_access'))
                                            <span class="ml-2 text-xs uppercase tracking-[0.14em]">Demo</span>
                                        @endif
                                    </span>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="rounded-lg border border-[#bdd0ea] bg-white px-4 py-2 text-sm font-semibold text-[#244566] transition hover:bg-[#f8fbff]">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('azure.login') }}" class="rounded-lg bg-[#2f58e8] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#284dca]">
                                    Sign in
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="space-y-4">
                    @if (session('status'))
                        <div class="portal-status-flash rounded-lg border px-5 py-4 text-sm">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
