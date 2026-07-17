@extends('layouts.app')

@section('title', 'JSON Diff Viewer')
@section('description', 'Compare two JSON objects and see what changed.')

@section('content')
    <x-tool-header title="JSON Diff Viewer" subtitle="Compare two JSON documents and highlight added, removed and changed values."
        from="from-emerald-500" to="to-green-500" icon="M7 8h10M7 12h4m-4 4h10M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex items-center gap-3">
            <button onclick="run()" class="rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Compare</button>
            <div id="summary" class="ml-auto flex gap-3 text-sm font-semibold"></div>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Original</label>
                <textarea id="a" rows="12" spellcheck="false" oninput="run()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-emerald-400/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Changed</label>
                <textarea id="b" rows="12" spellcheck="false" oninput="run()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-emerald-400/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
            </div>
        </div>
        <div id="diff" class="mt-5 overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm"></div>
    </div>
@endsection

@push('scripts')
<script>
    let counts;
    const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;');

    function diff(a, b, path = '') {
        let rows = [];
        const keys = [...new Set([...(a && typeof a === 'object' ? Object.keys(a) : []), ...(b && typeof b === 'object' ? Object.keys(b) : [])])];
        if (typeof a !== 'object' || a === null || typeof b !== 'object' || b === null || Array.isArray(a) !== Array.isArray(b)) {
            if (JSON.stringify(a) !== JSON.stringify(b)) { counts.changed++; rows.push(line('~', path, `${fmt(a)} → ${fmt(b)}`, 'amber')); }
            return rows;
        }
        keys.forEach((k) => {
            const p = path ? `${path}.${k}` : k;
            const inA = a && Object.prototype.hasOwnProperty.call(a, k);
            const inB = b && Object.prototype.hasOwnProperty.call(b, k);
            if (inA && !inB) { counts.removed++; rows.push(line('-', p, fmt(a[k]), 'rose')); }
            else if (!inA && inB) { counts.added++; rows.push(line('+', p, fmt(b[k]), 'emerald')); }
            else if (typeof a[k] === 'object' && a[k] && typeof b[k] === 'object' && b[k]) rows = rows.concat(diff(a[k], b[k], p));
            else if (JSON.stringify(a[k]) !== JSON.stringify(b[k])) { counts.changed++; rows.push(line('~', p, `${fmt(a[k])} → ${fmt(b[k])}`, 'amber')); }
        });
        return rows;
    }
    const fmt = (v) => v === undefined ? '∅' : JSON.stringify(v);
    function line(sign, path, val, color) {
        const c = { rose: 'text-rose-300 bg-rose-500/10', emerald: 'text-emerald-300 bg-emerald-500/10', amber: 'text-amber-300 bg-amber-500/10' }[color];
        return `<div class="rounded px-2 py-1 ${c}"><span class="font-bold">${sign}</span> ${esc(path)}: ${esc(val)}</div>`;
    }

    function run() {
        const out = document.getElementById('diff');
        counts = { added: 0, removed: 0, changed: 0 };
        let a, b;
        try { a = JSON.parse(document.getElementById('a').value); } catch (e) { out.innerHTML = '<span class="text-rose-300">Original is not valid JSON</span>'; return; }
        try { b = JSON.parse(document.getElementById('b').value); } catch (e) { out.innerHTML = '<span class="text-rose-300">Changed is not valid JSON</span>'; return; }
        const rows = diff(a, b);
        out.innerHTML = rows.length ? rows.join('') : '<span class="text-slate-500">No differences — the two objects are identical.</span>';
        document.getElementById('summary').innerHTML = `<span class="text-emerald-300">+${counts.added}</span><span class="text-rose-300">-${counts.removed}</span><span class="text-amber-300">~${counts.changed}</span>`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('a').value = '{\n  "name": "Ada",\n  "age": 36,\n  "roles": ["admin"]\n}';
        document.getElementById('b').value = '{\n  "name": "Ada Lovelace",\n  "age": 36,\n  "active": true\n}';
        run();
    });
</script>
@endpush
