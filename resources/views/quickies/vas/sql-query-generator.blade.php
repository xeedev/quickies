@extends('layouts.app')

@section('title', 'SQL Query Generator')
@section('description', 'Build and format SELECT, INSERT and UPDATE SQL visually.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sql-formatter@15.3.1/dist/sql-formatter.min.js"></script>
@endpush

@section('content')
    <x-tool-header title="SQL Query Generator" subtitle="Assemble and format SQL statements from a simple form."
        from="from-purple-500" to="to-violet-500" icon="M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3-3.582 3-8 3-8-1.343-8-3zm0 0v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <div class="mb-4 inline-flex rounded-2xl border border-white/10 bg-white/5 p-1">
                <button data-t="SELECT" onclick="setType('SELECT')" class="rounded-xl px-4 py-1.5 text-sm font-semibold transition">SELECT</button>
                <button data-t="INSERT" onclick="setType('INSERT')" class="rounded-xl px-4 py-1.5 text-sm font-semibold transition">INSERT</button>
                <button data-t="UPDATE" onclick="setType('UPDATE')" class="rounded-xl px-4 py-1.5 text-sm font-semibold transition">UPDATE</button>
                <button data-t="DELETE" onclick="setType('DELETE')" class="rounded-xl px-4 py-1.5 text-sm font-semibold transition">DELETE</button>
            </div>
            <div class="space-y-3">
                <div><label class="mb-1 block text-xs font-semibold text-slate-300">Table</label><input id="table" value="users" oninput="build()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 font-mono text-sm text-white focus:border-violet-400/60 focus:outline-none"></div>
                <div id="colsWrap"><label class="mb-1 block text-xs font-semibold text-slate-300">Columns <span class="text-slate-500">(comma separated, * for all)</span></label><input id="cols" value="id, name, email" oninput="build()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 font-mono text-sm text-white focus:border-violet-400/60 focus:outline-none"></div>
                <div id="valuesWrap" class="hidden"><label class="mb-1 block text-xs font-semibold text-slate-300">Values / SET <span class="text-slate-500">(col=value per line)</span></label><textarea id="values" rows="4" oninput="build()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 font-mono text-sm text-white focus:border-violet-400/60 focus:outline-none">name='Ada'&#10;email='ada@example.com'</textarea></div>
                <div id="whereWrap"><label class="mb-1 block text-xs font-semibold text-slate-300">WHERE</label><input id="where" value="active = 1" oninput="build()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 font-mono text-sm text-white focus:border-violet-400/60 focus:outline-none"></div>
                <div id="extraWrap" class="grid grid-cols-2 gap-3">
                    <div><label class="mb-1 block text-xs font-semibold text-slate-300">ORDER BY</label><input id="order" value="id DESC" oninput="build()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 font-mono text-sm text-white focus:border-violet-400/60 focus:outline-none"></div>
                    <div><label class="mb-1 block text-xs font-semibold text-slate-300">LIMIT</label><input id="limit" value="100" oninput="build()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 font-mono text-sm text-white focus:border-violet-400/60 focus:outline-none"></div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <div class="mb-2 flex items-center justify-between"><span class="text-sm font-semibold text-slate-200">Generated SQL</span><button onclick="copyToClipboard(document.getElementById('out').textContent, 'SQL copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button></div>
            <pre id="out" class="min-h-[300px] overflow-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm text-violet-100"></pre>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let type = 'SELECT';
    const val = (id) => document.getElementById(id).value.trim();

    function setType(t) {
        type = t;
        document.querySelectorAll('[data-t]').forEach((b) => { const a = b.dataset.t === t; b.className = `rounded-xl px-4 py-1.5 text-sm font-semibold transition ${a ? 'bg-gradient-to-r from-purple-500 to-violet-500 text-white shadow' : 'text-slate-400'}`; });
        document.getElementById('colsWrap').style.display = (t === 'SELECT' || t === 'INSERT') ? 'block' : 'none';
        document.getElementById('valuesWrap').classList.toggle('hidden', t === 'SELECT' || t === 'DELETE');
        document.getElementById('whereWrap').style.display = (t === 'INSERT') ? 'none' : 'block';
        document.getElementById('extraWrap').style.display = (t === 'SELECT') ? 'grid' : 'none';
        build();
    }

    function build() {
        const table = val('table') || 'table';
        let sql = '';
        const setLines = val('values').split('\n').map((l) => l.trim()).filter(Boolean);
        if (type === 'SELECT') {
            sql = `SELECT ${val('cols') || '*'} FROM ${table}`;
            if (val('where')) sql += ` WHERE ${val('where')}`;
            if (val('order')) sql += ` ORDER BY ${val('order')}`;
            if (val('limit')) sql += ` LIMIT ${val('limit')}`;
        } else if (type === 'INSERT') {
            const cols = setLines.map((l) => l.split('=')[0].trim());
            const vals = setLines.map((l) => l.split('=').slice(1).join('=').trim());
            sql = `INSERT INTO ${table} (${cols.join(', ')}) VALUES (${vals.join(', ')})`;
        } else if (type === 'UPDATE') {
            sql = `UPDATE ${table} SET ${setLines.join(', ')}`;
            if (val('where')) sql += ` WHERE ${val('where')}`;
        } else if (type === 'DELETE') {
            sql = `DELETE FROM ${table}`;
            if (val('where')) sql += ` WHERE ${val('where')}`;
        }
        sql += ';';
        try { sql = window.sqlFormatter.format(sql, { keywordCase: 'upper' }); } catch (e) {}
        document.getElementById('out').textContent = sql;
    }
    document.addEventListener('DOMContentLoaded', () => setType('SELECT'));
</script>
@endpush
