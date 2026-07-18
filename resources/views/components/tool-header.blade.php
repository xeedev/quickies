@props([
    'title' => 'Tool',
    'subtitle' => '',
    'from' => 'from-fuchsia-500',
    'to' => 'to-indigo-500',
    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
])

<header class="mb-8 sm:mb-10">
    <a href="/dashboard" class="mb-5 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/[0.03] px-3.5 py-1.5 text-sm font-medium text-slate-400 transition hover:border-white/20 hover:text-white">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Dashboard
    </a>
    <div class="flex items-center gap-4">
        <span class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br {{ $from }} {{ $to }} text-white shadow-lg">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path></svg>
        </span>
        <div class="min-w-0">
            <h1 class="text-2xl font-semibold tracking-tight text-white sm:text-4xl">{{ $title }}</h1>
            @if ($subtitle)
                <p class="mt-1 text-sm text-slate-400 sm:text-base">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
</header>
