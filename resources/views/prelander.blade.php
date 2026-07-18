@extends('layouts.marketing')

@section('title', 'The all-in-one toolbox for people who ship')
@section('description', 'Quickies bundles 80+ blazing-fast, private, browser-based tools for developers, marketers and creators into one subscription. Trusted by 250,000+ makers. Start free.')

@php
    // Small helper to render heroicon-style SVGs (matches the dashboard icon set).
    $svg = fn ($d, $class = 'h-6 w-6') =>
        '<svg class="'.$class.'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="'.$d.'"></path></svg>';

    $showcase = collect($quickies)->whereIn('href', [
        '/pdf-compressor', '/image-cropper', '/json-formatter', '/qr-code',
        '/jwt-decoder', '/word-to-pdf', '/color-picker', '/regex-tester', '/base64',
    ])->values();

    $star = 'M11.48 3.5a.56.56 0 011.04 0l2.08 4.71 5.12.46c.5.04.7.66.32 1l-3.87 3.4 1.15 5.02c.11.49-.42.87-.85.61L12 16.5l-4.47 2.7c-.43.26-.96-.12-.85-.61l1.15-5.02-3.87-3.4c-.38-.34-.18-.96.32-1l5.12-.46 2.08-4.71z';
    $check = 'M4.5 12.75l6 6 9-13.5';
    $arrow = 'M13 7l5 5m0 0l-5 5m5-5H6';
@endphp

