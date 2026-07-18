@extends('layouts.marketing')

@section('title', 'Upgrade to Pro')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="rounded-2xl border border-white/[0.08] bg-white/[0.02] p-8 text-center sm:p-12">
        <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-fuchsia-500 to-indigo-500 shadow-lg">
            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>

        @if ($reason === 'limit')
            <h1 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">You've used today's free run</h1>
            <p class="mx-auto mt-3 max-w-lg text-slate-400">Free tools can be used once per day. Your free runs reset at midnight — or go Pro now for unlimited access to every tool.</p>
        @else
            <h1 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">This is a Pro tool</h1>
            <p class="mx-auto mt-3 max-w-lg text-slate-400">Upgrade to unlock this and all 80+ tools with no daily limits, bigger files and priority speed.</p>
        @endif

        <div class="mx-auto mt-8 grid max-w-md grid-cols-1 gap-2.5 text-left sm:grid-cols-2">
            @foreach (config('plans.pro_features') as $feat)
                <div class="flex items-center gap-2 rounded-xl border border-white/[0.08] bg-white/[0.02] px-3 py-2 text-sm text-slate-200">
                    <svg class="h-4 w-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ $feat }}
                </div>
            @endforeach
        </div>

        <div class="mt-9 flex flex-col items-center justify-center gap-3 sm:flex-row">
            @auth
                <form method="POST" action="{{ route('billing.checkout', 'monthly') }}">
                    @csrf
                    <button class="rounded-xl bg-white px-8 py-3.5 font-semibold text-slate-900 transition hover:bg-slate-100">
                        {{ $trialDays > 0 ? "Start {$trialDays}-day free trial" : 'Upgrade to Pro' }}
                    </button>
                </form>
                <a href="{{ route('pricing') }}" class="rounded-xl border border-white/10 bg-white/[0.03] px-8 py-3.5 font-semibold text-slate-100 transition hover:bg-white/[0.06]">Compare plans</a>
            @else
                <a href="{{ route('register') }}" class="rounded-xl bg-white px-8 py-3.5 font-semibold text-slate-900 transition hover:bg-slate-100">Create free account</a>
                <a href="{{ route('login') }}" class="rounded-xl border border-white/10 bg-white/[0.03] px-8 py-3.5 font-semibold text-slate-100 transition hover:bg-white/[0.06]">Log in</a>
            @endauth
        </div>

        <a href="{{ route('dashboard') }}" class="mt-6 inline-block text-sm font-medium text-slate-400 transition hover:text-white">← Back to tools</a>
    </div>
</section>
@endsection
