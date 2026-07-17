@extends('layouts.app')

@section('title', 'Word Counter')
@section('description', 'Count words, characters, sentences and estimated reading time.')

@section('content')
    <x-tool-header
        title="Word Counter"
        subtitle="Count words, characters, sentences and reading time in real time."
        from="from-sky-500"
        to="to-blue-500"
        icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />

    <div class="space-y-6">
        <div id="statsGrid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6"></div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
            <div class="mb-2 flex items-center justify-between">
                <label class="text-sm font-semibold text-slate-200">Your text</label>
                <button onclick="clearText()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Clear</button>
            </div>
            <textarea id="textInput" rows="14" oninput="analyze()"
                class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm leading-relaxed text-white placeholder-slate-500 transition focus:border-sky-400/60 focus:outline-none focus:ring-2 focus:ring-sky-500/30"
                placeholder="Start typing or paste your text here…"></textarea>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const STATS = [
        { key: 'words', label: 'Words' },
        { key: 'chars', label: 'Characters' },
        { key: 'charsNoSpace', label: 'No spaces' },
        { key: 'sentences', label: 'Sentences' },
        { key: 'paragraphs', label: 'Paragraphs' },
        { key: 'reading', label: 'Reading time' },
    ];

    function buildCards() {
        document.getElementById('statsGrid').innerHTML = STATS.map((s) => `
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur-xl">
                <div id="stat-${s.key}" class="bg-gradient-to-r from-sky-400 to-blue-400 bg-clip-text text-2xl font-bold text-transparent sm:text-3xl">0</div>
                <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400">${s.label}</div>
            </div>`).join('');
    }

    function analyze() {
        const text = document.getElementById('textInput').value;
        const words = (text.match(/\S+/g) || []).length;
        const chars = text.length;
        const charsNoSpace = text.replace(/\s/g, '').length;
        const sentences = (text.match(/[^.!?]+[.!?]+/g) || []).length;
        const paragraphs = text.split(/\n+/).filter((p) => p.trim().length).length;
        const minutes = words / 200;
        const reading = words === 0 ? '0s' : minutes < 1 ? `${Math.ceil(minutes * 60)}s` : `${Math.ceil(minutes)}m`;
        const values = { words, chars, charsNoSpace, sentences, paragraphs, reading };
        Object.entries(values).forEach(([k, v]) => {
            const el = document.getElementById(`stat-${k}`);
            if (el) el.textContent = typeof v === 'number' ? v.toLocaleString() : v;
        });
    }

    function clearText() {
        document.getElementById('textInput').value = '';
        analyze();
    }

    document.addEventListener('DOMContentLoaded', () => { buildCards(); analyze(); });
</script>
@endpush
