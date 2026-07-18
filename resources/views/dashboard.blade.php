@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'Quickies — a fast, free collection of everyday browser utilities for images, text, security and developers, plus a Smart Toolbox that auto-detects your input.')

@section('content')
    @php
        $freeTools = config('plans.free_tools', []);
        $trialAll = (bool) config('plans.trial_all_tools', false);
        $isPro = auth()->check() && auth()->user()->hasActiveSubscription();
        $authed = auth()->check();
        $favorites = $authed ? auth()->user()->favoriteTools() : [];
        $star = 'M11.48 3.5a.56.56 0 011.04 0l2.08 4.71 5.12.46c.5.04.7.66.32 1l-3.87 3.4 1.15 5.02c.11.49-.42.87-.85.61L12 16.5l-4.47 2.7c-.43.26-.96-.12-.85-.61l1.15-5.02-3.87-3.4c-.38-.34-.18-.96.32-1l5.12-.46 2.08-4.71z';
        $catIcons = [
            'Featured' => $star,
            'VAS & DCB' => 'M3 3v18h18M7 14l3-3 4 4 5-6',
            'Text' => 'M4 6h16M4 12h16M4 18h7',
            'Data & Convert' => 'M8 7l-4 4 4 4m8-8l4 4-4 4',
            'Developer' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
            'Web & Network' => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0z M3 12h18M12 3a15 15 0 010 18 15 15 0 010-18',
            'Date & Time' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'Generators' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
            'Security' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'Design' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343',
            'Image' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
            'Document' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
        ];
        $catFallback = 'M4 6h16M4 10h16M4 14h16M4 18h16';
    @endphp
    {{-- Greeting --}}
    @php
        $access = $authed && auth()->user()->hasActiveSubscription();
        $firstName = $authed ? \Illuminate\Support\Str::before(trim(auth()->user()->name), ' ') : null;
    @endphp
    <section class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                @auth Welcome back, {{ $firstName }} @else Your toolbox @endauth
            </h1>
            <p class="mt-1.5 text-slate-400">{{ count($quickies) }} tools ready — everything runs privately in your browser.</p>
        </div>
        @auth
            @if ($access)
                <span class="inline-flex items-center gap-2 rounded-full border border-emerald-400/25 bg-emerald-500/[0.08] px-4 py-2 text-sm font-semibold text-emerald-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ auth()->user()->isAdmin() ? 'Admin — full access' : 'Pro — full access' }}
                </span>
            @else
                <a href="{{ route('pricing') }}" class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.05] px-4 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/[0.09] hover:text-white">
                    Free plan
                    <span class="rounded-full bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-2 py-0.5 text-xs font-semibold text-white">Upgrade</span>
                </a>
            @endif
        @endauth
    </section>

    {{-- Insights --}}
    <section class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
            <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                Tools
            </div>
            <div class="mt-1.5 text-3xl font-semibold text-white">{{ count($quickies) }}</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
            <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h6a2 2 0 012 2v7a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"></path></svg>
                Categories
            </div>
            <div class="mt-1.5 text-3xl font-semibold text-white">{{ count($quickieCategories) }}</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
            <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.5a.56.56 0 011.04 0l2.08 4.71 5.12.46c.5.04.7.66.32 1l-3.87 3.4 1.15 5.02c.11.49-.42.87-.85.61L12 16.5l-4.47 2.7c-.43.26-.96-.12-.85-.61l1.15-5.02-3.87-3.4c-.38-.34-.18-.96.32-1l5.12-.46 2.08-4.71z"></path></svg>
                Favorites
            </div>
            <div class="mt-1.5 text-3xl font-semibold text-white" id="statFavs">{{ count($favorites) }}</div>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4">
            <div class="flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-slate-500">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Recently used
            </div>
            <div class="mt-1.5 text-3xl font-semibold text-white" id="statRecent">0</div>
        </div>
    </section>

    {{-- Jump back in --}}
    <section id="recentRow" class="mb-6 hidden">
        <h2 class="mb-3 text-[13px] font-semibold uppercase tracking-[0.16em] text-slate-400">Jump back in</h2>
        <div id="recentChips" class="flex flex-wrap gap-2"></div>
    </section>

    {{-- Smart paste --}}
    <section class="mb-6">
        <div id="smartDrop" class="rounded-2xl border border-white/10 bg-white/[0.05] p-5 transition">
            <div class="mb-3 flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-fuchsia-500 to-indigo-500 text-white shadow-sm">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
                <h2 class="text-lg font-semibold text-white">Smart paste</h2>
                <x-info text="Paste — or drop a file. Detects JSON, a JWT, Base64, a URL, a UUID, a timestamp, SQL or a hex colour, and reads images, PDFs and text files to suggest the right tool." />
                <span class="ml-auto inline-flex items-center gap-1.5 rounded-full border border-indigo-400/25 bg-indigo-500/10 px-2.5 py-1 text-xs font-semibold text-indigo-500">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    AI-powered detection
                </span>
            </div>
            <textarea id="smartInput" rows="3" oninput="analyzeSmart()"
                class="w-full resize-y rounded-xl border border-white/10 bg-black/40 px-4 py-3.5 font-mono text-base text-white placeholder-slate-500 transition focus:border-indigo-400/70 focus:outline-none focus:ring-2 focus:ring-indigo-500/25"
                placeholder="Paste anything, or drop a file to detect it…"></textarea>
            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                <button type="button" id="smartFileBtn" class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/[0.05] px-2.5 py-1.5 font-medium text-slate-300 transition hover:bg-white/[0.09]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    Attach file
                </button>
                <span>or drop an image, PDF, JSON or text file here</span>
                <input type="file" id="smartFile" class="hidden">
            </div>
            <div id="smartBadges" class="mt-3 flex flex-wrap gap-2 empty:hidden"></div>
            <div id="smartResult" class="mt-4 space-y-4 empty:hidden"></div>
        </div>
    </section>

    {{-- Search --}}
    <section class="mb-5">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input id="toolSearch" type="search" autocomplete="off" placeholder="Search tools by name or category…"
                       class="w-full rounded-2xl border border-white/10 bg-white/[0.05] py-3.5 pl-12 pr-4 text-base text-white placeholder-slate-400 transition focus:border-indigo-400/70 focus:outline-none focus:ring-2 focus:ring-indigo-500/25">
            </div>
            <button id="resetBtn" onclick="resetFilters()" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/[0.05] px-5 py-3.5 text-sm font-medium text-slate-300 transition hover:bg-white/[0.09]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Reset
            </button>
        </div>
    </section>

    {{-- Category tabs --}}
    <section class="mb-6 -mx-1 overflow-x-auto pb-1">
        <div class="flex items-center gap-2 px-1">
            <button type="button" data-cat="all" class="cat-tab is-active">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                All Categories
            </button>
            @foreach ($quickieCategories as $category)
                <button type="button" data-cat="{{ $category }}" class="cat-tab">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $catIcons[$category] ?? $catFallback }}"></path></svg>
                    {{ $category }}
                </button>
            @endforeach
        </div>
    </section>

    {{-- Tool grid --}}
    <section>
        <div id="toolGrid" data-auth="{{ $authed ? '1' : '0' }}" data-favs='@json($favorites)' class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($quickies as $tool)
                @php $tryable = $trialAll || in_array($tool['href'], $freeTools, true); @endphp
                <div data-tool-card
                     data-href="{{ $tool['href'] }}"
                     data-name="{{ strtolower($tool['name']) }}"
                     data-keywords="{{ strtolower($tool['description'].' '.$tool['category']) }}"
                     data-category="{{ $tool['category'] }}"
                     data-grad="{{ $tool['from'] }} {{ $tool['to'] }}"
                     class="group relative overflow-hidden rounded-2xl border border-white/10 bg-white/[0.04] p-5 transition-all duration-200 hover:-translate-y-0.5 hover:border-white/20 hover:bg-white/[0.07]">
                    <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} opacity-0 blur-2xl transition-opacity duration-300 group-hover:opacity-20"></div>
                    <button type="button" data-fav aria-label="Add to favourites" class="absolute right-2.5 top-2.5 z-10 flex h-7 w-7 items-center justify-center rounded-lg">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $star }}"></path></svg>
                    </button>
                    <a href="{{ $tool['href'] }}" class="relative flex items-center gap-4" data-card-link>
                        <span class="relative flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} text-white shadow-md transition-transform duration-200 group-hover:scale-105">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $tool['icon'] }}"></path></svg>
                            @unless ($isPro)
                                @unless ($tryable)
                                    <span class="absolute -bottom-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-gradient-to-br from-slate-600 to-slate-800 text-white ring-2 ring-white/20" title="Pro tool">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    </span>
                                @endunless
                            @endunless
                        </span>
                        <span class="min-w-0 flex-1 pr-2">
                            <span class="block text-base font-semibold text-white">{{ $tool['name'] }}</span>
                            <span class="mt-0.5 line-clamp-2 block text-xs leading-relaxed text-slate-500">{{ $tool['description'] }}</span>
                        </span>
                        <svg class="h-5 w-5 flex-shrink-0 text-slate-400 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            @endforeach
        </div>

        <div id="noResults" class="hidden py-20 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="mt-4 text-slate-500" data-empty-msg>No tools match your search.</p>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    /* ---------------- Filters: tabs, search, views, favourites, history ---------------- */
    (function () {
        const cards = Array.from(document.querySelectorAll('[data-tool-card]'));
        const search = document.getElementById('toolSearch');
        const noResults = document.getElementById('noResults');
        const emptyMsg = noResults ? noResults.querySelector('[data-empty-msg]') : null;
        const tabs = Array.from(document.querySelectorAll('[data-cat]'));
        const navs = Array.from(document.querySelectorAll('[data-nav]'));

        const FAV_KEY = 'quickies:favs', HIST_KEY = 'quickies:history';
        const read = (k) => { try { return JSON.parse(localStorage.getItem(k)) || []; } catch (e) { return []; } };
        const store = (k, v) => { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) {} };
        const grid = document.getElementById('toolGrid');
        const authed = grid && grid.dataset.auth === '1';
        const csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content;
        // Logged-in users keep favourites in the database; guests fall back to localStorage.
        let favs = new Set(authed ? JSON.parse(grid.dataset.favs || '[]') : read(FAV_KEY));
        let recent = read(HIST_KEY);
        const statFavs = document.getElementById('statFavs');
        const statRecent = document.getElementById('statRecent');
        function updateStats() {
            if (statFavs) statFavs.textContent = favs.size;
            if (statRecent) statRecent.textContent = recent.length;
        }
        function renderRecent() {
            const wrap = document.getElementById('recentRow');
            const chips = document.getElementById('recentChips');
            if (!wrap || !chips) return;
            const lookup = {};
            cards.forEach((c) => { lookup[c.dataset.href] = c; });
            const items = recent.map((h) => lookup[h]).filter(Boolean).slice(0, 8);
            if (!items.length) { wrap.classList.add('hidden'); return; }
            wrap.classList.remove('hidden');
            chips.innerHTML = items.map((c) => {
                const nameEl = c.querySelector('[data-card-link] .font-semibold');
                const name = (nameEl ? nameEl.textContent : c.dataset.name).trim();
                return `<a href="${c.dataset.href}" class="group flex items-center gap-2.5 rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2 transition hover:-translate-y-0.5 hover:border-white/20 hover:bg-white/[0.07]">
                    <span class="h-6 w-6 flex-shrink-0 rounded-md bg-gradient-to-br ${c.dataset.grad || 'from-slate-500 to-slate-600'}"></span>
                    <span class="text-sm font-semibold text-white">${name}</span>
                </a>`;
            }).join('');
        }

        function persistFav(href) {
            if (authed) {
                fetch(@json(route('favorites.toggle')), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ href }),
                }).catch(() => {});
            } else {
                store(FAV_KEY, [...favs]);
            }
        }

        const state = { view: 'all', cat: 'all', q: '' };

        function apply() {
            let visible = 0;
            cards.forEach((c) => {
                const inView = state.view === 'all' ? true
                    : state.view === 'favorites' ? favs.has(c.dataset.href)
                    : recent.includes(c.dataset.href);
                const inCat = state.cat === 'all' || c.dataset.category === state.cat;
                const inQ = !state.q || `${c.dataset.name} ${c.dataset.keywords}`.includes(state.q);
                const show = inView && inCat && inQ;
                c.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            if (noResults) noResults.classList.toggle('hidden', visible !== 0);
            if (emptyMsg) emptyMsg.textContent = state.view === 'favorites'
                ? 'No favourites yet — tap the star on any tool to save it here.'
                : state.view === 'history' ? 'Nothing here yet — open a tool and it will show up in History.'
                : 'No tools match your search.';
        }

        // Favourite stars
        document.querySelectorAll('[data-fav]').forEach((btn) => {
            const href = btn.closest('[data-tool-card]').dataset.href;
            const sync = () => btn.classList.toggle('is-fav', favs.has(href));
            sync();
            btn.addEventListener('click', (e) => {
                e.preventDefault(); e.stopPropagation();
                if (favs.has(href)) favs.delete(href); else favs.add(href);
                sync();
                persistFav(href);
                updateStats();
                if (state.view === 'favorites') apply();
            });
        });

        // Track opened tools for History
        document.querySelectorAll('[data-card-link]').forEach((a) => {
            a.addEventListener('click', () => {
                const href = a.closest('[data-tool-card]').dataset.href;
                recent = [href, ...recent.filter((h) => h !== href)].slice(0, 24);
                store(HIST_KEY, recent);
            });
        });

        if (search) search.addEventListener('input', () => { state.q = search.value.trim().toLowerCase(); apply(); });

        tabs.forEach((t) => t.addEventListener('click', () => {
            tabs.forEach((x) => x.classList.remove('is-active'));
            t.classList.add('is-active');
            state.cat = t.dataset.cat;
            apply();
        }));

        function setView(v) {
            state.view = v;
            navs.forEach((n) => n.classList.toggle('is-active', n.dataset.nav === v));
            apply();
        }
        navs.forEach((n) => n.addEventListener('click', (e) => {
            e.preventDefault();
            setView(n.dataset.nav);
            try { window.history.replaceState(null, '', n.dataset.nav === 'all' ? location.pathname : '#' + n.dataset.nav); } catch (_) {}
        }));

        window.resetFilters = function () {
            state.cat = 'all'; state.q = '';
            if (search) search.value = '';
            tabs.forEach((x) => x.classList.toggle('is-active', x.dataset.cat === 'all'));
            apply();
        };

        const h = (location.hash || '').replace('#', '');
        setView(h === 'favorites' || h === 'history' ? h : 'all');
        updateStats();
        renderRecent();
    })();

    /* ---------------- Smart Toolbox ---------------- */
    (function () {
        const loaded = {};
        function loadScript(src) {
            if (loaded[src]) return loaded[src];
            loaded[src] = new Promise((res, rej) => {
                const s = document.createElement('script');
                s.src = src; s.onload = res; s.onerror = rej;
                document.head.appendChild(s);
            });
            return loaded[src];
        }

        const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

        function badge(text, color) {
            return `<span class="rounded-full border px-3 py-1 text-xs font-semibold ${color}">${text}</span>`;
        }
        function panel(title, bodyHtml, href) {
            const link = href ? `<a href="${href}" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-indigo-200 transition hover:bg-white/10">Open full tool →</a>` : '';
            return `<div class="rounded-2xl border border-white/10 bg-slate-950/40 p-4">
                <div class="mb-2 flex items-center justify-between gap-3"><span class="text-sm font-bold text-white">${title}</span>${link}</div>
                ${bodyHtml}</div>`;
        }
        function pre(text, cls = 'text-slate-200') {
            return `<pre class="max-h-64 overflow-auto rounded-xl bg-black/30 p-3 font-mono text-xs ${cls}">${esc(text)}</pre>`;
        }

        function b64urlDecode(str) {
            str = str.replace(/-/g, '+').replace(/_/g, '/');
            while (str.length % 4) str += '=';
            return decodeURIComponent(Array.prototype.map.call(atob(str), (c) => '%' + c.charCodeAt(0).toString(16).padStart(2, '0')).join(''));
        }

        function detect(v) {
            const types = [];
            const t = v.trim();
            if (!t) return types;
            // JWT
            if (/^[A-Za-z0-9_-]+\.[A-Za-z0-9_-]+\.[A-Za-z0-9_-]*$/.test(t)) types.push('jwt');
            // JSON
            if (/^[\[{]/.test(t)) { try { JSON.parse(t); types.push('json'); } catch (_) {} }
            // UUID
            if (/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i.test(t)) types.push('uuid');
            // Unix timestamp
            if (/^\d{10}$|^\d{13}$/.test(t)) types.push('timestamp');
            // Hex colour
            if (/^#?([0-9a-f]{3}|[0-9a-f]{6}|[0-9a-f]{8})$/i.test(t)) types.push('color');
            // URL
            if (/^https?:\/\/\S+$/i.test(t)) types.push('url');
            // SQL
            if (/\b(select|insert|update|delete|create|alter|drop)\b/i.test(t) && /\b(from|into|table|where|values)\b/i.test(t)) types.push('sql');
            // Base64 (avoid clashing with jwt/json/hex)
            if (!types.length && /^[A-Za-z0-9+/]+={0,2}$/.test(t.replace(/\s+/g, '')) && t.replace(/\s+/g, '').length % 4 === 0 && t.length >= 8) types.push('base64');
            // Number (base conversion)
            if (/^-?\d+$/.test(t) && !types.includes('timestamp')) types.push('number');
            return types;
        }

        window.analyzeSmart = async function () {
            const v = document.getElementById('smartInput').value;
            const badges = document.getElementById('smartBadges');
            const result = document.getElementById('smartResult');
            const types = detect(v);
            if (!v.trim()) { badges.innerHTML = ''; result.innerHTML = ''; return; }
            if (!types.length) {
                badges.innerHTML = badge('plain text', 'border-slate-600 text-slate-300');
                result.innerHTML = panel('Text tools', `<div class="flex flex-wrap gap-2">
                    <a href="/word-counter" class="rounded-lg bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Word Counter</a>
                    <a href="/case-converter" class="rounded-lg bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Case Converter</a>
                    <a href="/slug" class="rounded-lg bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Slug Generator</a>
                    <a href="/hash-generator" class="rounded-lg bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Hash Generator</a></div>`);
                return;
            }

            const colors = { jwt: 'border-indigo-400/40 text-indigo-200', json: 'border-amber-400/40 text-amber-200', uuid: 'border-lime-400/40 text-lime-200', timestamp: 'border-orange-400/40 text-orange-200', color: 'border-pink-400/40 text-pink-200', url: 'border-blue-400/40 text-blue-200', sql: 'border-violet-400/40 text-violet-200', base64: 'border-teal-400/40 text-teal-200', number: 'border-cyan-400/40 text-cyan-200' };
            badges.innerHTML = types.map((t) => badge(t.toUpperCase(), colors[t] || 'border-slate-600 text-slate-300')).join('');

            const t = v.trim();
            const panels = [];

            for (const type of types) {
                try {
                    if (type === 'json') {
                        panels.push(panel('Formatted JSON', pre(JSON.stringify(JSON.parse(t), null, 2), 'text-amber-100'), '/json-formatter'));
                    } else if (type === 'jwt') {
                        const [h, p] = t.split('.');
                        const header = JSON.stringify(JSON.parse(b64urlDecode(h)), null, 2);
                        const payload = JSON.stringify(JSON.parse(b64urlDecode(p)), null, 2);
                        panels.push(panel('Decoded JWT', `<div class="grid gap-3 sm:grid-cols-2"><div><div class="mb-1 text-xs font-semibold text-rose-300">Header</div>${pre(header, 'text-rose-200')}</div><div><div class="mb-1 text-xs font-semibold text-indigo-300">Payload</div>${pre(payload, 'text-indigo-200')}</div></div>`, '/jwt-decoder'));
                    } else if (type === 'uuid') {
                        const ver = t[14];
                        panels.push(panel('UUID', `<div class="text-sm text-slate-300">Valid UUID · version <span class="font-mono text-lime-200">${ver}</span></div>`, '/id-generator'));
                    } else if (type === 'timestamp') {
                        const ms = t.length === 13 ? +t : +t * 1000;
                        const d = new Date(ms);
                        panels.push(panel('Timestamp', `<div class="space-y-1 text-sm text-slate-300"><div>Local: <span class="font-mono text-white">${esc(d.toLocaleString())}</span></div><div>UTC: <span class="font-mono text-white">${esc(d.toUTCString())}</span></div><div>ISO: <span class="font-mono text-white">${esc(d.toISOString())}</span></div></div>`, '/timestamp'));
                    } else if (type === 'color') {
                        const hex = (t[0] === '#' ? t : '#' + t).toUpperCase();
                        panels.push(panel('Colour', `<div class="flex items-center gap-3"><div class="h-12 w-12 rounded-lg border border-white/10" style="background:${hex}"></div><span class="font-mono text-white">${esc(hex)}</span></div>`, '/color-picker'));
                    } else if (type === 'url') {
                        const u = new URL(t);
                        const params = [...u.searchParams].map(([k, val]) => `<tr><td class="pr-4 font-mono text-blue-200">${esc(k)}</td><td class="font-mono text-slate-300">${esc(val)}</td></tr>`).join('') || '<tr><td class="text-slate-500">no query params</td></tr>';
                        panels.push(panel('URL', `<div class="space-y-1 text-sm text-slate-300"><div>Host: <span class="font-mono text-white">${esc(u.host)}</span></div><div>Path: <span class="font-mono text-white">${esc(u.pathname)}</span></div><table class="mt-2 text-xs">${params}</table></div>`, '/query-parser'));
                    } else if (type === 'base64') {
                        panels.push(panel('Base64 decoded', pre(b64urlDecode(t.replace(/\s+/g, '')), 'text-teal-100'), '/base64'));
                    } else if (type === 'number') {
                        const n = BigInt(t);
                        panels.push(panel('Number bases', `<div class="grid grid-cols-2 gap-2 text-sm sm:grid-cols-4"><div><div class="text-xs text-slate-500">HEX</div><div class="font-mono text-white">${n.toString(16).toUpperCase()}</div></div><div><div class="text-xs text-slate-500">OCT</div><div class="font-mono text-white">${n.toString(8)}</div></div><div><div class="text-xs text-slate-500">BIN</div><div class="font-mono text-white break-all">${n.toString(2)}</div></div><div><div class="text-xs text-slate-500">DEC</div><div class="font-mono text-white">${n.toString(10)}</div></div></div>`, '/base-n'));
                    } else if (type === 'sql') {
                        await loadScript('https://cdn.jsdelivr.net/npm/sql-formatter@15.3.1/dist/sql-formatter.min.js');
                        const formatted = window.sqlFormatter.format(t);
                        panels.push(panel('Formatted SQL', pre(formatted, 'text-violet-100'), '/sql-formatter'));
                    }
                } catch (e) { /* ignore individual failures */ }
            }
            result.innerHTML = panels.join('');
        };

        /* ---- File input: read text files, or show metadata + tool links ---- */
        const bytes = (n) => n < 1024 ? n + ' B' : n < 1048576 ? (n / 1024).toFixed(1) + ' KB' : (n / 1048576).toFixed(2) + ' MB';
        async function sha256(buf) {
            const h = await crypto.subtle.digest('SHA-256', buf);
            return [...new Uint8Array(h)].map((b) => b.toString(16).padStart(2, '0')).join('');
        }
        const toolLinks = (links) => `<div class="flex flex-wrap gap-2">` + links.map(([href, label]) =>
            `<a href="${href}" data-handoff class="rounded-lg bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">${label}</a>`).join('') + `</div>`;

        // Carry the attached file to whichever tool the user opens.
        function wireHandoff(file) {
            document.querySelectorAll('#smartResult [data-handoff]').forEach((a) => {
                a.addEventListener('click', async (e) => {
                    e.preventDefault();
                    try { if (window.quickiesHandoff) await window.quickiesHandoff.put(file); } catch (_) {}
                    location.href = a.getAttribute('href');
                });
            });
        }

        async function handleFile(file) {
            const badges = document.getElementById('smartBadges');
            const result = document.getElementById('smartResult');
            const input = document.getElementById('smartInput');
            const name = file.name || 'file';
            const ext = (name.split('.').pop() || '').toLowerCase();
            const type = file.type || '';
            const textExt = ['txt','json','csv','tsv','yaml','yml','xml','md','markdown','log','sql','js','ts','html','css','svg','env','ini','conf'];
            const isText = type.startsWith('text/') || type === 'application/json' || textExt.includes(ext);

            // Text-like → drop the contents into the box and run the normal detector
            if (isText && file.size <= 2 * 1024 * 1024) {
                input.value = await file.text();
                window.analyzeSmart();
                if (window.showNotification) window.showNotification('Loaded ' + name, 'success');
                return;
            }

            const isImage = type.startsWith('image/') || ['png','jpg','jpeg','gif','webp','bmp','avif'].includes(ext);
            const isPdf = type === 'application/pdf' || ext === 'pdf';

            const buf = await file.arrayBuffer();
            let hash = '';
            try { hash = await sha256(buf); } catch (_) {}
            const meta = `<div class="grid grid-cols-2 gap-x-6 gap-y-1 text-sm text-slate-300 sm:grid-cols-3">
                <div><div class="text-xs text-slate-500">Name</div><div class="truncate font-mono text-white">${esc(name)}</div></div>
                <div><div class="text-xs text-slate-500">Size</div><div class="font-mono text-white">${bytes(file.size)}</div></div>
                <div><div class="text-xs text-slate-500">Type</div><div class="font-mono text-white">${esc(type || ext || 'unknown')}</div></div>
              </div>${hash ? `<div class="mt-3"><div class="mb-1 text-xs text-slate-500">SHA-256</div>${pre(hash, 'text-teal-100')}</div>` : ''}`;

            if (isImage) {
                const url = URL.createObjectURL(file);
                const dims = await new Promise((r) => { const im = new Image(); im.onload = () => r(im.naturalWidth + ' × ' + im.naturalHeight + ' px'); im.onerror = () => r(''); im.src = url; });
                badges.innerHTML = badge('IMAGE', 'border-orange-400/40 text-orange-200');
                result.innerHTML = panel('Image', `<div class="flex items-start gap-4"><img src="${url}" class="h-24 w-24 flex-shrink-0 rounded-xl border border-white/10 object-cover"><div class="min-w-0 flex-1">${meta}${dims ? `<div class="mt-2 text-sm text-slate-300">Dimensions: <span class="font-mono text-white">${dims}</span></div>` : ''}</div></div><div class="mt-3">${toolLinks([['/image-compressor','Compress'],['/image-cropper','Crop'],['/exif-viewer','EXIF data'],['/favicon-generator','Make favicon'],['/png-to-svg','To SVG']])}</div>`);
            } else if (isPdf) {
                badges.innerHTML = badge('PDF', 'border-rose-400/40 text-rose-200');
                result.innerHTML = panel('PDF', meta + `<div class="mt-3">${toolLinks([['/pdf-compressor','Compress'],['/pdf-split','Split'],['/pdf-merge','Merge'],['/pdf-to-images','To images'],['/pdf-to-word','To Word']])}</div>`);
            } else {
                badges.innerHTML = badge('FILE', 'border-slate-600 text-slate-300');
                result.innerHTML = panel('File', meta + `<div class="mt-3">${toolLinks([['/base64','Base64'],['/hash-generator','Hashes']])}</div>`);
            }
            wireHandoff(file);
        }

        (function () {
            const drop = document.getElementById('smartDrop');
            const fileInput = document.getElementById('smartFile');
            const fileBtn = document.getElementById('smartFileBtn');
            if (fileBtn && fileInput) {
                fileBtn.addEventListener('click', () => fileInput.click());
                fileInput.addEventListener('change', (e) => { if (e.target.files[0]) handleFile(e.target.files[0]); fileInput.value = ''; });
            }
            if (drop) {
                ['dragenter', 'dragover'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.add('ring-2', 'ring-indigo-500/40'); }));
                ['dragleave', 'drop'].forEach((ev) => drop.addEventListener(ev, (e) => { e.preventDefault(); drop.classList.remove('ring-2', 'ring-indigo-500/40'); }));
                drop.addEventListener('drop', (e) => { const f = e.dataTransfer.files[0]; if (f) handleFile(f); });
            }
        })();
    })();
</script>
@endpush
