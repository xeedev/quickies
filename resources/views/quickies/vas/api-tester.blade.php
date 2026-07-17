@extends('layouts.app')

@section('title', 'REST API Tester')
@section('description', 'Send HTTP requests and inspect the response like Postman.')

@section('content')
    <x-tool-header title="REST API Tester" subtitle="Send HTTP requests and inspect the response — right from your browser."
        from="from-indigo-500" to="to-blue-500" icon="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="flex flex-col gap-2 sm:flex-row">
            <select id="method" class="rounded-xl border border-white/10 bg-white/5 px-3 py-3 text-sm font-bold text-white focus:border-indigo-400/60 focus:outline-none">
                @foreach (['GET','POST','PUT','PATCH','DELETE','HEAD','OPTIONS'] as $m)<option class="bg-slate-900">{{ $m }}</option>@endforeach
            </select>
            <input id="url" placeholder="https://api.example.com/endpoint" class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 focus:border-indigo-400/60 focus:outline-none" onkeydown="if(event.key==='Enter')send()">
            <button onclick="send()" class="rounded-xl bg-gradient-to-r from-indigo-500 to-blue-500 px-6 py-3 font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Send</button>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Headers <span class="font-normal text-slate-400">(Key: Value per line)</span></label>
                <textarea id="headers" rows="5" spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-xs text-white focus:border-indigo-400/60 focus:outline-none" placeholder="Content-Type: application/json&#10;Authorization: Bearer ..."></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Body <span class="font-normal text-slate-400">(for POST/PUT/PATCH)</span></label>
                <textarea id="body" rows="5" spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-xs text-white focus:border-indigo-400/60 focus:outline-none" placeholder='{"key":"value"}'></textarea>
            </div>
        </div>

        <div id="response" class="mt-6"></div>

        <div class="mt-6 flex items-start gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-400">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Requests run from your browser, so the target API must allow CORS. For APIs that don't, use a desktop client like curl or Postman.</span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    async function send() {
        const method = document.getElementById('method').value;
        const url = document.getElementById('url').value.trim();
        const box = document.getElementById('response');
        if (!/^https?:/.test(url)) { box.innerHTML = err('Enter a valid http(s) URL.'); return; }

        const headers = {};
        document.getElementById('headers').value.split('\n').forEach((l) => { const i = l.indexOf(':'); if (i > 0) headers[l.slice(0, i).trim()] = l.slice(i + 1).trim(); });
        const body = document.getElementById('body').value.trim();
        const opts = { method, headers };
        if (!['GET', 'HEAD'].includes(method) && body) opts.body = body;

        box.innerHTML = '<div class="rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-300">Sending…</div>';
        const start = performance.now();
        try {
            const res = await fetch(url, opts);
            const ms = Math.round(performance.now() - start);
            const text = await res.text();
            const size = new Blob([text]).size;
            let pretty = text; let lang = 'text';
            try { pretty = JSON.stringify(JSON.parse(text), null, 2); lang = 'json'; } catch (_) {}
            const resHeaders = [...res.headers.entries()].map(([k, v]) => `<div class="flex justify-between gap-3 text-xs"><span class="text-slate-500">${k}</span><span class="min-w-0 truncate font-mono text-slate-300">${String(v).replace(/</g,'&lt;')}</span></div>`).join('');
            const ok = res.ok;
            box.innerHTML = `
                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-lg ${ok ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'} px-3 py-1 text-sm font-bold">${res.status} ${res.statusText}</span>
                    <span class="text-sm text-slate-400">${ms} ms</span>
                    <span class="text-sm text-slate-400">${(size/1024).toFixed(1)} KB</span>
                    <button onclick="copyToClipboard(document.getElementById('respBody').textContent, 'Response copied')" class="ml-auto rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy body</button>
                </div>
                <div class="mt-3 grid grid-cols-1 gap-3 lg:grid-cols-[1fr_260px]">
                    <pre id="respBody" class="max-h-[400px] overflow-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-xs text-indigo-100">${pretty.replace(/</g,'&lt;')}</pre>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4"><div class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400">Response headers</div><div class="space-y-1">${resHeaders || '<span class="text-xs text-slate-500">none exposed</span>'}</div></div>
                </div>`;
        } catch (e) {
            box.innerHTML = err(`Request failed: ${e.message}. The API may not allow cross-origin requests (CORS).`);
        }
    }
    function err(msg) { return `<div class="rounded-xl border border-rose-400/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">${msg}</div>`; }
    document.addEventListener('DOMContentLoaded', () => { document.getElementById('url').value = 'https://httpbin.org/get'; });
</script>
@endpush
