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
            <button onclick="copyOut()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            <button onclick="clearAll()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Clear</button>
            <span id="status" class="ml-auto rounded-lg px-3 py-1 text-sm font-semibold"></span>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Input</label>
                <textarea id="jsonInput" rows="16" spellcheck="false" oninput="validate()"
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
    function parseInput() {
        return JSON.parse(document.getElementById('jsonInput').value);
    }

    function setStatus(ok, msg) {
        const el = document.getElementById('status');
        el.textContent = msg;
        el.className = `ml-auto rounded-lg px-3 py-1 text-sm font-semibold ${ok ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300'}`;
    }

    function validate() {
        const raw = document.getElementById('jsonInput').value.trim();
        if (!raw) { document.getElementById('status').textContent = ''; return false; }
        try { parseInput(); setStatus(true, 'Valid JSON'); return true; }
        catch (e) { setStatus(false, e.message.replace(/^JSON.parse:?\s*/i, '')); return false; }
    }

    function format(indent) {
        const raw = document.getElementById('jsonInput').value.trim();
        if (!raw) return;
        try {
            const obj = JSON.parse(raw);
            const gap = indent === -1 ? '\t' : indent;
            document.getElementById('jsonOutput').value = JSON.stringify(obj, null, gap);
            setStatus(true, 'Valid JSON');
        } catch (e) {
            setStatus(false, e.message.replace(/^JSON.parse:?\s*/i, ''));
            showNotification('Invalid JSON — cannot format.', 'error');
        }
    }

    function copyOut() {
        const val = document.getElementById('jsonOutput').value;
        if (!val) return showNotification('Nothing to copy.', 'error');
        copyToClipboard(val, 'JSON copied');
    }

    function clearAll() {
        document.getElementById('jsonInput').value = '';
        document.getElementById('jsonOutput').value = '';
        document.getElementById('status').textContent = '';
    }
</script>
@endpush
