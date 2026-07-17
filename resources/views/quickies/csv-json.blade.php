@extends('layouts.app')

@section('title', 'CSV ↔ JSON')
@section('description', 'Convert CSV to JSON and JSON arrays back to CSV.')

@section('content')
    <x-tool-header title="CSV ↔ JSON" subtitle="Convert tabular CSV data to JSON and back."
        from="from-green-500" to="to-teal-500" icon="M3 10h18M3 14h18M12 4v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button onclick="toJson()" class="rounded-xl bg-gradient-to-r from-green-500 to-teal-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">CSV → JSON</button>
            <button onclick="toCsv()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">JSON → CSV</button>
            <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">Delimiter
                <select id="delim" class="rounded-lg border border-white/10 bg-slate-900 px-2 py-1 text-sm text-white focus:outline-none">
                    <option value="," class="bg-slate-900">Comma</option>
                    <option value=";" class="bg-slate-900">Semicolon</option>
                    <option value="\t" class="bg-slate-900">Tab</option>
                </select>
            </label>
            <span id="status" class="ml-auto text-sm font-semibold"></span>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Left</label>
                <textarea id="left" rows="16" spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-teal-400/60 focus:outline-none focus:ring-2 focus:ring-teal-500/30"></textarea>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-200">Right</label>
                    <button onclick="copyToClipboard(document.getElementById('right').value, 'Copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <textarea id="right" rows="16" readonly spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-teal-100 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function delim() { const v = document.getElementById('delim').value; return v === '\\t' ? '\t' : v; }
    function setStatus(ok, msg) { const el = document.getElementById('status'); el.textContent = msg; el.className = `ml-auto text-sm font-semibold ${ok ? 'text-emerald-300' : 'text-rose-300'}`; }

    function parseCsv(text, d) {
        const rows = [];
        let row = [], field = '', inQuotes = false;
        for (let i = 0; i < text.length; i++) {
            const c = text[i];
            if (inQuotes) {
                if (c === '"' && text[i + 1] === '"') { field += '"'; i++; }
                else if (c === '"') inQuotes = false;
                else field += c;
            } else {
                if (c === '"') inQuotes = true;
                else if (c === d) { row.push(field); field = ''; }
                else if (c === '\n') { row.push(field); rows.push(row); row = []; field = ''; }
                else if (c === '\r') { /* skip */ }
                else field += c;
            }
        }
        if (field.length || row.length) { row.push(field); rows.push(row); }
        return rows.filter((r) => r.length && !(r.length === 1 && r[0] === ''));
    }

    function toJson() {
        try {
            const rows = parseCsv(document.getElementById('left').value, delim());
            if (!rows.length) throw new Error('No data');
            const headers = rows[0];
            const out = rows.slice(1).map((r) => Object.fromEntries(headers.map((h, i) => [h, r[i] ?? ''])));
            document.getElementById('right').value = JSON.stringify(out, null, 2);
            setStatus(true, `${out.length} rows`);
        } catch (e) { setStatus(false, e.message); }
    }

    function csvCell(v) {
        v = v == null ? '' : String(v);
        return /["\n\r,;\t]/.test(v) ? '"' + v.replace(/"/g, '""') + '"' : v;
    }
    function toCsv() {
        try {
            const arr = JSON.parse(document.getElementById('left').value);
            if (!Array.isArray(arr)) throw new Error('Expected a JSON array of objects');
            const headers = [...new Set(arr.flatMap((o) => Object.keys(o)))];
            const d = delim();
            const lines = [headers.map(csvCell).join(d)];
            arr.forEach((o) => lines.push(headers.map((h) => csvCell(o[h])).join(d)));
            document.getElementById('right').value = lines.join('\n');
            setStatus(true, `${arr.length} rows`);
        } catch (e) { setStatus(false, e.message); }
    }
    document.addEventListener('DOMContentLoaded', () => { document.getElementById('left').value = 'name,role,active\nAda,Engineer,true\nGrace,Admiral,true'; toJson(); });
</script>
@endpush
