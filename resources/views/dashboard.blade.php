@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'Quickies — a fast, free collection of everyday browser utilities for images, text, security and developers.')

@section('content')
    {{-- Hero --}}
    <section class="mb-10 text-center sm:mb-14">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-slate-300 backdrop-blur">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            {{ count($quickies) }} tools · 100% in your browser
        </span>
        <h1 class="animate-gradient mt-6 bg-gradient-to-r from-fuchsia-400 via-indigo-400 to-cyan-400 bg-clip-text text-5xl font-bold tracking-tight text-transparent sm:text-7xl">
            Quickies
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-base text-slate-400 sm:text-lg">
            A growing toolbox of fast, free and privacy-friendly utilities. No uploads, no sign-ups — everything runs locally on your device.
        </p>

        {{-- Search --}}
        <div class="mx-auto mt-8 max-w-md">
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input id="toolSearch" type="search" autocomplete="off" placeholder="Search tools…"
                       class="w-full rounded-2xl border border-white/10 bg-white/5 py-3.5 pl-12 pr-4 text-sm text-white placeholder-slate-500 backdrop-blur-xl transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
            </div>
        </div>
    </section>

    {{-- Tools grid --}}
    <section>
        <div id="toolsGrid" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($quickies as $i => $tool)
                <a href="{{ $tool['href'] }}"
                   data-tool-card
                   data-name="{{ strtolower($tool['name']) }}"
                   data-category="{{ strtolower($tool['category']) }}"
                   data-keywords="{{ strtolower($tool['description']) }}"
                   class="group relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl transition-all duration-300 hover:-translate-y-1 hover:border-white/25 hover:bg-white/10 rise-in"
                   style="animation-delay: {{ $i * 45 }}ms">
                    <div class="pointer-events-none absolute -right-16 -top-16 h-40 w-40 rounded-full bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} opacity-0 blur-2xl transition-opacity duration-300 group-hover:opacity-25"></div>
                    <div class="relative flex items-start gap-4">
                        <span class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} shadow-lg transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tool['icon'] }}"></path></svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-bold text-white">{{ $tool['name'] }}</h3>
                            <span class="mt-1 inline-block rounded-full border border-white/10 bg-white/5 px-2.5 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $tool['category'] }}</span>
                        </div>
                        <svg class="h-5 w-5 flex-shrink-0 text-slate-500 transition-all duration-300 group-hover:translate-x-1 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </div>
                    <p class="relative mt-4 text-sm leading-relaxed text-slate-400">{{ $tool['description'] }}</p>
                </a>
            @endforeach
        </div>

        <div id="noResults" class="hidden py-16 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="mt-4 text-slate-400">No tools match your search.</p>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    (function () {
        const search = document.getElementById('toolSearch');
        const cards = Array.from(document.querySelectorAll('[data-tool-card]'));
        const noResults = document.getElementById('noResults');
        if (!search) return;
        search.addEventListener('input', () => {
            const q = search.value.trim().toLowerCase();
            let visible = 0;
            cards.forEach((card) => {
                const haystack = `${card.dataset.name} ${card.dataset.category} ${card.dataset.keywords}`;
                const match = !q || haystack.includes(q);
                card.classList.toggle('hidden', !match);
                if (match) visible++;
            });
            noResults.classList.toggle('hidden', visible !== 0);
        });
    })();
</script>
@endpush
