<!DOCTYPE html>
<html lang="en" class="scroll-smooth theme-light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'Quickies — a fast, free and privacy-first collection of everyday web utilities. Everything runs right in your browser.')">
    <title>@yield('title', 'Quickies') · Quickies</title>
    <script>(function(){try{var t=localStorage.getItem('quickies:theme');document.documentElement.classList.toggle('theme-light', t!=='dark');}catch(e){}})();</script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="relative min-h-screen bg-[#0a0b11] font-sans text-slate-100 antialiased overflow-x-hidden">

    {{-- Quiet static canvas --}}
    <div class="app-bg pointer-events-none"></div>

    {{-- Navigation --}}
    <header class="app-header sticky top-0 z-40">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-fuchsia-500 to-indigo-500 text-lg font-bold text-white shadow-sm">Q</span>
                <span class="text-[17px] font-semibold tracking-tight">Quickies</span>
            </a>

            <div class="flex items-center gap-1.5">
                {{-- Command palette trigger (desktop) --}}
                <button type="button" data-cmdk-open
                        class="hidden items-center gap-2 rounded-lg border border-white/10 bg-white/[0.04] px-3 py-2 text-sm font-medium text-slate-400 transition hover:bg-white/[0.08] hover:text-white md:flex">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Search tools</span>
                    <kbd class="ml-3 rounded border border-white/15 bg-white/[0.06] px-1.5 py-0.5 font-mono text-[11px] text-slate-400">⌘K</kbd>
                </button>

                @auth
                    @unless (auth()->user()->hasActiveSubscription())
                        <a href="{{ route('pricing') }}" class="hidden rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-100 sm:block">Upgrade</a>
                    @endunless
                    <div class="relative hidden md:block" x-account-menu>
                        <button type="button" data-account-toggle class="ml-1 flex items-center gap-2 rounded-lg px-1.5 py-1.5 text-sm font-medium text-slate-200 transition hover:bg-white/[0.06]">
                            <span class="flex h-8 w-8 items-center justify-center rounded-lg border border-white/10 bg-white/[0.06] text-xs font-bold uppercase text-slate-200">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div data-account-panel class="invisible absolute right-0 mt-2 w-60 origin-top-right translate-y-1 scale-95 opacity-0 rounded-xl border border-white/10 bg-[#0d0e12]/95 p-2 shadow-2xl shadow-black/50 backdrop-blur-2xl transition-all duration-150">
                            <div class="border-b border-white/10 px-3 py-2">
                                <div class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
                                <div class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</div>
                                <span class="mt-1 inline-block rounded-full border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold {{ auth()->user()->hasActiveSubscription() ? 'text-emerald-300' : 'text-slate-400' }}">{{ auth()->user()->planLabel() }} plan</span>
                            </div>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5">Admin panel</a>
                            @endif
                            @if (auth()->user()->hasActiveSubscription() && auth()->user()->subscription_status !== 'comp')
                                <a href="{{ route('billing.portal') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5">Manage billing</a>
                            @else
                                <a href="{{ route('pricing') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5">Plans &amp; billing</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-rose-300 transition hover:bg-rose-500/10">Log out</button></form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-lg px-3.5 py-2 text-sm font-medium text-slate-300 transition hover:bg-white/[0.06] hover:text-white sm:block">Log in</a>
                    <a href="{{ route('register') }}" class="hidden rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900 transition hover:bg-slate-100 sm:block">Sign up</a>
                @endauth

                {{-- Mobile menu toggle --}}
                <button type="button" data-mobile-toggle
                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-white/10 bg-white/[0.04] transition hover:bg-white/10 md:hidden">
                        <svg class="h-5 w-5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </nav>
    </header>

    {{-- Mobile drawer --}}
    <div data-mobile-panel class="fixed inset-0 z-50 hidden md:hidden">
        <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm" data-mobile-close></div>
        <div class="absolute inset-y-0 right-0 flex w-[85%] max-w-sm flex-col border-l border-white/10 bg-slate-900/95 shadow-2xl">
            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                <span class="text-lg font-bold">All tools</span>
                <button type="button" data-mobile-close class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/10 bg-white/5 transition hover:bg-white/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 space-y-1 overflow-y-auto p-3">
                @auth
                    <div class="mb-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2.5">
                        <div class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
                        <span class="mt-0.5 inline-block rounded-full border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold {{ auth()->user()->hasActiveSubscription() ? 'text-emerald-300' : 'text-slate-400' }}">{{ auth()->user()->planLabel() }} plan</span>
                    </div>
                    @unless (auth()->user()->hasActiveSubscription())
                        <a href="{{ route('pricing') }}" class="mb-1 block rounded-xl bg-white px-3 py-3 text-center font-semibold text-slate-900 transition hover:bg-slate-100">Upgrade to Pro</a>
                    @endunless
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 font-semibold text-slate-200 transition hover:bg-white/5">Admin panel</a>
                    @endif
                @else
                    <div class="mb-2 grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-center text-sm font-semibold text-white">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-white px-3 py-2.5 text-center text-sm font-semibold text-slate-900">Sign up</a>
                    </div>
                @endauth
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 font-semibold transition hover:bg-white/5">
                    <svg class="h-5 w-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                @foreach ($quickies as $tool)
                    <a href="{{ $tool['href'] }}" class="group flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-white/5 {{ request()->is(trim($tool['href'], '/')) ? 'bg-white/5' : '' }}">
                        <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $tool['icon'] }}"></path></svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-medium text-white">{{ $tool['name'] }}</span>
                            <span class="block truncate text-xs text-slate-500">{{ $tool['description'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="mx-auto flex w-full max-w-[100rem]">
        {{-- Sidebar (desktop) --}}
        <aside class="sticky top-[57px] hidden h-[calc(100vh-57px)] w-64 flex-shrink-0 flex-col border-r border-white/[0.08] px-4 py-6 lg:flex">
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" data-nav="all" class="side-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path></svg>
                    Toolbox
                </a>
                <a href="{{ route('dashboard') }}#favorites" data-nav="favorites" class="side-link">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11.48 3.5a.56.56 0 011.04 0l2.08 4.71 5.12.46c.5.04.7.66.32 1l-3.87 3.4 1.15 5.02c.11.49-.42.87-.85.61L12 16.5l-4.47 2.7c-.43.26-.96-.12-.85-.61l1.15-5.02-3.87-3.4c-.38-.34-.18-.96.32-1l5.12-.46 2.08-4.71z"></path></svg>
                    Favorites
                </a>
                <a href="{{ route('dashboard') }}#history" data-nav="history" class="side-link">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    History
                </a>
            </nav>

            <div class="mt-auto space-y-4 pt-6">
                @auth
                    @if (auth()->user()->hasActiveSubscription())
                        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-500/[0.06] p-4 text-center">
                            <div class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-400/15 text-emerald-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="text-sm font-semibold text-white">{{ auth()->user()->isAdmin() ? 'Admin' : 'Pro' }}</div>
                            <div class="mt-0.5 text-xs text-emerald-300">Full access unlocked</div>
                        </div>
                    @else
                        <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 text-center">
                            <div class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-lg bg-amber-400/15 text-amber-500">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 6l5.5 4L12 4l3.5 6L21 6l-2 10H5zm0 2h14v2H5v-2z"></path></svg>
                            </div>
                            <div class="text-sm font-semibold text-white">Pro Plan</div>
                            <div class="mt-0.5 text-xs text-slate-500">Unlock premium tools</div>
                            <a href="{{ route('pricing') }}" class="mt-3 block rounded-lg bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-3 py-2 text-sm font-semibold text-white transition hover:opacity-90">Upgrade Now</a>
                        </div>
                    @endif
                @else
                    <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-4 text-center">
                        <div class="mx-auto mb-2 flex h-9 w-9 items-center justify-center rounded-lg bg-amber-400/15 text-amber-500">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 6l5.5 4L12 4l3.5 6L21 6l-2 10H5zm0 2h14v2H5v-2z"></path></svg>
                        </div>
                        <div class="text-sm font-semibold text-white">Pro Plan</div>
                        <div class="mt-0.5 text-xs text-slate-500">Unlock premium tools</div>
                        <a href="{{ route('register') }}" class="mt-3 block rounded-lg bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-3 py-2 text-sm font-semibold text-white transition hover:opacity-90">Upgrade Now</a>
                    </div>
                @endauth

                <button type="button" id="themeToggle" class="flex w-full items-center justify-between rounded-xl border border-white/10 bg-white/[0.04] px-3 py-2.5 text-sm font-medium text-slate-400 transition hover:bg-white/[0.06] hover:text-white">
                    <span class="flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path id="themeIcon" stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-9h-1M4.34 12h-1m14.14-6.14l-.7.7M6.34 6.34l-.7-.7m12.02 12.02l-.7-.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <span id="themeLabel">Light</span>
                    </span>
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path></svg>
                </button>
            </div>
        </aside>

        <main class="min-w-0 flex-1 px-4 pb-20 pt-6 sm:px-8 sm:pt-8">
            @yield('content')
        </main>
    </div>

    <footer class="border-t border-white/[0.06] py-8">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 text-sm text-slate-500 sm:flex-row sm:px-6">
            <p>&copy; {{ date('Y') }} Quickies. Everything runs locally in your browser.</p>
            <a href="/" class="font-medium text-slate-400 transition hover:text-white">Back to dashboard &rarr;</a>
        </div>
    </footer>

    {{-- Command palette (⌘K) --}}
    <div data-cmdk class="fixed inset-0 z-[90] hidden">
        <div class="absolute inset-0 bg-black/50" data-cmdk-close></div>
        <div class="absolute left-1/2 top-20 w-[92%] max-w-xl -translate-x-1/2 overflow-hidden rounded-2xl border border-white/10 bg-[#0d0e12]/95 shadow-2xl shadow-black/60 backdrop-blur-2xl">
            <div class="flex items-center gap-3 border-b border-white/10 px-4">
                <svg class="h-5 w-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input data-cmdk-input type="text" placeholder="Search {{ count($quickies) }} tools…" class="w-full bg-transparent py-4 text-base text-white placeholder-slate-500 focus:outline-none">
                <kbd class="flex-shrink-0 rounded border border-white/15 bg-white/[0.06] px-1.5 py-0.5 font-mono text-[11px] text-slate-400">esc</kbd>
            </div>
            <div data-cmdk-list class="max-h-[60vh] overflow-y-auto p-2">
                @foreach ($quickies as $tool)
                    <a href="{{ $tool['href'] }}" data-cmdk-item data-name="{{ strtolower($tool['name'].' '.$tool['category'].' '.$tool['description']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition">
                        <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} text-white shadow-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $tool['icon'] }}"></path></svg>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-sm font-medium text-white">{{ $tool['name'] }}</span>
                            <span class="block truncate text-xs text-slate-500">{{ $tool['category'] }}</span>
                        </span>
                        <svg class="h-4 w-4 flex-shrink-0 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @endforeach
                <div data-cmdk-empty class="hidden px-3 py-10 text-center text-sm text-slate-500">No tools match your search.</div>
            </div>
        </div>
    </div>

    {{-- Toast container --}}
    <div id="toastContainer" class="pointer-events-none fixed inset-x-0 top-4 z-[100] flex flex-col items-center gap-2 px-4 sm:inset-x-auto sm:right-4 sm:items-end"></div>

    <script>
        // ---- Global toast system ----
        window.showNotification = function (message, type = 'info', duration = 3200) {
            const palette = {
                success: { bar: 'bg-emerald-400', icon: 'M5 13l4 4L19 7' },
                error:   { bar: 'bg-rose-400',    icon: 'M6 18L18 6M6 6l12 12' },
                info:    { bar: 'bg-indigo-400',  icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
                warning: { bar: 'bg-amber-400',   icon: 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z' },
            };
            const conf = palette[type] || palette.info;
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast pointer-events-auto flex w-full max-w-sm items-center gap-3 overflow-hidden rounded-2xl border border-white/10 bg-slate-900/90 px-4 py-3 shadow-2xl shadow-black/40 backdrop-blur-xl';
            toast.innerHTML = `
                <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg ${conf.bar}/20">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${conf.icon}"></path></svg>
                </span>
                <p class="min-w-0 flex-1 text-sm font-medium text-slate-100">${message}</p>`;
            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('toast-in'));
            const remove = () => {
                toast.classList.add('toast-out');
                setTimeout(() => toast.remove(), 250);
            };
            const timer = setTimeout(remove, duration);
            toast.addEventListener('click', () => { clearTimeout(timer); remove(); });
        };

        // ---- Global copy helper ----
        window.copyToClipboard = async function (text, label = 'Copied to clipboard') {
            try {
                await navigator.clipboard.writeText(text);
                window.showNotification(label, 'success');
            } catch (e) {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); window.showNotification(label, 'success'); }
                catch (_) { window.showNotification('Unable to copy', 'error'); }
                ta.remove();
            }
        };

        // ---- Navigation behaviour ----
        (function () {
            const toggle = document.querySelector('[data-tools-toggle]');
            const panel = document.querySelector('[data-tools-panel]');
            const caret = document.querySelector('[data-tools-caret]');
            let open = false;
            const setOpen = (state) => {
                open = state;
                if (!panel) return;
                panel.classList.toggle('invisible', !state);
                panel.classList.toggle('opacity-0', !state);
                panel.classList.toggle('scale-95', !state);
                panel.classList.toggle('translate-y-1', !state);
                if (caret) caret.classList.toggle('rotate-180', state);
            };
            if (toggle) {
                toggle.addEventListener('click', (e) => { e.stopPropagation(); setOpen(!open); });
                document.addEventListener('click', (e) => {
                    if (open && !e.target.closest('[x-tools-menu]')) setOpen(false);
                });
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
            }

            const mobileToggle = document.querySelector('[data-mobile-toggle]');
            const mobilePanel = document.querySelector('[data-mobile-panel]');
            const setMobile = (state) => {
                if (!mobilePanel) return;
                mobilePanel.classList.toggle('hidden', !state);
                document.body.classList.toggle('overflow-hidden', state);
            };
            if (mobileToggle) mobileToggle.addEventListener('click', () => setMobile(true));
            document.querySelectorAll('[data-mobile-close]').forEach((el) => el.addEventListener('click', () => setMobile(false)));

            // Account dropdown
            const accToggle = document.querySelector('[data-account-toggle]');
            const accPanel = document.querySelector('[data-account-panel]');
            let accOpen = false;
            const setAcc = (state) => {
                accOpen = state;
                if (!accPanel) return;
                accPanel.classList.toggle('invisible', !state);
                accPanel.classList.toggle('opacity-0', !state);
                accPanel.classList.toggle('scale-95', !state);
                accPanel.classList.toggle('translate-y-1', !state);
            };
            if (accToggle) {
                accToggle.addEventListener('click', (e) => { e.stopPropagation(); setAcc(!accOpen); });
                document.addEventListener('click', (e) => { if (accOpen && !e.target.closest('[x-account-menu]')) setAcc(false); });
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setAcc(false); });
            }
        })();

        // ---- Theme toggle (light / dark) ----
        (function () {
            const btn = document.getElementById('themeToggle');
            const label = document.getElementById('themeLabel');
            const icon = document.getElementById('themeIcon');
            const sun = 'M12 3v1m0 16v1m8.66-9h-1M4.34 12h-1m14.14-6.14l-.7.7M6.34 6.34l-.7-.7m12.02 12.02l-.7-.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z';
            const moon = 'M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z';
            const sync = () => {
                const light = document.documentElement.classList.contains('theme-light');
                if (label) label.textContent = light ? 'Light' : 'Dark';
                if (icon) icon.setAttribute('d', light ? sun : moon);
            };
            sync();
            if (btn) btn.addEventListener('click', () => {
                const light = !document.documentElement.classList.contains('theme-light');
                document.documentElement.classList.toggle('theme-light', light);
                try { localStorage.setItem('quickies:theme', light ? 'light' : 'dark'); } catch (e) {}
                sync();
            });
        })();

        // ---- File handoff between Smart paste and tool pages ----
        // Lets a file attached in Smart paste carry over to the tool the user
        // opens, so they never have to upload it twice. Stored in IndexedDB
        // (survives navigation, keeps binary intact).
        window.quickiesHandoff = (function () {
            const DB = 'quickies_handoff', STORE = 'files', KEY = 'pending';
            const open = () => new Promise((res, rej) => {
                const r = indexedDB.open(DB, 1);
                r.onupgradeneeded = () => r.result.createObjectStore(STORE);
                r.onsuccess = () => res(r.result);
                r.onerror = () => rej(r.error);
            });
            async function put(file) {
                try {
                    const db = await open();
                    await new Promise((res, rej) => {
                        const tx = db.transaction(STORE, 'readwrite');
                        tx.objectStore(STORE).put(file, KEY);
                        tx.oncomplete = res; tx.onerror = () => rej(tx.error);
                    });
                } catch (e) {}
            }
            async function take() {
                try {
                    const db = await open();
                    return await new Promise((res) => {
                        const tx = db.transaction(STORE, 'readwrite');
                        const s = tx.objectStore(STORE);
                        const g = s.get(KEY);
                        g.onsuccess = () => { const v = g.result; s.delete(KEY); res(v || null); };
                        g.onerror = () => res(null);
                    });
                } catch (e) { return null; }
            }
            return { put, take };
        })();

        // Receiver: on a tool page, inject any handed-off file into its uploader.
        window.addEventListener('DOMContentLoaded', async function () {
            const input = document.getElementById('fileInput') || document.querySelector('input[type="file"]:not(#smartFile)');
            if (!input) return;
            const file = await window.quickiesHandoff.take();
            if (!file) return;
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
                if (window.showNotification) window.showNotification('Loaded ' + (file.name || 'file') + ' from Smart paste', 'success');
            } catch (e) {}
        });

        // ---- Command palette (⌘K) ----
        (function () {
            const modal = document.querySelector('[data-cmdk]');
            if (!modal) return;
            const input = modal.querySelector('[data-cmdk-input]');
            const items = Array.from(modal.querySelectorAll('[data-cmdk-item]'));
            const empty = modal.querySelector('[data-cmdk-empty]');
            let visible = items.slice();
            let sel = 0;

            const isOpen = () => !modal.classList.contains('hidden');
            function open() { modal.classList.remove('hidden'); document.body.classList.add('overflow-hidden'); input.value = ''; filter(); input.focus(); }
            function close() { modal.classList.add('hidden'); document.body.classList.remove('overflow-hidden'); }
            function setSel(i) {
                items.forEach((el) => el.removeAttribute('data-active'));
                if (!visible.length) return;
                sel = (i + visible.length) % visible.length;
                const el = visible[sel];
                el.setAttribute('data-active', '');
                el.scrollIntoView({ block: 'nearest' });
            }
            function filter() {
                const q = input.value.trim().toLowerCase();
                visible = [];
                items.forEach((el) => {
                    const match = !q || el.dataset.name.includes(q);
                    el.classList.toggle('hidden', !match);
                    if (match) visible.push(el);
                });
                empty.classList.toggle('hidden', visible.length > 0);
                setSel(0);
            }

            document.querySelectorAll('[data-cmdk-open]').forEach((b) => b.addEventListener('click', open));
            modal.querySelectorAll('[data-cmdk-close]').forEach((b) => b.addEventListener('click', close));
            items.forEach((el) => el.addEventListener('mousemove', () => { const i = visible.indexOf(el); if (i >= 0 && i !== sel) setSel(i); }));
            input.addEventListener('input', filter);
            input.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowDown') { e.preventDefault(); setSel(sel + 1); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); setSel(sel - 1); }
                else if (e.key === 'Enter') { e.preventDefault(); if (visible[sel]) location.href = visible[sel].getAttribute('href'); }
                else if (e.key === 'Escape') { close(); }
            });
            document.addEventListener('keydown', (e) => {
                if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); isOpen() ? close() : open(); }
                else if (e.key === '/' && !/^(INPUT|TEXTAREA|SELECT)$/.test((e.target.tagName || '')) && !isOpen()) { e.preventDefault(); open(); }
            });
        })();
    </script>

    @if (session('status'))
        <script>window.showNotification(@json(session('status')), 'success');</script>
    @endif
    @if (session('error'))
        <script>window.showNotification(@json(session('error')), 'error');</script>
    @endif

    @stack('scripts')
</body>
</html>
