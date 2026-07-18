@extends('layouts.marketing')

@section('title', 'All your everyday tools, unlocked')
@section('description', 'Quickies bundles 80+ fast, private, browser-based tools for developers, marketers and creators into one subscription. Try 5 free, upgrade for everything.')

@section('content')
    {{-- Hero --}}
    <section class="relative mx-auto max-w-7xl px-4 pb-10 pt-12 text-center sm:px-6 sm:pt-20">
        <a href="{{ route('pricing') }}" class="group inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1.5 text-xs font-semibold text-slate-300 backdrop-blur transition hover:border-white/20">
            <span class="rounded-full bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-2 py-0.5 text-[10px] font-bold text-white">NEW</span>
            80+ tools · one simple subscription
            <svg class="h-3.5 w-3.5 transition group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
        </a>

        <h1 class="mx-auto mt-8 max-w-4xl text-5xl font-bold leading-[1.05] tracking-tight text-white sm:text-7xl">
            Every little tool you need,
            <span class="animate-gradient bg-gradient-to-r from-fuchsia-400 via-indigo-400 to-cyan-400 bg-clip-text text-transparent">in one place</span>
        </h1>
        <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-400">
            Convert PDFs, crop images, format code, decode tokens, build campaigns and 75 more — all lightning fast and 100% private, right in your browser. Stop juggling sketchy free sites.
        </p>

        <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="{{ route('register') }}" class="w-full rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-8 py-4 text-base font-bold text-white shadow-xl shadow-indigo-500/30 transition hover:scale-[1.03] sm:w-auto">
                Start free — no card needed
            </a>
            <a href="{{ route('dashboard') }}" class="w-full rounded-2xl border border-white/10 bg-white/5 px-8 py-4 text-base font-bold text-slate-100 backdrop-blur-xl transition hover:bg-white/10 sm:w-auto">
                Explore the tools
            </a>
        </div>
        <p class="mt-4 text-sm text-slate-500">Free plan includes 5 tools · Pro from {{ config('plans.currency_symbol') }}{{ config('plans.plans.monthly.price') }}/mo</p>

        {{-- Floating tool chips --}}
        <div class="mx-auto mt-14 flex max-w-3xl flex-wrap items-center justify-center gap-2.5">
            @foreach (['PDF Compressor','Image Cropper','JSON Formatter','JWT Decoder','QR Codes','Word to PDF','Regex Tester','Base64','Color Picker','SQL Formatter','UTM Builder','Markdown → PDF'] as $chip)
                <span class="rounded-full border border-white/10 bg-white/5 px-3.5 py-1.5 text-sm font-medium text-slate-300 backdrop-blur">{{ $chip }}</span>
            @endforeach
        </div>
    </section>

    {{-- Social proof band --}}
    <section class="border-y border-white/10 bg-white/[0.02] py-6">
        <div class="mx-auto grid max-w-5xl grid-cols-2 gap-6 px-4 text-center sm:grid-cols-4">
            <div><div class="text-3xl font-bold text-white">80+</div><div class="text-xs uppercase tracking-wide text-slate-500">Tools</div></div>
            <div><div class="text-3xl font-bold text-white">100%</div><div class="text-xs uppercase tracking-wide text-slate-500">In your browser</div></div>
            <div><div class="text-3xl font-bold text-white">0</div><div class="text-xs uppercase tracking-wide text-slate-500">Uploads to servers</div></div>
            <div><div class="text-3xl font-bold text-white">1</div><div class="text-xs uppercase tracking-wide text-slate-500">Subscription</div></div>
        </div>
    </section>

    {{-- Feature grid --}}
    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold text-white sm:text-4xl">Built for people who ship</h2>
            <p class="mt-3 text-slate-400">Everything you reach for during the day, without the ads, uploads or paywalled downloads.</p>
        </div>
        <div class="mt-12 grid grid-cols-1 gap-5 md:grid-cols-3">
            @php
            $features = [
                ['⚡','Instant & offline','Tools run locally in your browser. No waiting on uploads, no server queues.','from-fuchsia-500','to-pink-500'],
                ['🔒','Truly private','Your files and text never leave your device. Perfect for sensitive data.','from-indigo-500','to-blue-500'],
                ['🧰','One toolbox','Documents, images, developer, marketing and VAS tools — all under one login.','from-emerald-500','to-teal-500'],
                ['📄','Real conversions','PDF ⇄ Word, images ⇄ PDF, JSON ⇄ YAML ⇄ CSV ⇄ Excel and much more.','from-amber-500','to-orange-500'],
                ['🚀','Always improving','New tools ship every month and land in your plan automatically.','from-cyan-500','to-sky-500'],
                ['💳','Fair pricing','One low subscription unlocks everything. Cancel in a click, anytime.','from-violet-500','to-purple-500'],
            ];
            @endphp
            @foreach ($features as $f)
                <div class="group rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl transition hover:-translate-y-1 hover:border-white/20">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br {{ $f[3] }} {{ $f[4] }} text-2xl shadow-lg">{{ $f[0] }}</div>
                    <h3 class="text-lg font-bold text-white">{{ $f[1] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-400">{{ $f[2] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Categories --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6">
        <div class="rounded-3xl border border-white/10 bg-gradient-to-br from-white/[0.06] to-transparent p-8 backdrop-blur-xl sm:p-12">
            <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-2">
                <div>
                    <h2 class="text-3xl font-bold text-white sm:text-4xl">A tool for every task</h2>
                    <p class="mt-3 text-slate-400">From compressing a PDF before an email to decoding a JWT while debugging — Quickies has the little utilities that unblock your day.</p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach (['Documents & PDF','Images','Developer','Text','Security','Design','Web & Network','Date & Time','Generators','VAS & DCB'] as $cat)
                            <span class="rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-sm font-medium text-slate-200">{{ $cat }}</span>
                        @endforeach
                    </div>
                    <a href="{{ route('dashboard') }}" class="mt-8 inline-flex items-center gap-2 rounded-2xl bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/15">
                        Browse all tools
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    @foreach (['📄','🖼️','⚙️','🔤','🔐','🎨','🌐','⏱️','🎲','📱','📊','🔗'] as $emoji)
                        <div class="flex aspect-square items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-3xl backdrop-blur transition hover:scale-105 hover:bg-white/10">{{ $emoji }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing teaser --}}
    <section class="mx-auto max-w-5xl px-4 pb-20 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold text-white sm:text-4xl">Simple, honest pricing</h2>
            <p class="mt-3 text-slate-400">Start free. Upgrade when you need the whole toolbox.</p>
        </div>
        <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-xl">
                <h3 class="text-lg font-bold text-white">Free</h3>
                <div class="mt-3 text-4xl font-bold text-white">{{ config('plans.currency_symbol') }}0</div>
                <ul class="mt-6 space-y-3 text-sm text-slate-300">
                    @foreach (config('plans.free_features') as $feat)
                        <li class="flex items-center gap-2"><svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>{{ $feat }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="mt-8 block rounded-2xl border border-white/10 bg-white/5 py-3 text-center font-bold text-white transition hover:bg-white/10">Start free</a>
            </div>
            <div class="relative overflow-hidden rounded-3xl border border-indigo-400/30 bg-gradient-to-br from-indigo-500/15 to-fuchsia-500/10 p-8 backdrop-blur-xl">
                <span class="absolute right-5 top-5 rounded-full bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-3 py-1 text-xs font-bold text-white">Most popular</span>
                <h3 class="text-lg font-bold text-white">Pro</h3>
                <div class="mt-3 text-4xl font-bold text-white">{{ config('plans.currency_symbol') }}{{ config('plans.plans.monthly.price') }}<span class="text-base font-medium text-slate-400">/mo</span></div>
                <ul class="mt-6 space-y-3 text-sm text-slate-200">
                    @foreach (config('plans.pro_features') as $feat)
                        <li class="flex items-center gap-2"><svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>{{ $feat }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('pricing') }}" class="mt-8 block rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 py-3 text-center font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.02]">See plans</a>
            </div>
        </div>
    </section>

    {{-- Final CTA --}}
    <section class="mx-auto max-w-7xl px-4 pb-24 sm:px-6">
        <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-fuchsia-600/20 via-indigo-600/20 to-cyan-600/20 p-10 text-center backdrop-blur-xl sm:p-16">
            <div class="pointer-events-none absolute -left-20 -top-20 h-60 w-60 rounded-full bg-fuchsia-500/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-20 -right-20 h-60 w-60 rounded-full bg-indigo-500/30 blur-3xl"></div>
            <h2 class="relative text-3xl font-bold text-white sm:text-5xl">Ready to work faster?</h2>
            <p class="relative mx-auto mt-4 max-w-xl text-slate-300">Join today, keep your data private, and get every tool you need in one tab.</p>
            <a href="{{ route('register') }}" class="relative mt-8 inline-block rounded-2xl bg-white px-10 py-4 text-base font-bold text-slate-900 shadow-2xl transition hover:scale-[1.03]">Create your free account</a>
        </div>
    </section>
@endsection
