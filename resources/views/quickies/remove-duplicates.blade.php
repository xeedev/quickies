@extends('layouts.app')

@section('title', 'Remove Duplicate Lines')
@section('description', 'Strip duplicate lines and clean up whitespace.')

@section('content')
    <x-tool-header title="Remove Duplicate Lines" subtitle="Deduplicate, trim and tidy up lists of text."
        from="from-teal-500" to="to-emerald-500" icon="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap gap-2">
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="ignoreCase" onchange="run()" class="h-4 w-4 accent-teal-500"> Ignore case</label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="trim" checked onchange="run()" class="h-4 w-4 accent-teal-500"> Trim whitespace</label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="removeEmpty" checked onchange="run()" class="h-4 w-4 accent-teal-500"> Remove empty lines</label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="sort" onchange="run()" class="h-4 w-4 accent-teal-500"> Sort result</label>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Input</label>
                <textarea id="input" rows="14" oninput="run()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-teal-400/60 focus:outline-none focus:ring-2 focus:ring-teal-500/30" placeholder="One item per line…"></textarea>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-200">Output <span id="stats" class="ml-2 font-normal text-slate-400"></span></label>
                    <button onclick="copyToClipboard(document.getElementById('output').value, 'Copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <textarea id="output" rows="14" readonly class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-emerald-100 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function run() {
        let lines = document.getElementById('input').value.split('\n');
        const ignoreCase = document.getElementById('ignoreCase').checked;
        const trim = document.getElementById('trim').checked;
        const removeEmpty = document.getElementById('removeEmpty').checked;
        const sort = document.getElementById('sort').checked;
        const total = lines.length;
        if (trim) lines = lines.map((l) => l.trim());
        if (removeEmpty) lines = lines.filter((l) => l.length);
        const seen = new Set();
        const out = [];
        for (const l of lines) {
            const key = ignoreCase ? l.toLowerCase() : l;
            if (!seen.has(key)) { seen.add(key); out.push(l); }
        }
        if (sort) out.sort((a, b) => a.localeCompare(b));
        document.getElementById('output').value = out.join('\n');
        document.getElementById('stats').textContent = `${out.length} kept · ${total - out.length} removed`;
    }
    document.addEventListener('DOMContentLoaded', run);
</script>
@endpush
