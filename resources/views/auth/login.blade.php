@extends('layouts.marketing')

@section('title', 'Log in')

@section('content')
<section class="mx-auto flex max-w-md flex-col px-4 py-16 sm:py-24">
    <div class="rounded-2xl border border-white/[0.08] bg-white/[0.02] p-6 sm:p-8">
        <h1 class="text-3xl font-semibold tracking-tight text-white">Welcome back</h1>
        <p class="mt-2 text-sm text-slate-400">Log in to unlock your Quickies toolbox.</p>

        @if ($errors->any())
            <div class="mt-5 rounded-xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-200">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    placeholder="you@example.com">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-200">Password</label>
                <input type="password" name="password" required autocomplete="current-password"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    placeholder="••••••••">
            </div>
            <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-300">
                <input type="checkbox" name="remember" class="h-4 w-4 accent-indigo-500"> Remember me
            </label>
            <button type="submit" class="w-full rounded-xl bg-white px-6 py-3 font-semibold text-slate-900 transition hover:bg-slate-100">
                Log in
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            New here? <a href="{{ route('register') }}" class="font-semibold text-indigo-300 hover:text-indigo-200">Create an account</a>
        </p>
    </div>
</section>
@endsection
