@extends('layouts.app')

@section('title', 'Redirect Chain Checker')
@section('description', 'Trace redirect chains and inspect each hop of a URL.')

@section('content')
    <x-tool-header title="Redirect Chain Checker" subtitle="Follow a URL through its redirect hops to the final destination."
        from="from-red-500" to="to-rose-500" icon="M14 5l7 7m0 0l-7 7m7-7H3" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="flex flex-col gap-3 sm:flex-row">
            <input id="url" placeholder="https://example.com/redirect" class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 focus:border-rose-400/60 focus:outline-none" onkeydown="if(event.key==='Enter')check()">
            <button onclick="check()" class="rounded-xl bg-gradient-to-r from-red-500 to-rose-500 px-6 py-3 font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Trace</button>
        </div>
        <div class="mt-3 flex items-center gap-2">
            <label class="text-sm text-slate-300">Method</label>
            <select id="method" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-white focus:outline-none"><option class="bg-slate-900">HEAD</option><option class="bg-slate-900">GET</option></select>
        </div>
        <div id="result" class="mt-6"></div>
        <div class="mt-6 flex items-start gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-400">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Browsers hide cross-origin redirect details for security, so full hop-by-hop tracing needs a server. This tool reports what the browser exposes and detects when a redirect occurred.</span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    async function check() {
        const url = document.getElementById('url').value.trim();
        const method = document.getElementById('method').value;
        const box = document.getElementById('result');
        if (!/^https?:/.test(url)) { box.innerHTML = '<div class="rounded-xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">Enter a valid http(s) URL.</div>'; return; }
        box.innerHTML = '<div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">Tracing…</div>';
        const start = performance.now();
        try {
            const res = await fetch(url, { method, redirect: 'follow' });
            const ms = Math.round(performance.now() - start);
            const redirected = res.redirected;
            box.innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                        <span class="rounded-lg bg-slate-500/20 px-2 py-0.5 text-xs font-bold text-slate-300">START</span>
                        <span class="min-w-0 flex-1 truncate font-mono text-sm text-slate-300">${url.replace(/</g,'&lt;')}</span>
                    </div>
                    ${redirected ? '<div class="pl-6 text-slate-500">↓ redirected</div>' : ''}
                    <div class="flex items-center gap-3 rounded-xl border ${res.ok ? 'border-emerald-400/20' : 'border-amber-400/20'} bg-white/5 px-4 py-3">
                        <span class="rounded-lg ${res.ok ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300'} px-2 py-0.5 text-xs font-bold">${res.status}</span>
                        <span class="min-w-0 flex-1 truncate font-mono text-sm text-white">${res.url.replace(/</g,'&lt;')}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2 sm:grid-cols-4">
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"><div class="text-lg font-bold text-white">${res.status}</div><div class="text-xs text-slate-400">Status</div></div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"><div class="text-lg font-bold text-white">${redirected ? 'Yes' : 'No'}</div><div class="text-xs text-slate-400">Redirected</div></div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"><div class="text-lg font-bold text-white">${ms}ms</div><div class="text-xs text-slate-400">Time</div></div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"><div class="text-lg font-bold text-white">${res.type}</div><div class="text-xs text-slate-400">Type</div></div>
                    </div>
                </div>`;
        } catch (e) {
            box.innerHTML = `<div class="rounded-xl border border-amber-400/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">Could not complete the request from the browser (likely CORS or network). Final URL and redirect details are not exposed for this origin.</div>`;
        }
    }
    document.addEventListener('DOMContentLoaded', () => { document.getElementById('url').value = 'https://httpbin.org/redirect/2'; });
</script>
@endpush
