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
        <div id="statsGrid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5"></div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8 lg:col-span-2">
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-200">Your text</label>
                    <button onclick="clearText()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Clear</button>
                </div>
                <textarea id="textInput" rows="16" oninput="analyze()"
                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm leading-relaxed text-white placeholder-slate-500 transition focus:border-sky-400/60 focus:outline-none focus:ring-2 focus:ring-sky-500/30"
                    placeholder="Start typing or paste your text here…"></textarea>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-slate-200">Keyword density</h3>
                    <label class="flex cursor-pointer items-center gap-1.5 text-xs text-slate-400"><input type="checkbox" id="stopwords" checked onchange="analyze()" class="h-3.5 w-3.5 accent-sky-500"> hide common</label>
                </div>
                <div id="keywords" class="space-y-2"></div>
                <p id="keywordsEmpty" class="py-6 text-center text-sm text-slate-500">Start typing to see your most-used words.</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const STATS = [
        { key: 'words', label: 'Words' },
        { key: 'chars', label: 'Characters' },
        { key: 'charsNoSpace', label: 'No spaces' },
        { key: 'unique', label: 'Unique words' },
        { key: 'sentences', label: 'Sentences' },
        { key: 'paragraphs', label: 'Paragraphs' },
        { key: 'lines', label: 'Lines' },
        { key: 'avgLen', label: 'Avg word len' },
        { key: 'reading', label: 'Reading time' },
        { key: 'speaking', label: 'Speaking time' },
    ];

    const STOP = new Set('the a an and or but of to in on for with at by from is are was were be been being it its this that these those as i you he she we they my your our their me him her them do does did have has had not no so if then than too very can will just about into over after under'.split(' '));

    function buildCards() {
        document.getElementById('statsGrid').innerHTML = STATS.map((s) => `
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur-xl">
                <div id="stat-${s.key}" class="bg-gradient-to-r from-sky-400 to-blue-400 bg-clip-text text-2xl font-bold text-transparent sm:text-3xl">0</div>
                <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400">${s.label}</div>
            </div>`).join('');
    }

    function fmtTime(min) {
        if (min <= 0) return '0s';
        return min < 1 ? `${Math.ceil(min * 60)}s` : `${Math.ceil(min)}m`;
    }

    function analyze() {
        const text = document.getElementById('textInput').value;
        const wordList = text.match(/\S+/g) || [];
        const words = wordList.length;
        const chars = text.length;
        const charsNoSpace = text.replace(/\s/g, '').length;
        const sentences = (text.match(/[^.!?]+[.!?]+/g) || []).length;
        const paragraphs = text.split(/\n\s*\n/).filter((p) => p.trim().length).length;
        const lines = text === '' ? 0 : text.split(/\n/).length;
        const tokens = (text.toLowerCase().match(/[a-z0-9']+/g) || []);
        const unique = new Set(tokens).size;
        const letters = tokens.reduce((a, w) => a + w.length, 0);
        const avgLen = tokens.length ? (letters / tokens.length).toFixed(1) : 0;
        const reading = fmtTime(words / 200);
        const speaking = fmtTime(words / 130);

        const values = { words, chars, charsNoSpace, unique, sentences, paragraphs, lines, avgLen, reading, speaking };
        Object.entries(values).forEach(([k, v]) => {
            const el = document.getElementById(`stat-${k}`);
            if (el) el.textContent = typeof v === 'number' ? v.toLocaleString() : v;
        });

        renderKeywords(tokens);
    }

    function renderKeywords(tokens) {
        const hide = document.getElementById('stopwords').checked;
        const freq = {};
        tokens.forEach((w) => { if (w.length < 2) return; if (hide && STOP.has(w)) return; freq[w] = (freq[w] || 0) + 1; });
        const entries = Object.entries(freq).sort((a, b) => b[1] - a[1]).slice(0, 10);
        const total = tokens.length || 1;
        const box = document.getElementById('keywords');
        const emptyEl = document.getElementById('keywordsEmpty');
        if (!entries.length) { box.innerHTML = ''; emptyEl.classList.remove('hidden'); return; }
        emptyEl.classList.add('hidden');
        const max = entries[0][1];
        box.innerHTML = entries.map(([w, c]) => {
            const pct = ((c / total) * 100).toFixed(1);
            return `<div>
                <div class="mb-1 flex items-center justify-between text-xs"><span class="font-mono text-slate-200">${w}</span><span class="text-slate-500">${c} · ${pct}%</span></div>
                <div class="h-1.5 overflow-hidden rounded-full bg-white/10"><div class="h-full rounded-full bg-gradient-to-r from-sky-400 to-blue-500" style="width:${(c / max) * 100}%"></div></div>
            </div>`;
        }).join('');
    }

    function clearText() {
        document.getElementById('textInput').value = '';
        analyze();
    }

    document.addEventListener('DOMContentLoaded', () => { buildCards(); analyze(); });
</script>
@endpush
