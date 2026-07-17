@extends('layouts.app')

@section('title', 'SQL Formatter')
@section('description', 'Beautify and standardise SQL queries for many dialects.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/sql-formatter@15.3.1/dist/sql-formatter.min.js"></script>
@endpush

@section('content')
    <x-tool-header title="SQL Formatter" subtitle="Beautify messy SQL into clean, readable queries."
        from="from-indigo-500" to="to-violet-500" icon="M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3-3.582 3-8 3-8-1.343-8-3zm0 0v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button onclick="format()" class="rounded-xl bg-gradient-to-r from-indigo-500 to-violet-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Format</button>
            <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">Dialect
                <select id="dialect" onchange="format()" class="rounded-lg border border-white/10 bg-slate-900 px-2 py-1 text-sm text-white focus:outline-none">
                    <option value="sql" class="bg-slate-900">Standard</option>
                    <option value="mysql" class="bg-slate-900">MySQL</option>
                    <option value="postgresql" class="bg-slate-900">PostgreSQL</option>
                    <option value="sqlite" class="bg-slate-900">SQLite</option>
                    <option value="mariadb" class="bg-slate-900">MariaDB</option>
                    <option value="bigquery" class="bg-slate-900">BigQuery</option>
                    <option value="tsql" class="bg-slate-900">SQL Server</option>
                </select>
            </label>
            <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">Keywords
                <select id="keywordCase" onchange="format()" class="rounded-lg border border-white/10 bg-slate-900 px-2 py-1 text-sm text-white focus:outline-none">
                    <option value="upper" class="bg-slate-900">UPPER</option>
                    <option value="lower" class="bg-slate-900">lower</option>
                    <option value="preserve" class="bg-slate-900">Preserve</option>
                </select>
            </label>
            <button onclick="copyToClipboard(document.getElementById('output').value, 'SQL copied')" class="ml-auto rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Input</label>
                <textarea id="input" rows="16" spellcheck="false" oninput="format()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-violet-400/60 focus:outline-none focus:ring-2 focus:ring-violet-500/30"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Formatted</label>
                <textarea id="output" rows="16" readonly spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-violet-100 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function format() {
        const sql = document.getElementById('input').value;
        if (!sql.trim()) { document.getElementById('output').value = ''; return; }
        try {
            document.getElementById('output').value = window.sqlFormatter.format(sql, {
                language: document.getElementById('dialect').value,
                keywordCase: document.getElementById('keywordCase').value,
                tabWidth: 2,
            });
        } catch (e) { document.getElementById('output').value = '⚠ ' + e.message; }
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('input').value = "select u.id,u.name,count(o.id) as orders from users u left join orders o on o.user_id=u.id where u.active=1 group by u.id,u.name having count(o.id)>3 order by orders desc limit 10;";
        format();
    });
</script>
@endpush
