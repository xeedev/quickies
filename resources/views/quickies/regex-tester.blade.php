@extends('layouts.app')

@section('title', 'Regex Tester')
@section('description', 'Test regular expressions with live match highlighting.')

@section('content')
    <x-tool-header
        title="Regex Tester"
        subtitle="Test JavaScript regular expressions with live match highlighting."
        from="from-violet-500"
        to="to-purple-500"
        icon="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">Pattern</label>
        <div class="flex items-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-3 focus-within:border-violet-400/60 focus-within:ring-2 focus-within:ring-violet-500/30">
            <span class="font-mono text-lg text-slate-500">/</span>
            <input id="pattern" type="text" spellcheck="false" oninput="run()" value="\b\w+@\w+\.\w+\b"
                class="flex-1 bg-transparent py-3 font-mono text-sm text-white placeholder-slate-500 focus:outline-none" placeholder="pattern">
            <span class="font-mono text-lg text-slate-500">/</span>
            <input id="flags" type="text" spellcheck="false" oninput="run()" value="g"
                class="w-20 bg-transparent py-3 font-mono text-sm text-violet-300 focus:outline-none" placeholder="flags">
        </div>
        <div class="mt-2 flex flex-wrap gap-2 text-xs">
            @foreach (['g' => 'global', 'i' => 'ignore case', 'm' => 'multiline', 's' => 'dotall', 'u' => 'unicode', 'y' => 'sticky'] as $f => $desc)
                <button type="button" onclick="toggleFlag('{{ $f }}')" class="flag-btn rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 font-mono font-semibold text-slate-300 transition hover:bg-white/10" data-flag="{{ $f }}" title="{{ $desc }}">{{ $f }}</button>
            @endforeach
        </div>
        <p id="regexError" class="mt-2 hidden text-sm font-semibold text-rose-300"></p>

        <label class="mb-2 mt-6 block text-sm font-semibold text-slate-200">Test string</label>
        <textarea id="testString" rows="8" spellcheck="false" oninput="run()"
            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-violet-400/60 focus:outline-none focus:ring-2 focus:ring-violet-500/30"
            placeholder="Text to search…">Contact us at hello@example.com or support@quickies.dev today.</textarea>

        <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-200">Highlighted matches</span>
                    <span id="matchCount" class="rounded-lg bg-violet-500/20 px-2.5 py-0.5 text-xs font-semibold text-violet-200">0 matches</span>
                </div>
                <div id="highlight" class="min-h-[120px] overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm leading-relaxed"></div>
            </div>
            <div>
                <span class="mb-2 block text-sm font-semibold text-slate-200">Match details</span>
                <div id="matchList" class="max-h-[240px] space-y-2 overflow-y-auto"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleFlag(f) {
        const input = document.getElementById('flags');
        const set = new Set(input.value.split(''));
        set.has(f) ? set.delete(f) : set.add(f);
        input.value = [...set].join('');
        run();
    }

    function escapeHtml(s) {
        return s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function run() {
        const pattern = document.getElementById('pattern').value;
        let flags = document.getElementById('flags').value;
        const text = document.getElementById('testString').value;
        const err = document.getElementById('regexError');
        const highlight = document.getElementById('highlight');
        const matchList = document.getElementById('matchList');

        document.querySelectorAll('.flag-btn').forEach((b) => {
            const active = flags.includes(b.dataset.flag);
            b.classList.toggle('bg-violet-500/30', active);
            b.classList.toggle('text-violet-100', active);
            b.classList.toggle('border-violet-400/40', active);
        });

        if (!pattern) { highlight.textContent = ''; matchList.innerHTML = ''; err.classList.add('hidden'); document.getElementById('matchCount').textContent = '0 matches'; return; }

        let re;
        try {
            re = new RegExp(pattern, flags);
            err.classList.add('hidden');
        } catch (e) {
            err.textContent = e.message;
            err.classList.remove('hidden');
            return;
        }

        const global = flags.includes('g');
        const matches = [];
        if (global) {
            let m; let guard = 0;
            while ((m = re.exec(text)) !== null) {
                matches.push(m);
                if (m.index === re.lastIndex) re.lastIndex++;
                if (++guard > 10000) break;
            }
        } else {
            const m = re.exec(text);
            if (m) matches.push(m);
        }

        // Highlight
        let html = '', last = 0;
        matches.forEach((m) => {
            html += escapeHtml(text.slice(last, m.index));
            html += `<mark class="rounded bg-violet-500/40 px-0.5 text-violet-100">${escapeHtml(m[0]) || '∅'}</mark>`;
            last = m.index + m[0].length;
        });
        html += escapeHtml(text.slice(last));
        highlight.innerHTML = `<pre class="whitespace-pre-wrap break-words text-slate-300">${html || '<span class="text-slate-500">No matches</span>'}</pre>`;

        document.getElementById('matchCount').textContent = `${matches.length} match${matches.length === 1 ? '' : 'es'}`;
        matchList.innerHTML = matches.length ? matches.map((m, i) => {
            const groups = m.slice(1).map((g, gi) => `<div class="text-xs text-slate-400">Group ${gi + 1}: <span class="font-mono text-slate-200">${g === undefined ? '—' : escapeHtml(g)}</span></div>`).join('');
            return `<div class="rounded-xl border border-white/10 bg-white/5 p-3">
                <div class="font-mono text-sm text-violet-200">${escapeHtml(m[0])}</div>
                <div class="mt-1 text-xs text-slate-500">at index ${m.index}</div>${groups}</div>`;
        }).join('') : '<p class="text-sm text-slate-500">No matches found.</p>';
    }

    document.addEventListener('DOMContentLoaded', run);
</script>
@endpush
