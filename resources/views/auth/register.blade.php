@extends('layouts.marketing')

@section('title', 'Create account')

@section('content')
<section class="mx-auto flex max-w-md flex-col px-4 py-16 sm:py-24">
    <div class="rounded-2xl border border-white/[0.08] bg-white/[0.02] p-6 sm:p-8">
        <h1 class="text-3xl font-semibold tracking-tight text-white">Create your account</h1>
        <p class="mt-2 text-sm text-slate-400">Start free — 5 tools, no card required.</p>

        @if ($errors->any())
            <div class="mt-5 rounded-xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.attempt') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-200">Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    placeholder="Ada Lovelace">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-200">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    placeholder="you@example.com">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-200">Password</label>
                <input type="password" name="password" required autocomplete="new-password"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    placeholder="At least 8 characters">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-semibold text-slate-200">Confirm password</label>
                <input type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                    placeholder="••••••••">
            </div>
            <button type="submit" class="w-full rounded-xl bg-white px-6 py-3 font-semibold text-slate-900 transition hover:bg-slate-100">
                Create account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">
            Already have an account? <a href="{{ route('login') }}" class="font-semibold text-indigo-300 hover:text-indigo-200">Log in</a>
        </p>
    </div>
</section>
@endsection