@section('content')
    {{-- ===================== HERO ===================== --}}
    <section class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute left-1/2 top-[-10%] h-[420px] w-[820px] -translate-x-1/2 rounded-full bg-gradient-to-r from-fuchsia-600/25 via-indigo-600/25 to-cyan-500/20 blur-[110px]"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 pb-8 pt-14 sm:px-6 sm:pt-20">
            <div class="mx-auto max-w-3xl text-center">
                <div class="mb-7 flex items-center justify-center gap-3">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3.5 py-1.5 text-xs font-semibold text-slate-300 backdrop-blur">
                        <span class="flex -space-x-1.5">
                            @foreach (['from-fuchsia-500 to-pink-500','from-indigo-500 to-blue-500','from-emerald-500 to-teal-500'] as $g)
                                <span class="h-5 w-5 rounded-full bg-gradient-to-br {{ $g }} ring-2 ring-slate-950"></span>
                            @endforeach
                        </span>
                        Loved by 250,000+ makers
                    </span>
                    <span class="hidden items-center gap-1 rounded-full border border-white/10 bg-white/5 px-3.5 py-1.5 text-xs font-semibold text-amber-300 backdrop-blur sm:inline-flex">
                        <span class="flex text-amber-400">
                            @for ($i = 0; $i < 5; $i++) {!! $svg($star, 'h-3.5 w-3.5 fill-amber-400 stroke-amber-400') !!} @endfor
                        </span>
                        4.9/5
                    </span>
                </div>

                <h1 class="text-5xl font-bold leading-[1.03] tracking-tight text-white sm:text-7xl">
                    Every tool you need,
                    <span class="animate-gradient bg-gradient-to-r from-fuchsia-400 via-indigo-400 to-cyan-400 bg-clip-text text-transparent">one login away</span>
                </h1>
                <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-400 sm:text-xl">
                    Compress PDFs, crop images, format code, decode tokens, build campaigns and 75 more — lightning fast and 100% private, right in your browser. Cancel the tab-hopping.
                </p>

                <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="group inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-8 py-4 text-base font-bold text-white shadow-xl shadow-indigo-500/30 transition hover:scale-[1.03] sm:w-auto">
                        Start free — no card needed
                        {!! $svg($arrow, 'h-5 w-5 transition group-hover:translate-x-0.5') !!}
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-8 py-4 text-base font-bold text-slate-100 backdrop-blur-xl transition hover:bg-white/10 sm:w-auto">
                        {!! $svg('M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'h-5 w-5') !!}
                        See it in action
                    </a>
                </div>
                <p class="mt-4 flex flex-wrap items-center justify-center gap-x-5 gap-y-1 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-1.5">{!! $svg($check, 'h-4 w-4 text-emerald-400') !!} Try every tool free</span>
                    <span class="inline-flex items-center gap-1.5">{!! $svg($check, 'h-4 w-4 text-emerald-400') !!} No uploads to servers</span>
                    <span class="inline-flex items-center gap-1.5">{!! $svg($check, 'h-4 w-4 text-emerald-400') !!} Cancel anytime</span>
                </p>
            </div>

            {{-- App window mock --}}
            <div class="relative mx-auto mt-16 max-w-5xl">
                <div class="pointer-events-none absolute -inset-x-10 -top-8 bottom-0 -z-10 rounded-[2.5rem] bg-gradient-to-b from-white/5 to-transparent blur-2xl"></div>
                <div class="overflow-hidden rounded-3xl border border-white/10 bg-slate-900/60 shadow-2xl shadow-black/50 backdrop-blur-xl">
                    <div class="flex items-center gap-2 border-b border-white/10 bg-white/5 px-4 py-3">
                        <span class="h-3 w-3 rounded-full bg-rose-400/80"></span>
                        <span class="h-3 w-3 rounded-full bg-amber-400/80"></span>
                        <span class="h-3 w-3 rounded-full bg-emerald-400/80"></span>
                        <div class="mx-auto flex items-center gap-2 rounded-lg border border-white/10 bg-slate-950/50 px-3 py-1 text-xs text-slate-400">
                            {!! $svg('M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'h-3.5 w-3.5') !!}
                            app.quickies.io
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 sm:p-6">
                        @foreach ($showcase as $tool)
                            <div class="group flex items-start gap-3 rounded-2xl border border-white/10 bg-white/5 p-4 transition hover:-translate-y-0.5 hover:bg-white/10">
                                <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $tool['from'] }} {{ $tool['to'] }} shadow-lg">
                                    {!! $svg($tool['icon'], 'h-6 w-6 text-white') !!}
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-bold text-white">{{ $tool['name'] }}</span>
                                    <span class="mt-0.5 block truncate text-xs text-slate-400">{{ $tool['category'] }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== TRUST BAR ===================== --}}
    <section class="border-y border-white/10 bg-white/[0.02] py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6">
            <p class="text-center text-xs font-semibold uppercase tracking-widest text-slate-500">Powering fast teams at</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-x-10 gap-y-4 text-slate-400">
                @foreach (['Northwind','Lumen','Vertex Labs','Quanta','Nimbus','Aperture','Halcyon'] as $brand)
                    <span class="text-lg font-bold tracking-tight text-slate-400/80">{{ $brand }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== STATS ===================== --}}
    <section class="mx-auto max-w-6xl px-4 py-14 sm:px-6">
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach ([['80+','Tools in one place'],['250K+','Makers onboard'],['12M+','Files processed'],['100%','Runs in your browser']] as $stat)
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 text-center backdrop-blur-xl">
                    <div class="bg-gradient-to-r from-fuchsia-400 to-indigo-400 bg-clip-text text-4xl font-bold text-transparent sm:text-5xl">{{ $stat[0] }}</div>
                    <div class="mt-2 text-xs font-semibold uppercase tracking-wide text-slate-400 sm:text-sm">{{ $stat[1] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===================== FEATURE BENTO ===================== --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-sm font-bold uppercase tracking-widest text-indigo-300">Why Quickies</span>
            <h2 class="mt-3 text-4xl font-bold tracking-tight text-white sm:text-5xl">Built for people who ship</h2>
            <p class="mt-4 text-lg text-slate-400">Everything you reach for during the day — without the ads, uploads or paywalled downloads.</p>
        </div>

        @php
        $features = [
            ['Instant &amp; offline', 'Tools run locally in your browser. No waiting on uploads, no server queues — results appear the moment you click.', 'M13 10V3L4 14h7v7l9-11h-7z', 'from-fuchsia-500','to-pink-500'],
            ['Truly private', 'Your files and text never leave your device. Perfect for contracts, tokens and anything sensitive.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'from-indigo-500','to-blue-500'],
            ['One subscription', 'Documents, images, developer, marketing and VAS tools — 80+ of them, all under a single login.', 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'from-emerald-500','to-teal-500'],
            ['Real conversions', 'PDF to Word, images to PDF, JSON to YAML to CSV to Excel — the exports you actually need, done right.', 'M7 16V4m0 0L3 8m4-4l4 4m6 12v-12m0 12l4-4m-4 4l-4-4', 'from-amber-500','to-orange-500'],
            ['Always improving', 'New tools ship every month and land in your plan automatically — no upgrades, no extra fees.', 'M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.63 8.41m5.96 5.96a14.9 14.9 0 01-5.84 2.58m-.12-8.54a6 6 0 00-7.38 5.84h4.8m2.58-5.84a14.9 14.9 0 00-2.58 5.84', 'from-cyan-500','to-sky-500'],
            ['Fair pricing', 'One low subscription unlocks everything. No per-tool fees, no surprise limits. Cancel in a click.', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'from-violet-500','to-purple-500'],
        ];
        @endphp
        <div class="mt-12 grid grid-cols-1 gap-5 md:grid-cols-3">
            @foreach ($features as $f)
                <div class="group relative overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-7 backdrop-blur-xl transition hover:-translate-y-1 hover:border-white/20">
                    <div class="pointer-events-none absolute -right-14 -top-14 h-36 w-36 rounded-full bg-gradient-to-br {{ $f[3] }} {{ $f[4] }} opacity-0 blur-2xl transition-opacity group-hover:opacity-25"></div>
                    <div class="relative mb-5 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br {{ $f[3] }} {{ $f[4] }} shadow-lg">
                        {!! $svg($f[2], 'h-6 w-6 text-white') !!}
                    </div>
                    <h3 class="relative text-lg font-bold text-white">{!! $f[0] !!}</h3>
                    <p class="relative mt-2 text-sm leading-relaxed text-slate-400">{!! $f[1] !!}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===================== HOW IT WORKS ===================== --}}
    <section class="mx-auto max-w-6xl px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-sm font-bold uppercase tracking-widest text-indigo-300">Ridiculously simple</span>
            <h2 class="mt-3 text-4xl font-bold tracking-tight text-white sm:text-5xl">Get it done in three steps</h2>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-5 md:grid-cols-3">
            @php
            $steps = [
                ['01','Pick a tool','Search 80+ tools or paste anything into the Smart Toolbox and let it detect what you need.', 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'],
                ['02','Do the work','Everything runs instantly in your browser. Drop a file, tweak the options, watch it happen live.', 'M13 10V3L4 14h7v7l9-11h-7z'],
                ['03','Export & go','Download, copy or share the result. Nothing is uploaded, nothing is stored. You are done.', 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
            ];
            @endphp
            @foreach ($steps as $s)
                <div class="rounded-3xl border border-white/10 bg-white/5 p-7 backdrop-blur-xl">
                    <div class="flex items-center justify-between">
                        <span class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/10 bg-white/5 text-slate-200">{!! $svg($s[3], 'h-5 w-5') !!}</span>
                        <span class="text-4xl font-bold text-white/10">{{ $s[0] }}</span>
                    </div>
                    <h3 class="mt-5 text-lg font-bold text-white">{{ $s[1] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-400">{{ $s[2] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===================== CATEGORIES ===================== --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="rounded-[2rem] border border-white/10 bg-gradient-to-br from-white/[0.06] to-transparent p-8 backdrop-blur-xl sm:p-12">
            <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-2">
                <div>
                    <span class="text-sm font-bold uppercase tracking-widest text-indigo-300">A tool for every task</span>
                    <h2 class="mt-3 text-4xl font-bold tracking-tight text-white sm:text-5xl">Ten categories.<br>One toolbox.</h2>
                    <p class="mt-4 text-slate-400">From compressing a PDF before an email to decoding a JWT while debugging — Quickies has the little utilities that unblock your day.</p>
                    <a href="{{ route('dashboard') }}" class="group mt-8 inline-flex items-center gap-2 rounded-2xl bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                        Browse all tools {!! $svg($arrow, 'h-4 w-4 transition group-hover:translate-x-0.5') !!}
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @php
                    $cats = [
                        ['Documents','M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z','from-rose-500','to-red-500'],
                        ['Images','M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z','from-orange-500','to-amber-500'],
                        ['Developer','M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4','from-violet-500','to-purple-500'],
                        ['Text','M4 6h16M4 12h16M4 18h7','from-sky-500','to-blue-500'],
                        ['Security','M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','from-fuchsia-500','to-pink-500'],
                        ['Design','M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343','from-pink-500','to-rose-500'],
                        ['Web &amp; Network','M21 12a9 9 0 11-18 0 9 9 0 0118 0z M3 12h18M12 3a15 15 0 010 18 15 15 0 010-18','from-cyan-500','to-teal-500'],
                        ['Date &amp; Time','M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','from-amber-500','to-orange-500'],
                        ['VAS &amp; DCB','M3 3v18h18M7 14l3-3 4 4 5-6','from-emerald-500','to-green-500'],
                    ];
                    @endphp
                    @foreach ($cats as $c)
                        <div class="flex flex-col items-center gap-2 rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur transition hover:scale-105 hover:bg-white/10">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br {{ $c[2] }} {{ $c[3] }} shadow-lg">{!! $svg($c[1], 'h-6 w-6 text-white') !!}</span>
                            <span class="text-xs font-semibold text-slate-200">{!! $c[0] !!}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== TESTIMONIALS ===================== --}}
    <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <div class="mb-3 flex items-center justify-center gap-1 text-amber-400">
                @for ($i = 0; $i < 5; $i++) {!! $svg($star, 'h-5 w-5 fill-amber-400 stroke-amber-400') !!} @endfor
            </div>
            <h2 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">Makers can't stop raving</h2>
            <p class="mt-4 text-lg text-slate-400">Join hundreds of thousands who replaced a dozen sketchy websites with one.</p>
        </div>
        @php
        $reviews = [
            ['This replaced eight bookmarked tool sites overnight. The PDF and image tools alone pay for it.','Maya Chen','Product Designer','from-fuchsia-500 to-pink-500'],
            ['The Smart Toolbox is genius — I paste a JWT or a timestamp and it just knows. Ridiculously handy.','Daniel Okoye','Backend Engineer','from-indigo-500 to-blue-500'],
            ['Everything runs locally, so I can use it on client contracts without worrying about uploads.','Sofia Marín','Freelance Consultant','from-emerald-500 to-teal-500'],
            ['Fast, clean, no ads. It feels like a premium product but costs less than a coffee a month.','Liam Novak','Growth Marketer','from-amber-500 to-orange-500'],
            ['New tools show up every month and they are always things I actually needed. Worth every cent.','Priya Nair','Full-stack Developer','from-cyan-500 to-sky-500'],
            ['Our whole team switched. Onboarding was zero — everyone just got it in seconds.','Tom Wright','Engineering Lead','from-violet-500 to-purple-500'],
        ];
        @endphp
        <div class="mt-12 grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($reviews as $r)
                <figure class="flex h-full flex-col rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
                    <div class="mb-3 flex text-amber-400">
                        @for ($i = 0; $i < 5; $i++) {!! $svg($star, 'h-4 w-4 fill-amber-400 stroke-amber-400') !!} @endfor
                    </div>
                    <blockquote class="flex-1 text-sm leading-relaxed text-slate-200">"{{ $r[0] }}"</blockquote>
                    <figcaption class="mt-5 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br {{ $r[3] }} text-sm font-bold text-white">{{ substr($r[1], 0, 1) }}</span>
                        <span>
                            <span class="block text-sm font-bold text-white">{{ $r[1] }}</span>
                            <span class="block text-xs text-slate-400">{{ $r[2] }}</span>
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </section>

    {{-- ===================== PRICING TEASER ===================== --}}
    <section class="mx-auto max-w-5xl px-4 py-16 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <span class="text-sm font-bold uppercase tracking-widest text-indigo-300">Simple pricing</span>
            <h2 class="mt-3 text-4xl font-bold tracking-tight text-white sm:text-5xl">Start free. Upgrade when ready.</h2>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-xl">
                <h3 class="text-lg font-bold text-white">Free</h3>
                <div class="mt-3 flex items-end gap-1"><span class="text-5xl font-bold text-white">{{ config('plans.currency_symbol') }}0</span><span class="pb-1.5 text-sm text-slate-400">forever</span></div>
                <ul class="mt-6 space-y-3 text-sm text-slate-300">
                    @foreach (config('plans.free_features') as $feat)
                        <li class="flex items-start gap-2">{!! $svg($check, 'mt-0.5 h-4 w-4 flex-shrink-0 text-slate-500') !!}{{ $feat }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="mt-8 block rounded-2xl border border-white/10 bg-white/5 py-3 text-center font-bold text-white transition hover:bg-white/10">Start free</a>
            </div>
            <div class="relative overflow-hidden rounded-3xl border border-indigo-400/30 bg-gradient-to-br from-indigo-500/15 to-fuchsia-500/10 p-8 backdrop-blur-xl">
                <span class="absolute right-5 top-5 rounded-full bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-3 py-1 text-xs font-bold text-white">Most popular</span>
                <h3 class="text-lg font-bold text-white">Pro</h3>
                <div class="mt-3 flex items-end gap-1"><span class="text-5xl font-bold text-white">{{ config('plans.currency_symbol') }}{{ config('plans.plans.monthly.price') }}</span><span class="pb-1.5 text-sm text-slate-400">/mo</span></div>
                <ul class="mt-6 space-y-3 text-sm text-slate-200">
                    @foreach (config('plans.pro_features') as $feat)
                        <li class="flex items-start gap-2">{!! $svg($check, 'mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-400') !!}{{ $feat }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('pricing') }}" class="mt-8 block rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 py-3 text-center font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.02]">See all plans</a>
            </div>
        </div>
    </section>

    {{-- ===================== FAQ ===================== --}}
    <section class="mx-auto max-w-3xl px-4 py-16 sm:px-6">
        <div class="text-center">
            <h2 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">Questions, answered</h2>
        </div>
        <div class="mt-10 space-y-3">
            @php
            $faqs = [
                ['Is my data really private?','Yes. Almost every tool runs entirely inside your browser using JavaScript — your files and text are never uploaded to our servers. What you do stays on your device.'],
                ['What do I get for free?','Five starter tools, usable once per day each, with no credit card required. Upgrade to Pro any time for unlimited use of all 80+ tools.'],
                ['Can I cancel anytime?','Absolutely. Manage or cancel your subscription in one click from your account — no emails, no hoops.'],
                ['Do you add new tools?','Constantly. New tools ship every month and are instantly included in your Pro plan at no extra cost.'],
                ['Which payment methods do you accept?','All major cards via Stripe, our secure payments partner. Your card details never touch our servers.'],
            ];
            @endphp
            @foreach ($faqs as $faq)
                <details class="group rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-base font-semibold text-white">
                        {{ $faq[0] }}
                        <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/5 transition group-open:rotate-45">{!! $svg('M12 4v16m8-8H4', 'h-4 w-4') !!}</span>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-400">{{ $faq[1] }}</p>
                </details>
            @endforeach
        </div>
    </section>

    {{-- ===================== FINAL CTA ===================== --}}
    <section class="mx-auto max-w-7xl px-4 pb-24 sm:px-6">
        <div class="relative overflow-hidden rounded-[2.5rem] border border-white/10 bg-gradient-to-br from-fuchsia-600/20 via-indigo-600/20 to-cyan-600/20 p-10 text-center backdrop-blur-xl sm:p-16">
            <div class="pointer-events-none absolute -left-24 -top-24 h-64 w-64 rounded-full bg-fuchsia-500/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -right-24 h-64 w-64 rounded-full bg-indigo-500/30 blur-3xl"></div>
            <div class="relative mb-5 flex items-center justify-center gap-1 text-amber-400">
                @for ($i = 0; $i < 5; $i++) {!! $svg($star, 'h-5 w-5 fill-amber-400 stroke-amber-400') !!} @endfor
            </div>
            <h2 class="relative text-4xl font-bold tracking-tight text-white sm:text-6xl">Ready to work faster?</h2>
            <p class="relative mx-auto mt-4 max-w-xl text-lg text-slate-300">Join 250,000+ makers, keep your data private, and get every tool you need in one tab.</p>
            <div class="relative mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="{{ route('register') }}" class="inline-block rounded-2xl bg-white px-10 py-4 text-base font-bold text-slate-900 shadow-2xl transition hover:scale-[1.03]">Create your free account</a>
                <a href="{{ route('pricing') }}" class="inline-block rounded-2xl border border-white/15 bg-white/5 px-10 py-4 text-base font-bold text-white transition hover:bg-white/10">View pricing</a>
            </div>
        </div>
    </section>
@endsection
