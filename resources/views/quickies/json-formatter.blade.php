@extends('layouts.app')

@section('title', 'JSON Formatter')
@section('description', 'Beautify, minify and validate JSON in your browser.')

@section('content')
    <x-tool-header
        title="JSON Formatter"
        subtitle="Beautify, minify and validate JSON — nothing leaves your browser."
        from="from-amber-500"
        to="to-yellow-500"
        icon="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button onclick="format(2)" class="rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 px-4 py-2 text-sm font-semibold text-slate-900 shadow transition hover:scale-[1.02] active:scale-95">Beautify</button>
            <button onclick="format(0)" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Minify</button>
            <select id="indent" onchange="format(parseInt(this.value))" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-white focus:border-amber-400/60 focus:outline-none">
                <option value="2" class="bg-slate-900">2 spaces</option>
                <option value="4" class="bg-slate-900">4 spaces</option>
                <option value="-1" class="bg-slate-900">Tabs</option>
            </select>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="sortKeys" onchange="render()" class="h-4 w-4 accent-amber-500"> Sort keys</label>
            <button onclick="copyOut()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            <button onclick="downloadJson()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Download</button>
            <button onclick="clearAll()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Clear</button>
            <span id="status" class="ml-auto rounded-lg px-3 py-1 text-sm font-semibold"></span>
        </div>

        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 font-mono text-sm text-amber-300/70">$</span>
                <input id="jsonPath" oninput="render()" placeholder="Filter by path — e.g. users[0].name or data.items"
                       class="w-full rounded-xl border border-white/10 bg-white/5 py-2 pl-7 pr-3 font-mono text-sm text-white placeholder-slate-500 focus:border-amber-400/60 focus:outline-none">
            </div>
            <span id="jsonStats" class="text-xs text-slate-500"></span>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Input</label>
                <textarea id="jsonInput" rows="16" spellcheck="false" oninput="render()"
                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-amber-400/60 focus:outline-none focus:ring-2 focus:ring-amber-500/30"
                    placeholder='{"hello": "world"}'></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Output</label>
                <textarea id="jsonOutput" rows="16" readonly spellcheck="false"
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-amber-100 focus:outline-none"
                    placeholder="Formatted JSON…"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let currentIndent = 2;

    function setStatus(ok, msg) {
        const el = document.getElementById('status');
        el.textContent = msg;
        el.className = `ml-auto rounded-lg px-3 py-1 text-sm font-semibold ${ok ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'}`;
    }

    function sortValue(v) {
        if (Array.isArray(v)) return v.map(sortValue);
        if (v && typeof v === 'object') {
            const o = {};
            Object.keys(v).sort().forEach((k) => { o[k] = sortValue(v[k]); });
            return o;
        }
        return v;
    }

    function getPath(obj, path) {
        const p = path.trim().replace(/^\$\.?/, '');
        if (!p) return obj;
        const tokens = p.match(/[^.\[\]]+|\[\d+\]/g) || [];
        let cur = obj;
        for (const t of tokens) {
            if (cur == null) return undefined;
            const m = t.match(/^\[(\d+)\]$/);
            cur = m ? cur[+m[1]] : cur[t];
        }
        return cur;
    }

    function computeStats(obj) {
        let keys = 0, depth = 0;
        (function walk(v, d) {
            depth = Math.max(depth, d);
            if (Array.isArray(v)) v.forEach((x) => walk(x, d + 1));
            else if (v && typeof v === 'object') { const ks = Object.keys(v); keys += ks.length; ks.forEach((k) => walk(v[k], d + 1)); }
        })(obj, 0);
        const bytes = new Blob([JSON.stringify(obj)]).size;
        return `${keys.toLocaleString()} keys · depth ${depth} · ${bytes < 1024 ? bytes + ' B' : (bytes / 1024).toFixed(1) + ' KB'}`;
    }

    function render() {
        const raw = document.getElementById('jsonInput').value.trim();
        const out = document.getElementById('jsonOutput');
        const statsEl = document.getElementById('jsonStats');
        if (!raw) { out.value = ''; statsEl.textContent = ''; document.getElementById('status').textContent = ''; return; }
        let obj;
        try { obj = JSON.parse(raw); }
        catch (e) { setStatus(false, e.message.replace(/^JSON.parse:?\s*/i, '')); statsEl.textContent = ''; return; }

        if (document.getElementById('sortKeys').checked) obj = sortValue(obj);
        statsEl.textContent = computeStats(obj);

        const pathVal = document.getElementById('jsonPath').value.trim();
        let result = obj;
        if (pathVal) {
            result = getPath(obj, pathVal);
            if (result === undefined) { out.value = ''; setStatus(false, 'Path not found'); return; }
        }
        const gap = currentIndent === -1 ? '\t' : currentIndent;
        out.value = JSON.stringify(result, null, gap);
        setStatus(true, 'Valid JSON');
    }

    function format(indent) { currentIndent = indent; render(); }

    function copyOut() {
        const val = document.getElementById('jsonOutput').value;
        if (!val) return showNotification('Nothing to copy.', 'error');
        copyToClipboard(val, 'JSON copied');
    }

    function downloadJson() {
        const val = document.getElementById('jsonOutput').value;
        if (!val) return showNotification('Nothing to download.', 'error');
        const blob = new Blob([val], { type: 'application/json' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'data.json';
        a.click();
        URL.revokeObjectURL(a.href);
    }

    function clearAll() {
        document.getElementById('jsonInput').value = '';
        document.getElementById('jsonOutput').value = '';
        document.getElementById('jsonPath').value = '';
        document.getElementById('jsonStats').textContent = '';
        document.getElementById('status').textContent = '';
    }
</script>
@endpush
