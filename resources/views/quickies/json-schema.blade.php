@extends('layouts.app')

@section('title', 'JSON Schema Generator')
@section('description', 'Infer a JSON Schema from an example JSON document.')

@section('content')
    <x-tool-header title="JSON Schema Generator" subtitle="Infer a draft-07 JSON Schema from an example document."
        from="from-amber-500" to="to-orange-500" icon="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 5h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button onclick="generate()" class="rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Generate schema</button>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200"><input type="checkbox" id="required" checked class="h-4 w-4 accent-amber-500"> Mark all keys required</label>
            <button onclick="copyToClipboard(document.getElementById('output').value, 'Schema copied')" class="ml-auto rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            <span id="status" class="text-sm font-semibold"></span>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Example JSON</label>
                <textarea id="input" rows="18" spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-amber-400/60 focus:outline-none focus:ring-2 focus:ring-amber-500/30"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">JSON Schema</label>
                <textarea id="output" rows="18" readonly spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-amber-100 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function infer(value) {
        if (value === null) return { type: 'null' };
        if (Array.isArray(value)) {
            if (!value.length) return { type: 'array', items: {} };
            return { type: 'array', items: infer(value[0]) };
        }
        if (typeof value === 'object') {
            const props = {}; const req = [];
            for (const [k, v] of Object.entries(value)) { props[k] = infer(v); req.push(k); }
            const out = { type: 'object', properties: props };
            if (document.getElementById('required').checked && req.length) out.required = req;
            return out;
        }
        if (typeof value === 'number') return { type: Number.isInteger(value) ? 'integer' : 'number' };
        if (typeof value === 'boolean') return { type: 'boolean' };
        if (typeof value === 'string') {
            if (/^\d{4}-\d{2}-\d{2}T/.test(value)) return { type: 'string', format: 'date-time' };
            if (/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(value)) return { type: 'string', format: 'email' };
            if (/^https?:\/\//.test(value)) return { type: 'string', format: 'uri' };
            return { type: 'string' };
        }
        return {};
    }

    function generate() {
        const status = document.getElementById('status');
        try {
            const data = JSON.parse(document.getElementById('input').value);
            const schema = Object.assign({ $schema: 'http://json-schema.org/draft-07/schema#' }, infer(data));
            document.getElementById('output').value = JSON.stringify(schema, null, 2);
            status.textContent = 'Schema generated'; status.className = 'text-sm font-semibold text-emerald-300';
        } catch (e) {
            status.textContent = 'Invalid JSON'; status.className = 'text-sm font-semibold text-rose-300';
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('input').value = '{\n  "id": 42,\n  "name": "Ada",\n  "email": "ada@example.com",\n  "active": true,\n  "roles": ["admin", "user"],\n  "created": "2024-01-01T00:00:00Z"\n}';
        generate();
    });
</script>
@endpush
