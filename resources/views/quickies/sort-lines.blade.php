@extends('layouts.app')

@section('title', 'Sort & Shuffle Lines')
@section('description', 'Sort lines alphabetically or numerically, reverse or shuffle.')

@section('content')
    <x-tool-header title="Sort & Shuffle Lines" subtitle="Reorder lines of text however you need."
        from="from-cyan-500" to="to-sky-500" icon="M3 4h13M3 8h9M3 12h5m4 6l4 4 4-4m-4 4V4" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap gap-2">
            <button onclick="apply('az')" class="rounded-xl bg-gradient-to-r from-cyan-500 to-sky-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">A → Z</button>
            <button onclick="apply('za')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Z → A</button>
            <button onclick="apply('num')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Numeric ↑</button>
            <button onclick="apply('numdesc')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Numeric ↓</button>
            <button onclick="apply('len')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">By length</button>
            <button onclick="apply('reverse')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Reverse</button>
            <button onclick="apply('shuffle')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Shuffle</button>
            <label class="ml-auto flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="ci" class="h-4 w-4 accent-cyan-500"> Case-insensitive</label>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Input</label>
                <textarea id="input" rows="14" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-cyan-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/30" placeholder="One item per line…"></textarea>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-200">Output</label>
                    <button onclick="copyToClipboard(document.getElementById('output').value, 'Copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <textarea id="output" rows="14" readonly class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-sky-100 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function apply(mode) {
        const ci = document.getElementById('ci').checked;
        let lines = document.getElementById('input').value.split('\n');
        const cmp = (a, b) => (ci ? a.toLowerCase() : a).localeCompare(ci ? b.toLowerCase() : b);
        switch (mode) {
            case 'az': lines.sort(cmp); break;
            case 'za': lines.sort((a, b) => cmp(b, a)); break;
            case 'num': lines.sort((a, b) => parseFloat(a) - parseFloat(b)); break;
            case 'numdesc': lines.sort((a, b) => parseFloat(b) - parseFloat(a)); break;
            case 'len': lines.sort((a, b) => a.length - b.length); break;
            case 'reverse': lines.reverse(); break;
            case 'shuffle': for (let i = lines.length - 1; i > 0; i--) { const j = Math.floor(Math.random() * (i + 1)); [lines[i], lines[j]] = [lines[j], lines[i]]; } break;
        }
        document.getElementById('output').value = lines.join('\n');
    }
</script>
@endpush
