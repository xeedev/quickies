@extends('layouts.app')

@section('title', 'JSON ↔ Excel ↔ CSV')
@section('description', 'Convert data between JSON, Excel (XLSX) and CSV.')

@push('head')
<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
@endpush

@section('content')
    <x-tool-header title="JSON ↔ Excel ↔ CSV" subtitle="Convert tabular data between JSON, Excel and CSV — including file import/export."
        from="from-green-500" to="to-emerald-500" icon="M3 10h18M3 14h18M12 4v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <label class="cursor-pointer rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Import file<input type="file" accept=".xlsx,.xls,.csv,.json" class="hidden" onchange="importFile(event)"></label>
            <span class="text-xs text-slate-500">Excel, CSV or JSON</span>
            <div class="ml-auto flex flex-wrap gap-2">
                <button onclick="download('json')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">↓ JSON</button>
                <button onclick="download('csv')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">↓ CSV</button>
                <button onclick="download('xlsx')" class="rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">↓ Excel</button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <div class="mb-2 flex items-center gap-2">
                    <label class="text-sm font-semibold text-slate-200">Data (JSON or CSV)</label>
                    <select id="inputFormat" onchange="parseInput()" class="ml-auto rounded-lg border border-white/10 bg-slate-900 px-2 py-1 text-xs text-white focus:outline-none"><option value="json" class="bg-slate-900">JSON</option><option value="csv" class="bg-slate-900">CSV</option></select>
                </div>
                <textarea id="input" rows="16" spellcheck="false" oninput="parseInput()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-xs text-white transition focus:border-emerald-400/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
                <p id="status" class="mt-2 text-sm font-semibold"></p>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Preview</label>
                <div id="preview" class="max-h-[420px] overflow-auto rounded-2xl border border-white/10 bg-slate-950/50 p-2"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let rows = [];
    function setStatus(ok, msg) { const el = document.getElementById('status'); el.textContent = msg; el.className = `mt-2 text-sm font-semibold ${ok ? 'text-emerald-300' : 'text-rose-300'}`; }

    function parseInput() {
        const fmt = document.getElementById('inputFormat').value;
        const text = document.getElementById('input').value.trim();
        if (!text) { rows = []; renderPreview(); setStatus(true, ''); return; }
        try {
            if (fmt === 'json') { const data = JSON.parse(text); rows = Array.isArray(data) ? data : [data]; }
            else { const wb = XLSX.read(text, { type: 'string' }); rows = XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]]); }
            setStatus(true, `${rows.length} row(s)`);
            renderPreview();
        } catch (e) { setStatus(false, e.message); }
    }

    function renderPreview() {
        const el = document.getElementById('preview');
        if (!rows.length) { el.innerHTML = '<p class="p-4 text-sm text-slate-500">No data yet.</p>'; return; }
        const cols = [...new Set(rows.flatMap((r) => Object.keys(r)))];
        el.innerHTML = `<table class="w-full text-xs"><thead><tr class="text-left text-slate-400">${cols.map((c) => `<th class="px-2 py-1 font-semibold">${String(c).replace(/</g,'&lt;')}</th>`).join('')}</tr></thead><tbody>${rows.slice(0, 100).map((r, i) => `<tr class="border-t border-white/10 ${i%2?'bg-white/[0.02]':''}">${cols.map((c) => `<td class="px-2 py-1 font-mono text-slate-300">${r[c] == null ? '' : String(r[c]).replace(/</g,'&lt;')}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
    }

    function importFile(e) {
        const file = e.target.files[0]; if (!file) return;
        const reader = new FileReader();
        const isText = /\.(csv|json)$/i.test(file.name);
        reader.onload = (ev) => {
            try {
                if (/\.json$/i.test(file.name)) { document.getElementById('inputFormat').value = 'json'; document.getElementById('input').value = ev.target.result; }
                else { const wb = XLSX.read(ev.target.result, { type: isText ? 'string' : 'array' }); rows = XLSX.utils.sheet_to_json(wb.Sheets[wb.SheetNames[0]]); document.getElementById('inputFormat').value = 'json'; document.getElementById('input').value = JSON.stringify(rows, null, 2); }
                parseInput();
                showNotification(`Imported ${file.name}`, 'success');
            } catch (err) { setStatus(false, 'Import failed: ' + err.message); }
        };
        if (isText) reader.readAsText(file); else reader.readAsArrayBuffer(file);
        e.target.value = '';
    }

    function download(type) {
        if (!rows.length) return showNotification('No data to export.', 'error');
        const ws = XLSX.utils.json_to_sheet(rows);
        if (type === 'json') return saveBlob(JSON.stringify(rows, null, 2), 'data.json', 'application/json');
        if (type === 'csv') return saveBlob(XLSX.utils.sheet_to_csv(ws), 'data.csv', 'text/csv');
        const wb = XLSX.utils.book_new(); XLSX.utils.book_append_sheet(wb, ws, 'Sheet1'); XLSX.writeFile(wb, 'data.xlsx');
        showNotification('Excel downloaded!', 'success');
    }
    function saveBlob(content, name, mime) {
        const blob = new Blob([content], { type: mime });
        const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = name; a.click(); URL.revokeObjectURL(a.href);
        showNotification(name + ' downloaded!', 'success');
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('input').value = '[\n  { "id": 1, "name": "Ada", "plan": "premium", "mrr": 4.99 },\n  { "id": 2, "name": "Grace", "plan": "basic", "mrr": 1.99 }\n]';
        parseInput();
    });
</script>
@endpush
