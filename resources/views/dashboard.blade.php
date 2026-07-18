@extends('layouts.app')

@section('title', 'Dashboard')
@section('description', 'Quickies — a fast, free collection of everyday browser utilities for images, text, security and developers, plus a Smart Toolbox that auto-detects your input.')

@section('content')
    @php
        $freeTools = config('plans.free_tools', []);
        $isPro = auth()->check() && auth()->user()->hasActiveSubscription();
    @endphp
    {{-- Hero --}}
    <section class="mb-8 text-center sm:mb-10">
        <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-slate-300 backdrop-blur">
            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
            {{ count($quickies) }} tools · 100% in your browser
        </span>
        <h1 class="animate-gradient mt-6 bg-gradient-to-r from-fuchsia-400 via-indigo-400 to-cyan-400 bg-clip-text text-5xl font-bold tracking-tight text-transparent sm:text-7xl">
            Quickies
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-base text-slate-400 sm:text-lg">
            A growing toolbox of fast, privacy-friendly utilities. Paste anything into the Smart Toolbox and we'll figure out what to do with it.
        </p>
    </section>

    {{-- Smart Toolbox --}}
    <section class="mx-auto mb-10 max-w-4xl">
        <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <div class="pointer-events-none absolute -right-20 -top-20 h-52 w-52 rounded-full bg-gradient-to-br from-fuchsia-500 to-indigo-500 opacity-20 blur-3xl"></div>
            <div class="relative">
                <div class="mb-3 flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-fuchsia-500 to-indigo-500 shadow-lg">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </span>
                    <h2 class="text-lg font-bold text-white">Smart Toolbox</h2>
                    <span class="rounded-full border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold uppercase tracking-wide text-slate-400">auto-detect</span>
                </div>
                <textarea id="smartInput" rows="3" oninput="analyzeSmart()"
                    class="w-full resize-y rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    placeholder="Paste JSON, a JWT, Base64, a URL, a UUID, a Unix timestamp, SQL, a hex colour…"></textarea>
                <div id="smartBadges" class="mt-3 flex flex-wrap gap-2"></div>
                <div id="smartResult" class="mt-4 space-y-4"></div>
            </div>
        </div>
    </section>

    {{-- Controls --}}
    <section class="mb-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input id="toolSearch" type="search" autocomplete="off" placeholder="Search {{ count($quickies) }} tools…"
                       class="w-full rounded-2xl border border-white/10 bg-white/5 py-3 pl-12 pr-4 text-sm text-white placeholder-slate-500 backdrop-blur-xl transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/40">
            </div>
            <button id="resetLayout" onclick="resetLayout()" class="hidden items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-slate-300 transition hover:bg-white/10 sm:inline-flex">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                Reset layout
            </button>
        </div>
        <p class="mt-2 hidden text-xs text-slate-500 sm:block">Tip: drag any card to reorder tools within its group — your layout is saved automatically.</p>
    </section>

    {{-- Tool groups --}}
    <section id="toolGroups">
        @foreach ($quickieCategories as $category)
            @php $group = collect($quickies)->where('category', $category)->values(); @endphp
            <div class="tool-group mb-10" data-category-section="{{ $category }}">
                <h2 class="mb-4 flex items-center gap-3 text-sm font-bold uppercase tracking-widest text-slate-400">
                    {{ $category }}
                    <span class="h-px flex-1 bg-white/10"></span>
                    <span class="rounded-full border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] normal-case tracking-normal text-slate-500">{{ $group->count() }}</span>
                </h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3" data-dnd-group data-category="{{ \Illuminate\Support\Str::slug($category) }}">
                    @foreach ($group as $tool)
                        @php $toolFree = in_array($tool['href'], $freeTools, true); @endphp
                        <div data-tool-card
                             data-href="{{ $tool['href'] }}"
                             data-name="{{ strtolower($tool['name']) }}"
                             data-keywords="{{ strtolower($tool['description'].' '.$category) }}"
                             draggable="true"
                             class="group relative cursor-grab overflow-hidden rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl transition-all duration-200 hover:-translate-y-0.5 hover:border-white/25 hover:bg-white/10 active:cursor-grabbing">
                            <div class="pointer-events-none absolute -right-12 -top-12 h-32 w-32 rounded-full bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} opacity-0 blur-2xl transition-opacity duration-300 group-hover:opacity-25"></div>
                            <span class="absolute right-3 top-3 z-10 rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $toolFree ? 'bg-emerald-500/20 text-emerald-300' : ($isPro ? 'bg-indigo-500/20 text-indigo-200' : 'bg-white/10 text-slate-400') }}">
                                @if ($toolFree) Free @elseif ($isPro) Pro @else 🔒 Pro @endif
                            </span>
                            <a href="{{ $tool['href'] }}" class="relative flex items-start gap-3.5" data-card-link>
                                <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} shadow-lg transition-transform duration-300 group-hover:scale-110">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tool['icon'] }}"></path></svg>
                                </span>
                                <span class="min-w-0 flex-1 pr-10">
                                    <span class="block font-bold text-white">{{ $tool['name'] }}</span>
                                    <span class="mt-1 block text-xs leading-relaxed text-slate-400">{{ $tool['description'] }}</span>
                                </span>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div id="noResults" class="hidden py-16 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="mt-4 text-slate-400">No tools match your search.</p>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    /* ---------------- Search ---------------- */
    (function () {
        const search = document.getElementById('toolSearch');
        const cards = Array.from(document.querySelectorAll('[data-tool-card]'));
        const noResults = document.getElementById('noResults');
        search.addEventListener('input', () => {
            const q = search.value.trim().toLowerCase();
            let visible = 0;
            cards.forEach((card) => {
                const match = !q || `${card.dataset.name} ${card.dataset.keywords}`.includes(q);
                card.classList.toggle('hidden', !match);
                if (match) visible++;
            });
            document.querySelectorAll('[data-category-section]').forEach((sec) => {
                const anyVisible = sec.querySelector('[data-tool-card]:not(.hidden)');
                sec.classList.toggle('hidden', !anyVisible);
            });
            noResults.classList.toggle('hidden', visible !== 0);
        });
    })();

    /* ---------------- Drag & drop ordering ---------------- */
    (function () {
        const STORAGE_KEY = 'quickies:order:v1';
        const groups = Array.from(document.querySelectorAll('[data-dnd-group]'));
        let dragged = null;

        function loadOrder() {
            try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; } catch (_) { return {}; }
        }
        function saveOrder() {
            const data = {};
            groups.forEach((g) => {
                data[g.dataset.category] = Array.from(g.querySelectorAll('[data-tool-card]')).map((c) => c.dataset.href);
            });
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            document.getElementById('resetLayout').classList.remove('hidden');
        }
        function applyOrder() {
            const saved = loadOrder();
            if (!Object.keys(saved).length) return;
            groups.forEach((g) => {
                const order = saved[g.dataset.category];
                if (!order) return;
                order.forEach((href) => {
                    const card = g.querySelector(`[data-tool-card][data-href="${CSS.escape(href)}"]`);
                    if (card) g.appendChild(card);
                });
            });
            document.getElementById('resetLayout').classList.remove('hidden');
        }

        groups.forEach((group) => {
            group.addEventListener('dragstart', (e) => {
                const card = e.target.closest('[data-tool-card]');
                if (!card) return;
                dragged = card;
                setTimeout(() => card.classList.add('opacity-40'), 0);
            });
            group.addEventListener('dragend', () => {
                if (dragged) dragged.classList.remove('opacity-40');
                dragged = null;
                saveOrder();
            });
            group.addEventListener('dragover', (e) => {
                e.preventDefault();
                if (!dragged || dragged.parentElement !== group) return;
                const after = getDragAfter(group, e.clientX, e.clientY);
                if (after == null) group.appendChild(dragged);
                else group.insertBefore(dragged, after);
            });
        });

        function getDragAfter(group, x, y) {
            const cards = [...group.querySelectorAll('[data-tool-card]:not(.opacity-40)')];
            let closest = { dist: Infinity, el: null };
            cards.forEach((card) => {
                const box = card.getBoundingClientRect();
                const cx = box.left + box.width / 2;
                const cy = box.top + box.height / 2;
                const dist = Math.hypot(x - cx, y - cy);
                const isAfter = y < cy || (Math.abs(y - cy) < box.height / 2 && x < cx);
                if (isAfter && dist < closest.dist) closest = { dist, el: card };
            });
            return closest.el;
        }

        window.resetLayout = function () {
            localStorage.removeItem(STORAGE_KEY);
            location.reload();
        };

        applyOrder();
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
    })();
</script>
@endpush
