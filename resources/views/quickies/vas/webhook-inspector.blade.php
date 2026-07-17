@extends('layouts.app')

@section('title', 'Webhook Inspector')
@section('description', 'Inspect and pretty-print incoming webhook payloads.')

@section('content')
    <x-tool-header title="Webhook Inspector" subtitle="Paste a captured webhook and inspect its headers, body and signature."
        from="from-orange-500" to="to-red-500" icon="M13 10V3L4 14h7v7l9-11h-7z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Raw headers <span class="font-normal text-slate-400">(Key: Value per line)</span></label>
                <textarea id="headers" rows="8" spellcheck="false" oninput="analyze()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-xs text-white transition focus:border-orange-400/60 focus:outline-none focus:ring-2 focus:ring-orange-500/30" placeholder="Content-Type: application/json&#10;X-Signature: sha256=..."></textarea>
                <label class="mb-2 mt-4 block text-sm font-semibold text-slate-200">Raw body</label>
                <textarea id="body" rows="8" spellcheck="false" oninput="analyze()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-xs text-white transition focus:border-orange-400/60 focus:outline-none focus:ring-2 focus:ring-orange-500/30" placeholder='{"event":"charge.success","amount":499}'></textarea>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between"><span class="text-sm font-semibold text-slate-200">Parsed</span><button onclick="copyToClipboard(document.getElementById('parsed').textContent, 'Copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button></div>
                <div id="meta" class="mb-3 space-y-1.5"></div>
                <pre id="parsed" class="max-h-[360px] overflow-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-xs text-orange-100"></pre>
            </div>
        </div>
        <div class="mt-6 flex items-start gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-400">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Receiving live webhooks needs a public endpoint — pair this with a bin from <a href="https://webhook.site" target="_blank" rel="noopener" class="font-semibold text-orange-300 underline">webhook.site</a> or <a href="https://requestbin.com" target="_blank" rel="noopener" class="font-semibold text-orange-300 underline">RequestBin</a>, then paste what you capture here.</span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function analyze() {
        const headersRaw = document.getElementById('headers').value;
        const bodyRaw = document.getElementById('body').value.trim();
        const headers = {};
        headersRaw.split('\n').forEach((l) => { const i = l.indexOf(':'); if (i > 0) headers[l.slice(0, i).trim()] = l.slice(i + 1).trim(); });

        const meta = [];
        const ct = headers['Content-Type'] || headers['content-type'] || '—';
        meta.push(['Content-Type', ct]);
        const sig = Object.keys(headers).find((h) => /signature|hmac|hub-signature/i.test(h));
        if (sig) meta.push(['Signature header', `${sig}: ${headers[sig].slice(0, 40)}…`]);
        const evt = headers['X-Event'] || headers['X-GitHub-Event'] || headers['X-Webhook-Event'];
        if (evt) meta.push(['Event', evt]);
        meta.push(['Body size', new Blob([bodyRaw]).size + ' bytes']);

        document.getElementById('meta').innerHTML = meta.map(([k, v]) => `<div class="flex items-center justify-between gap-3 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm"><span class="text-slate-500">${k}</span><span class="min-w-0 truncate font-mono text-slate-200">${String(v).replace(/</g,'&lt;')}</span></div>`).join('');

        const out = document.getElementById('parsed');
        if (!bodyRaw) { out.textContent = ''; return; }
        try { out.textContent = JSON.stringify(JSON.parse(bodyRaw), null, 2); }
        catch (e) {
            try { const params = Object.fromEntries(new URLSearchParams(bodyRaw)); out.textContent = JSON.stringify(params, null, 2) + '\n\n// parsed as form-urlencoded'; }
            catch (_) { out.textContent = bodyRaw; }
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('headers').value = 'Content-Type: application/json\nX-Signature: sha256=8f2a...\nX-Webhook-Event: charge.success';
        document.getElementById('body').value = '{"event":"charge.success","data":{"amount":499,"currency":"USD","msisdn":"+14155552671"}}';
        analyze();
    });
</script>
@endpush
