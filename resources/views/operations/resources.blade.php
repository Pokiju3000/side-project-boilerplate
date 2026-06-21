<x-layouts.app :title="'Resources | ' . config('app.name')">
    <div class="mx-auto w-full max-w-7xl space-y-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#2f58e8]">Knowledge base</p>
            <h1 class="mt-2 text-3xl font-semibold text-[#16324f]">Resources and activity history</h1>
        </div>

        <section class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Support resources</p>
                <div class="mt-4 space-y-3">
                    @foreach ($resources as $resource)
                        <article class="rounded-lg border border-[#d8e4f1] p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#2f58e8]">{{ $resource->category }}</p>
                            <h2 class="mt-2 font-semibold text-[#16324f]">{{ $resource->title }}</h2>
                            <p class="mt-2 text-sm leading-6 text-[#5f7083]">{{ $resource->summary }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg border border-[#d8e4f1] bg-white p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#6b7c90]">Activity history</p>
                <div class="mt-4 space-y-4">
                    @foreach ($activityLogs as $log)
                        <article class="border-l-2 border-[#bdd0ea] pl-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-[#16324f]">{{ $log->title }}</p>
                                    <p class="mt-1 text-sm text-[#5f7083]">{{ $log->description }}</p>
                                </div>
                                <span class="text-xs text-[#6b7c90]">{{ $log->created_at?->format('M d, H:i') }}</span>
                            </div>
                            <p class="mt-2 text-xs uppercase tracking-[0.14em] text-[#6b7c90]">{{ $log->category }} - {{ $log->clientProject?->reference }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-layouts.app>
