@extends('layouts.marketing')

@section('title', 'You are all set')

@section('content')
<section class="mx-auto max-w-2xl px-4 py-20 text-center sm:px-6 sm:py-28">
    <div class="rounded-3xl border border-white/10 bg-white/5 p-10 backdrop-blur-xl">
        <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-500/20">
            <svg class="h-9 w-9 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <h1 class="text-3xl font-bold text-white sm:text-4xl">Welcome to Pro! 🎉</h1>
        <p class="mx-auto mt-3 max-w-md text-slate-400">Your subscription is active. Every tool is now unlocked with no daily limits. Thanks for supporting Quickies!</p>
        <a href="{{ route('dashboard') }}" class="mt-8 inline-block rounded-2xl bg-gradient-to-r from-fuchsia-500 to-indigo-500 px-8 py-3.5 font-bold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.03]">Start using every tool</a>
    </div>
</section>
@endsection
