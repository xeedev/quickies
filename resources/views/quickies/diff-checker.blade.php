@extends('layouts.app')

@section('title', 'Diff Checker')
@section('description', 'Compare two texts and highlight added and removed lines.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/diff@5.2.0/dist/diff.min.js"></script>
@endpush

@section('content')
    <x-tool-header
        title="Diff Checker"
        subtitle="Compare two blocks of text and highlight the differences."
        from="from-emerald-500"
        to="to-teal-500"
        icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Original</label>
                <textarea id="textA" rows="10" spellcheck="false" oninput="runDiff()"
                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-emerald-400/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                    placeholder="Paste the original text…"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Changed</label>
                <textarea id="textB" rows="10" spellcheck="false" oninput="runDiff()"
                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-emerald-400/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"
                    placeholder="Paste the changed text…"></textarea>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10">
                <input type="checkbox" id="ignoreCase" onchange="runDiff()" class="h-4 w-4 accent-emerald-500"> Ignore case
            </label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10">
                <input type="checkbox" id="wordMode" onchange="runDiff()" class="h-4 w-4 accent-emerald-500"> Word-level diff
            </label>
            <div id="summary" class="ml-auto flex gap-3 text-sm font-semibold"></div>
        </div>

        <div id="diffOutput" class="mt-5 overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm leading-relaxed"></div>
    </div>
@endsection

@push('scripts')
<script>
    function runDiff() {
        let a = document.getElementById('textA').value;
        let b = document.getElementById('textB').value;
        const ignoreCase = document.getElementById('ignoreCase').checked;
        const wordMode = document.getElementById('wordMode').checked;
        if (ignoreCase) { a = a.toLowerCase(); b = b.toLowerCase(); }

        const out = document.getElementById('diffOutput');
        if (!a && !b) { out.innerHTML = '<span class="text-slate-500">Enter text in both boxes to see the diff.</span>'; document.getElementById('summary').innerHTML = ''; return; }

        const parts = wordMode ? Diff.diffWords(a, b) : Diff.diffLines(a, b);
        let added = 0, removed = 0;
        const html = parts.map((p) => {
            const safe = p.value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            if (p.added) { added += p.count || 1; return `<span class="rounded bg-emerald-500/20 text-emerald-200">${safe}</span>`; }
            if (p.removed) { removed += p.count || 1; return `<span class="rounded bg-rose-500/20 text-rose-200 line-through decoration-rose-400/50">${safe}</span>`; }
            return `<span class="text-slate-400">${safe}</span>`;
        }).join('');
        out.innerHTML = `<pre class="whitespace-pre-wrap break-words">${html}</pre>`;
        document.getElementById('summary').innerHTML =
            `<span class="text-emerald-300">+${added} added</span><span class="text-rose-300">-${removed} removed</span>`;
    }

    document.addEventListener('DOMContentLoaded', runDiff);
</script>
@endpush
