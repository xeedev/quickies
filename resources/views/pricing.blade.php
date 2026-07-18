@extends('layouts.marketing')

@section('title', 'Pricing')
@section('description', 'One simple subscription unlocks all 80+ Quickies tools. Start free, upgrade anytime.')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="mx-auto max-w-2xl text-center">
        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl">Unlock everything</h1>
        <p class="mt-4 text-lg text-slate-400">One plan, all 80+ tools, no limits. Cancel whenever you like.</p>
        @if ($trialDays > 0)
            <p class="mt-2 inline-block rounded-full border border-emerald-400/30 bg-emerald-500/10 px-4 py-1.5 text-sm font-semibold text-emerald-300">Includes a {{ $trialDays }}-day free trial</p>
        @endif
    </div>

    <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- Free --}}
        <div class="rounded-3xl border border-white/10 bg-white/5 p-8 backdrop-blur-xl">
            <h2 class="text-lg font-bold text-white">Free</h2>
            <p class="mt-1 text-sm text-slate-400">For the occasional quick task.</p>
            <div class="mt-5 text-4xl font-bold text-white">{{ $symbol }}0</div>
            <ul class="mt-6 space-y-3 text-sm text-slate-300">
                @foreach ($freeFeatures as $feat)
                    <li class="flex items-start gap-2"><svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>{{ $feat }}</li>
                @endforeach
            </ul>
            @auth
                <a href="{{ route('dashboard') }}" class="mt-8 block rounded-2xl border border-white/10 bg-white/5 py-3 text-center font-bold text-white transition hover:bg-white/10">Go to tools</a>
            @else
                <a href="{{ route('register') }}" class="mt-8 block rounded-2xl border border-white/10 bg-white/5 py-3 text-center font-bold text-white transition hover:bg-white/10">Start free</a>
            @endauth
        </div>

        {{-- Paid plans --}}
        @foreach ($plans as $key => $plan)
            <div class="relative overflow-hidden rounded-3xl border {{ $key === 'yearly' ? 'border-indigo-400/40 bg-gradient-to-br from-indigo-500/15 to-fuchsia-500/10' : 'border-white/10 bg-white/5' }} p-8 backdrop-blur-xl">
                @if (! empty($plan['badge']))
                    <span class="absolute right-5 top-5 rounded-full bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-3 py-1 text-xs font-bold text-white">{{ $plan['badge'] }}</span>
                @endif
                <h2 class="text-lg font-bold text-white">{{ $plan['name'] }}</h2>
                <p class="mt-1 text-sm text-slate-400">Everything, unlocked.</p>
                <div class="mt-5 flex items-end gap-1">
                    <span class="text-4xl font-bold text-white">{{ $symbol }}{{ $plan['price'] }}</span>
                    <span class="pb-1 text-sm text-slate-400">/{{ $plan['interval'] }}</span>
                </div>
                <ul class="mt-6 space-y-3 text-sm text-slate-200">
                    @foreach (config('plans.pro_features') as $feat)
                        <li class="flex items-start gap-2"><svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>{{ $feat }}</li>
                    @endforeach
                </ul>
                @auth
                    <form method="POST" action="{{ route('billing.checkout', $key) }}" class="mt-8">
                        @csrf
                        <button type="submit" class="w-full rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 py-3 text-center font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.02]">
                            {{ $trialDays > 0 ? "Start {$trialDays}-day trial" : 'Subscribe' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="mt-8 block rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 py-3 text-center font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.02]">Get {{ $plan['name'] }}</a>
                @endauth
            </div>
        @endforeach
    </div>

    <p class="mt-10 text-center text-sm text-slate-500">Secure payments by Stripe · Cancel anytime · Instant access</p>
</section>
@endsection
