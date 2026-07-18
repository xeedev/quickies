<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="description" content="@yield('description', 'Quickies — a fast, free and privacy-first collection of everyday web utilities. Everything runs right in your browser.')">
    <title>@yield('title', 'Quickies') · Quickies</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="relative min-h-screen bg-slate-950 font-sans text-slate-100 antialiased overflow-x-hidden">

    {{-- Animated aurora background --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
        <div class="aurora aurora-1"></div>
        <div class="aurora aurora-2"></div>
        <div class="aurora aurora-3"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(2,6,23,0.6)_100%)]"></div>
    </div>

    {{-- Navigation --}}
    <header class="sticky top-0 z-40">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-2 backdrop-blur-xl">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-fuchsia-500 to-indigo-500 text-lg font-bold shadow-lg shadow-indigo-500/30">Q</span>
                    <span class="text-lg font-bold tracking-tight">Quickies</span>
                </a>
            </div>

            <div class="flex items-center gap-2">
                {{-- Tools dropdown (desktop) --}}
                <div class="relative hidden md:block" x-tools-menu>
                    <button type="button" data-tools-toggle
                            class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-200 backdrop-blur-xl transition hover:border-white/20 hover:bg-white/10">
                        <svg class="h-5 w-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        All tools
                        <svg class="h-4 w-4 text-slate-400 transition" data-tools-caret fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div data-tools-panel
                         class="invisible absolute right-0 mt-2 w-80 origin-top-right translate-y-1 scale-95 opacity-0 rounded-2xl border border-white/10 bg-slate-900/90 p-2 shadow-2xl shadow-black/50 backdrop-blur-2xl transition-all duration-150">
                        <div class="grid max-h-[70vh] gap-1 overflow-y-auto">
                            @foreach ($quickies as $tool)
                                <a href="{{ $tool['href'] }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition hover:bg-white/5 {{ request()->is(trim($tool['href'], '/')) ? 'bg-white/5' : '' }}">
                                    <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} shadow-lg">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tool['icon'] }}"></path></svg>
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-semibold text-white">{{ $tool['name'] }}</span>
                                        <span class="block truncate text-xs text-slate-400">{{ $tool['category'] }}</span>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ route('dashboard') }}" class="hidden rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-200 backdrop-blur-xl transition hover:border-white/20 hover:bg-white/10 sm:block">
                    Dashboard
                </a>

                @auth
                    @unless (auth()->user()->hasActiveSubscription())
                        <a href="{{ route('pricing') }}" class="hidden rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.03] sm:block">Upgrade</a>
                    @endunless
                    <div class="relative hidden md:block" x-account-menu>
                        <button type="button" data-account-toggle class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 text-sm font-semibold text-slate-200 backdrop-blur-xl transition hover:bg-white/10">
                            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-slate-600 to-slate-700 text-xs font-bold uppercase">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div data-account-panel class="invisible absolute right-0 mt-2 w-60 origin-top-right translate-y-1 scale-95 opacity-0 rounded-2xl border border-white/10 bg-slate-900/90 p-2 shadow-2xl shadow-black/50 backdrop-blur-2xl transition-all duration-150">
                            <div class="border-b border-white/10 px-3 py-2">
                                <div class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
                                <div class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</div>
                                <span class="mt-1 inline-block rounded-full border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold {{ auth()->user()->hasActiveSubscription() ? 'text-emerald-300' : 'text-slate-400' }}">{{ auth()->user()->planLabel() }} plan</span>
                            </div>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5">Admin panel</a>
                            @endif
                            @if (auth()->user()->hasActiveSubscription() && auth()->user()->subscription_status !== 'comp')
                                <a href="{{ route('billing.portal') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5">Manage billing</a>
                            @else
                                <a href="{{ route('pricing') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5">Plans &amp; billing</a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-rose-300 transition hover:bg-rose-500/10">Log out</button></form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hidden rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-200 backdrop-blur-xl transition hover:bg-white/10 sm:block">Log in</a>
                    <a href="{{ route('register') }}" class="hidden rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.03] sm:block">Sign up</a>
                @endauth

                {{-- Mobile menu toggle --}}
                <button type="button" data-mobile-toggle
                        class="flex h-11 w-11 items-center justify-center rounded-2xl border border-white/10 bg-white/5 backdrop-blur-xl transition hover:bg-white/10 md:hidden">
                    <svg class="h-6 w-6 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
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
                        <a href="{{ route('pricing') }}" class="mb-1 block rounded-xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-3 py-3 text-center font-bold text-white">Upgrade to Pro</a>
                    @endunless
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 font-semibold text-slate-200 transition hover:bg-white/5">Admin panel</a>
                    @endif
                @else
                    <div class="mb-2 grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-center text-sm font-bold text-white">Log in</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-3 py-2.5 text-center text-sm font-bold text-white">Sign up</a>
                    </div>
                @endauth
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 font-semibold transition hover:bg-white/5">
                    <svg class="h-5 w-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                @foreach ($quickies as $tool)
                    <a href="{{ $tool['href'] }}" class="flex items-center gap-3 rounded-xl px-3 py-3 transition hover:bg-white/5 {{ request()->is(trim($tool['href'], '/')) ? 'bg-white/5' : '' }}">
                        <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} shadow-lg">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tool['icon'] }}"></path></svg>
                        </span>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-semibold text-white">{{ $tool['name'] }}</span>
                            <span class="block truncate text-xs text-slate-400">{{ $tool['description'] }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <main class="mx-auto w-full max-w-7xl px-4 pb-20 pt-6 sm:px-6 sm:pt-10">
        @yield('content')
    </main>

    <footer class="border-t border-white/10 py-8">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 text-sm text-slate-400 sm:flex-row sm:px-6">
            <p>&copy; {{ date('Y') }} Quickies. Everything runs locally in your browser.</p>
            <a href="/" class="font-semibold text-slate-300 transition hover:text-white">Back to dashboard →</a>
        </div>
    </footer>

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
