@extends('layouts.app')

@section('title', 'Postback URL Builder & Tester')
@section('description', 'Build and fire S2S postback URLs with macro placeholders.')

@section('content')
    <x-tool-header title="Postback URL Builder & Tester" subtitle="Compose S2S postback URLs with macros and fire a test call."
        from="from-amber-500" to="to-orange-500" icon="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-1 block text-sm font-semibold text-slate-200">Base postback URL</label>
        <input id="base" oninput="build()" value="https://tracker.example.com/postback" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 font-mono text-sm text-white focus:border-amber-400/60 focus:outline-none">

        <div class="mt-4 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-200">Parameters</span>
            <button onclick="addParam()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">+ Add</button>
        </div>
        <div id="params" class="mt-2 space-y-2"></div>

        <div class="mt-4">
            <span class="mb-2 block text-sm font-semibold text-slate-200">Common macros <span class="font-normal text-slate-400">(click to insert into last value)</span></span>
            <div class="flex flex-wrap gap-2">
                @foreach (['{click_id}','{payout}','{currency}','{offer_id}','{transaction_id}','{msisdn}','{country}','{timestamp}','{status}'] as $m)
                    <button onclick="insertMacro('{{ $m }}')" class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 font-mono text-xs text-amber-200 transition hover:bg-white/10">{{ $m }}</button>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <div class="mb-2 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-200">Generated URL</span>
                <div class="flex gap-2">
                    <button onclick="copyToClipboard(document.getElementById('out').textContent, 'URL copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                    <button onclick="fireTest()" class="rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 px-3 py-1 text-xs font-semibold text-white transition hover:scale-105">Fire test</button>
                </div>
            </div>
            <div id="out" class="break-all rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm text-amber-200"></div>
            <div id="testResult" class="mt-3"></div>
        </div>

        <div class="mt-6 flex items-start gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-400">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>The test fires a real GET request from your browser. Cross-origin trackers may block reading the response (CORS) — a "sent" result still means the request left your browser.</span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let params = [{ k: 'click_id', v: '{click_id}' }, { k: 'payout', v: '{payout}' }, { k: 'currency', v: 'USD' }];

    function renderParams() {
        document.getElementById('params').innerHTML = params.map((p, i) => `
            <div class="flex items-center gap-2">
                <input value="${p.k.replace(/"/g,'&quot;')}" oninput="updateParam(${i},'k',this.value)" placeholder="key" class="w-1/3 rounded-lg border border-white/10 bg-white/5 px-3 py-2 font-mono text-sm text-white focus:border-amber-400/60 focus:outline-none">
                <input value="${p.v.replace(/"/g,'&quot;')}" oninput="updateParam(${i},'v',this.value)" placeholder="value" class="flex-1 rounded-lg border border-white/10 bg-white/5 px-3 py-2 font-mono text-sm text-white focus:border-amber-400/60 focus:outline-none">
                <button onclick="removeParam(${i})" class="rounded p-1.5 text-rose-300 transition hover:bg-rose-500/20">✕</button>
            </div>`).join('');
    }
    function updateParam(i, key, val) { params[i][key] = val; build(); }
    function addParam() { params.push({ k: '', v: '' }); renderParams(); build(); }
    function removeParam(i) { params.splice(i, 1); renderParams(); build(); }
    function insertMacro(m) { if (params.length) { params[params.length - 1].v = m; renderParams(); build(); } }

    function build() {
        const base = document.getElementById('base').value.trim();
        const qs = params.filter((p) => p.k).map((p) => `${encodeURIComponent(p.k)}=${encodeURIComponent(p.v).replace(/%7B/g,'{').replace(/%7D/g,'}')}`).join('&');
        const sep = base.includes('?') ? '&' : '?';
        document.getElementById('out').textContent = qs ? base + sep + qs : base;
    }

    async function fireTest() {
        const url = document.getElementById('out').textContent;
        const box = document.getElementById('testResult');
        if (!/^https?:/.test(url)) { box.innerHTML = '<div class="rounded-xl border border-rose-400/20 bg-rose-500/10 px-4 py-2 text-sm text-rose-200">Enter a valid http(s) URL first.</div>'; return; }
        box.innerHTML = '<div class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-300">Firing…</div>';
        const start = performance.now();
        try {
            const res = await fetch(url, { method: 'GET', mode: 'cors' });
            const ms = Math.round(performance.now() - start);
            const body = await res.text().catch(() => '');
            box.innerHTML = `<div class="rounded-xl border border-emerald-400/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">Response ${res.status} ${res.statusText} · ${ms}ms${body ? `<pre class="mt-2 max-h-32 overflow-auto rounded bg-black/30 p-2 text-xs text-slate-200">${body.slice(0,500).replace(/</g,'&lt;')}</pre>` : ''}</div>`;
        } catch (e) {
            const ms = Math.round(performance.now() - start);
            // Fallback: fire no-cors so the request still leaves the browser
            try { await fetch(url, { method: 'GET', mode: 'no-cors' }); } catch (_) {}
            box.innerHTML = `<div class="rounded-xl border border-amber-400/20 bg-amber-500/10 px-4 py-3 text-sm text-amber-200">Request sent (~${ms}ms) but the response is hidden by CORS. The tracker still received the call.</div>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => { renderParams(); build(); });
</script>
@endpush
