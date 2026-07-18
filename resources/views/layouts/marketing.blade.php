<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="description" content="@yield('description', 'Quickies — 80+ fast, private, browser-based tools for developers, marketers and creators. One subscription, everything unlocked.')">
    <title>@yield('title', 'Quickies') · Quickies</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="relative min-h-screen bg-slate-950 font-sans text-slate-100 antialiased overflow-x-hidden">
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950"></div>
        <div class="aurora aurora-1"></div>
        <div class="aurora aurora-2"></div>
        <div class="aurora aurora-3"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(2,6,23,0.6)_100%)]"></div>
    </div>

    <header class="sticky top-0 z-40">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 rounded-2xl border border-white/10 bg-white/5 px-4 py-2 backdrop-blur-xl">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-fuchsia-500 to-indigo-500 text-lg font-bold shadow-lg shadow-indigo-500/30">Q</span>
                <span class="text-lg font-bold tracking-tight">Quickies</span>
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('pricing') }}" class="hidden rounded-2xl px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:text-white sm:block">Pricing</a>
                <a href="{{ route('dashboard') }}" class="hidden rounded-2xl px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:text-white sm:block">Tools</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.03]">Open app</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-200 backdrop-blur-xl transition hover:bg-white/10">Log in</a>
                    <a href="{{ route('register') }}" class="rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.03]">Get started</a>
                @endauth
            </div>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-white/10 py-10">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-4 text-sm text-slate-400 sm:flex-row sm:px-6">
            <div class="flex items-center gap-2">
                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-fuchsia-500 to-indigo-500 text-sm font-bold">Q</span>
                <span>&copy; {{ date('Y') }} Quickies. All rights reserved.</span>
            </div>
            <div class="flex flex-wrap items-center gap-x-5 gap-y-2">
                <a href="{{ route('pricing') }}" class="transition hover:text-white">Pricing</a>
                <a href="{{ route('dashboard') }}" class="transition hover:text-white">Tools</a>
                <a href="{{ route('login') }}" class="transition hover:text-white">Log in</a>
                <span>Privacy-first · Runs in your browser</span>
            </div>
        </div>
    </footer>

    <div id="toastContainer" class="pointer-events-none fixed inset-x-0 top-4 z-[100] flex flex-col items-center gap-2 px-4 sm:inset-x-auto sm:right-4 sm:items-end"></div>
    <script>
        window.showNotification = function (message, type = 'info', duration = 3600) {
            const palette = { success: 'bg-emerald-400', error: 'bg-rose-400', info: 'bg-indigo-400', warning: 'bg-amber-400' };
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast pointer-events-auto flex w-full max-w-sm items-center gap-3 rounded-2xl border border-white/10 bg-slate-900/90 px-4 py-3 shadow-2xl shadow-black/40 backdrop-blur-xl';
            toast.innerHTML = `<span class="h-2.5 w-2.5 flex-shrink-0 rounded-full ${palette[type] || palette.info}"></span><p class="min-w-0 flex-1 text-sm font-medium text-slate-100">${message}</p>`;
            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('toast-in'));
            setTimeout(() => { toast.classList.add('toast-out'); setTimeout(() => toast.remove(), 250); }, duration);
        };
        @if (session('status'))
            window.showNotification(@json(session('status')), 'success');
        @endif
        @if (session('error'))
            window.showNotification(@json(session('error')), 'error');
        @endif
    </script>
    @stack('scripts')
</body>
</html>
