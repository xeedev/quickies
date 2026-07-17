@extends('layouts.app')

@section('title', 'UTM Builder')
@section('description', 'Build trackable campaign URLs with UTM parameters.')

@section('content')
    <x-tool-header title="UTM Builder" subtitle="Compose clean, trackable campaign URLs with UTM parameters."
        from="from-violet-500" to="to-purple-500" icon="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-1 block text-sm font-semibold text-slate-200">Website URL <span class="text-rose-400">*</span></label>
                <input id="url" oninput="build()" placeholder="https://example.com/landing" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-slate-500 focus:border-violet-400/60 focus:outline-none">
            </div>
            @foreach (['source' => ['Campaign source', 'google, newsletter', true], 'medium' => ['Campaign medium', 'cpc, email, banner', true], 'campaign' => ['Campaign name', 'summer_sale', true], 'term' => ['Campaign term', 'running+shoes', false], 'content' => ['Campaign content', 'logolink, textlink', false]] as $id => $meta)
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-200">utm_{{ $id }} @if($meta[2])<span class="text-rose-400">*</span>@endif</label>
                    <input id="utm_{{ $id }}" oninput="build()" placeholder="{{ $meta[1] }}" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-slate-500 focus:border-violet-400/60 focus:outline-none">
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-200">Generated URL</span>
                <div class="flex gap-2">
                    <button onclick="copyToClipboard(document.getElementById('out').textContent, 'URL copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                    <button onclick="openUrl()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Open</button>
                </div>
            </div>
            <div id="out" class="break-all rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm text-violet-200"></div>
            <p id="warn" class="mt-2 text-sm font-semibold text-amber-300"></p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const val = (id) => document.getElementById(id).value.trim();
    function build() {
        const base = val('url');
        const warn = document.getElementById('warn');
        const params = [];
        ['source', 'medium', 'campaign', 'term', 'content'].forEach((k) => {
            const v = val('utm_' + k);
            if (v) params.push(`utm_${k}=${encodeURIComponent(v)}`);
        });
        if (!base) { document.getElementById('out').textContent = 'Enter a website URL to begin…'; warn.textContent = ''; return; }
        const sep = base.includes('?') ? '&' : '?';
        document.getElementById('out').textContent = params.length ? base + sep + params.join('&') : base;
        const missing = ['source', 'medium', 'campaign'].filter((k) => !val('utm_' + k));
        warn.textContent = missing.length ? `Recommended parameters missing: ${missing.map((m) => 'utm_' + m).join(', ')}` : '';
    }
    function openUrl() { const u = document.getElementById('out').textContent; if (/^https?:/.test(u)) window.open(u, '_blank', 'noopener'); else showNotification('Enter a valid URL first.', 'error'); }
    document.addEventListener('DOMContentLoaded', build);
</script>
@endpush
