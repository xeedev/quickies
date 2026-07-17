@extends('layouts.app')

@section('title', 'JSON ↔ YAML')
@section('description', 'Convert between JSON and YAML in either direction.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/js-yaml@4.1.0/dist/js-yaml.min.js"></script>
@endpush

@section('content')
    <x-tool-header title="JSON ↔ YAML" subtitle="Convert between JSON and YAML instantly."
        from="from-orange-500" to="to-amber-500" icon="M7 8l-4 4 4 4m10-8l4 4-4 4M14 4l-4 16" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button onclick="toYaml()" class="rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">JSON → YAML</button>
            <button onclick="toJson()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">YAML → JSON</button>
            <span id="status" class="ml-auto text-sm font-semibold"></span>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Left</label>
                <textarea id="left" rows="16" spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-amber-400/60 focus:outline-none focus:ring-2 focus:ring-amber-500/30" placeholder='{"name":"Quickies"}'></textarea>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-200">Right</label>
                    <button onclick="copyToClipboard(document.getElementById('right').value, 'Copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <textarea id="right" rows="16" readonly spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-amber-100 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function setStatus(ok, msg) {
        const el = document.getElementById('status');
        el.textContent = msg;
        el.className = `ml-auto text-sm font-semibold ${ok ? 'text-emerald-300' : 'text-rose-300'}`;
    }
    function toYaml() {
        try {
            const obj = JSON.parse(document.getElementById('left').value);
            document.getElementById('right').value = jsyaml.dump(obj, { indent: 2, lineWidth: -1 });
            setStatus(true, 'Converted to YAML');
        } catch (e) { setStatus(false, 'Invalid JSON: ' + e.message); }
    }
    function toJson() {
        try {
            const obj = jsyaml.load(document.getElementById('left').value);
            document.getElementById('right').value = JSON.stringify(obj, null, 2);
            setStatus(true, 'Converted to JSON');
        } catch (e) { setStatus(false, 'Invalid YAML: ' + e.message); }
    }
    document.addEventListener('DOMContentLoaded', () => { document.getElementById('left').value = '{\n  "name": "Quickies",\n  "tools": ["json", "yaml"],\n  "awesome": true\n}'; toYaml(); });
</script>
@endpush
