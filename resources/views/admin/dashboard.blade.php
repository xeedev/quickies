@extends('layouts.app')

@section('title', 'Admin')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Admin dashboard</h1>
            <p class="mt-1 text-sm text-slate-400">Overview of users, subscriptions and tool usage.</p>
        </div>
        <a href="{{ route('admin.users') }}" class="rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.02]">Manage users</a>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-5">
        @php
        $stats = [
            ['Total users', number_format($totalUsers), 'from-fuchsia-500','to-pink-500'],
            ['Active subs', number_format($activeSubs), 'from-emerald-500','to-teal-500'],
            ['Est. MRR', $symbol.number_format($mrr, 0), 'from-indigo-500','to-blue-500'],
            ['Uses today', number_format($usageToday), 'from-amber-500','to-orange-500'],
            ['Uses · 7d', number_format($usage7d), 'from-cyan-500','to-sky-500'],
        ];
        @endphp
        @foreach ($stats as $s)
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="bg-gradient-to-r {{ $s[2] }} {{ $s[3] }} bg-clip-text text-3xl font-bold text-transparent">{{ $s[1] }}</div>
                <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $s[0] }}</div>
            </div>
        @endforeach
    </div>

    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-300">Newest users</h2>
            <div class="space-y-2">
                @forelse ($recentUsers as $u)
                    <div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-2.5">
                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-white">{{ $u->name }}</div>
                            <div class="truncate text-xs text-slate-500">{{ $u->email }}</div>
                        </div>
                        <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-0.5 text-xs font-semibold text-slate-300">{{ $u->planLabel() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No users yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur-xl">
            <h2 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-300">Top tools · 30 days</h2>
            <div class="space-y-2">
                @forelse ($topTools as $t)
                    <div class="flex items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-2.5">
                        <span class="font-mono text-sm text-slate-200">{{ $t->tool_slug }}</span>
                        <span class="rounded-full bg-indigo-500/20 px-2.5 py-0.5 text-xs font-bold text-indigo-200">{{ $t->hits }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No usage recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
