@extends('layouts.marketing')

@section('title', 'Pricing')
@section('description', 'One simple subscription unlocks all 80+ Quickies tools. Start free, upgrade anytime.')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="mx-auto max-w-2xl text-center">
        <h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">Unlock everything</h1>
        <p class="mt-4 text-lg text-slate-400">One plan, all 80+ tools, no limits. Cancel whenever you like.</p>
        @if ($trialDays > 0)
            <p class="mt-3 inline-block rounded-full border border-emerald-400/25 bg-emerald-500/[0.08] px-4 py-1.5 text-sm font-medium text-emerald-300">Includes a {{ $trialDays }}-day free trial</p>
        @endif
    </div>

    <div class="mt-12 grid grid-cols-1 gap-3 md:grid-cols-3">
        {{-- Free --}}
        <div class="rounded-2xl border border-white/[0.08] bg-white/[0.02] p-8">
            <h2 class="text-lg font-semibold text-white">Free</h2>
            <p class="mt-1 text-sm text-slate-500">For the occasional quick task.</p>
            <div class="mt-5 text-4xl font-semibold tracking-tight text-white">{{ $symbol }}0</div>
            <ul class="mt-6 space-y-3 text-sm text-slate-300">
                @foreach ($freeFeatures as $feat)
                    <li class="flex items-start gap-2"><svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>{{ $feat }}</li>
                @endforeach
            </ul>
            @auth
                <a href="{{ route('dashboard') }}" class="mt-8 block rounded-xl border border-white/10 bg-white/[0.04] py-3 text-center font-semibold text-white transition hover:bg-white/[0.08]">Go to tools</a>
            @else
                <a href="{{ route('register') }}" class="mt-8 block rounded-xl border border-white/10 bg-white/[0.04] py-3 text-center font-semibold text-white transition hover:bg-white/[0.08]">Start free</a>
            @endauth
        </div>

        {{-- Paid plans --}}
        @foreach ($plans as $key => $plan)
            <div class="relative rounded-2xl border {{ $key === 'yearly' ? 'border-indigo-400/25 bg-indigo-500/[0.06]' : 'border-white/[0.08] bg-white/[0.02]' }} p-8">
                @if (! empty($plan['badge']))
                    <span class="absolute right-5 top-5 rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-900">{{ $plan['badge'] }}</span>
                @endif
                <h2 class="text-lg font-semibold text-white">{{ $plan['name'] }}</h2>
                <p class="mt-1 text-sm text-slate-500">Everything, unlocked.</p>
                <div class="mt-5 flex items-end gap-1">
                    <span class="text-4xl font-semibold tracking-tight text-white">{{ $symbol }}{{ $plan['price'] }}</span>
                    <span class="pb-1 text-sm text-slate-500">/{{ $plan['interval'] }}</span>
                </div>
                <ul class="mt-6 space-y-3 text-sm text-slate-200">
                    @foreach (config('plans.pro_features') as $feat)
                        <li class="flex items-start gap-2"><svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>{{ $feat }}</li>
                    @endforeach
                </ul>
                @auth
                    <form method="POST" action="{{ route('billing.checkout', $key) }}" class="mt-8">
                        @csrf
                        <button type="submit" class="w-full rounded-xl bg-white py-3 text-center font-semibold text-slate-900 transition hover:bg-slate-100">
                            {{ $trialDays > 0 ? "Start {$trialDays}-day trial" : 'Subscribe' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="mt-8 block rounded-xl bg-white py-3 text-center font-semibold text-slate-900 transition hover:bg-slate-100">Get {{ $plan['name'] }}</a>
                @endauth
            </div>
        @endforeach
    </div>

    <p class="mt-10 text-center text-sm text-slate-600">Secure payments by Stripe · Cancel anytime · Instant access</p>
</section>
@endsection
