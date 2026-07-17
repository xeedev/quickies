@extends('layouts.app')

@section('title', 'Query String Parser')
@section('description', 'Break a URL query string into readable key/value pairs.')

@section('content')
    <x-tool-header title="Query String Parser" subtitle="Split a URL into its parts and decode every query parameter."
        from="from-indigo-500" to="to-blue-500" icon="M8 9l3 3-3 3m5 0h3M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">URL or query string</label>
        <textarea id="input" rows="3" oninput="parse()" spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-blue-400/60 focus:outline-none focus:ring-2 focus:ring-blue-500/30" placeholder="https://example.com/path?utm_source=news&ids=1,2,3#section"></textarea>

        <div id="urlParts" class="mt-6 grid grid-cols-1 gap-2 sm:grid-cols-2"></div>

        <div class="mt-6 flex items-center justify-between">
            <h3 class="text-sm font-bold uppercase tracking-wide text-slate-300">Query parameters</h3>
            <button onclick="copyJson()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy as JSON</button>
        </div>
        <div id="params" class="mt-3 overflow-hidden rounded-2xl border border-white/10"></div>
    </div>
@endsection

@push('scripts')
<script>
    let currentParams = {};
    const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;');

    function parse() {
        const raw = document.getElementById('input').value.trim();
        const partsEl = document.getElementById('urlParts');
        const paramsEl = document.getElementById('params');
        currentParams = {};
        if (!raw) { partsEl.innerHTML = ''; paramsEl.innerHTML = ''; return; }

        let search = raw, url = null;
        try { url = new URL(raw); search = url.search; } catch (e) { if (raw.includes('?')) search = raw.slice(raw.indexOf('?')); }

        if (url) {
            const rows = [['Protocol', url.protocol], ['Host', url.host], ['Path', url.pathname], ['Hash', url.hash || '—']];
            partsEl.innerHTML = rows.map(([k, v]) => `<div class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm"><span class="text-slate-500">${k}:</span> <span class="font-mono text-slate-200">${esc(v)}</span></div>`).join('');
        } else partsEl.innerHTML = '';

        const sp = new URLSearchParams(search.startsWith('?') ? search : '?' + search);
        const entries = [...sp.entries()];
        if (!entries.length) { paramsEl.innerHTML = '<div class="p-4 text-sm text-slate-500">No query parameters found.</div>'; return; }
        entries.forEach(([k, v]) => { currentParams[k] = currentParams[k] !== undefined ? [].concat(currentParams[k], v) : v; });
        paramsEl.innerHTML = `<table class="w-full text-sm"><thead><tr class="bg-white/5 text-left text-xs uppercase tracking-wide text-slate-400"><th class="px-4 py-2">Key</th><th class="px-4 py-2">Value</th></tr></thead><tbody>${entries.map(([k, v], i) => `<tr class="border-t border-white/10 ${i % 2 ? 'bg-white/[0.02]' : ''}"><td class="px-4 py-2 font-mono text-blue-200">${esc(k)}</td><td class="break-all px-4 py-2 font-mono text-slate-200">${esc(v) || '<span class="text-slate-600">(empty)</span>'}</td></tr>`).join('')}</tbody></table>`;
    }

    function copyJson() {
        if (!Object.keys(currentParams).length) return showNotification('No parameters to copy.', 'error');
        copyToClipboard(JSON.stringify(currentParams, null, 2), 'JSON copied');
    }
    document.addEventListener('DOMContentLoaded', () => { document.getElementById('input').value = 'https://example.com/path?utm_source=newsletter&utm_medium=email&ids=1,2,3#top'; parse(); });
</script>
@endpush
