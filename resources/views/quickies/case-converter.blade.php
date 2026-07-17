@extends('layouts.app')

@section('title', 'Case Converter')
@section('description', 'Convert text between camelCase, snake_case, Title Case and more.')

@section('content')
    <x-tool-header title="Case Converter" subtitle="Transform text into any casing style — updates as you type."
        from="from-sky-500" to="to-cyan-500" icon="M4 7V4h16v3M9 20h6M12 4v16" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">Your text</label>
        <textarea id="input" rows="5" oninput="convert()"
            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-sky-400/60 focus:outline-none focus:ring-2 focus:ring-sky-500/30"
            placeholder="Type or paste text here…">Hello world, this is Quickies!</textarea>
        <div id="results" class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2"></div>
    </div>
@endsection

@push('scripts')
<script>
    const words = (s) => s.replace(/([a-z])([A-Z])/g, '$1 $2').replace(/[_\-]+/g, ' ').replace(/\s+/g, ' ').trim().split(' ').filter(Boolean);
    const cap = (w) => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase();

    const CASES = {
        'UPPERCASE': (s) => s.toUpperCase(),
        'lowercase': (s) => s.toLowerCase(),
        'Title Case': (s) => words(s).map(cap).join(' '),
        'Sentence case': (s) => { const l = s.toLowerCase(); return l.replace(/(^\s*\w|[.!?]\s+\w)/g, (m) => m.toUpperCase()); },
        'camelCase': (s) => words(s).map((w, i) => i === 0 ? w.toLowerCase() : cap(w)).join(''),
        'PascalCase': (s) => words(s).map(cap).join(''),
        'snake_case': (s) => words(s).map((w) => w.toLowerCase()).join('_'),
        'CONSTANT_CASE': (s) => words(s).map((w) => w.toUpperCase()).join('_'),
        'kebab-case': (s) => words(s).map((w) => w.toLowerCase()).join('-'),
        'dot.case': (s) => words(s).map((w) => w.toLowerCase()).join('.'),
        'path/case': (s) => words(s).map((w) => w.toLowerCase()).join('/'),
        'aLtErNaTiNg': (s) => s.split('').map((c, i) => i % 2 ? c.toUpperCase() : c.toLowerCase()).join(''),
    };

    function convert() {
        const s = document.getElementById('input').value;
        document.getElementById('results').innerHTML = Object.entries(CASES).map(([name, fn]) => {
            const out = s ? fn(s) : '';
            return `<div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div class="mb-1 flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wide text-slate-400">${name}</span>
                    <button onclick="copyToClipboard(this.previousElementSibling ? '' : '', '')" class="hidden"></button>
                    <button data-copy="${encodeURIComponent(out)}" onclick="copyToClipboard(decodeURIComponent(this.dataset.copy), '${name} copied')" class="rounded-md border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <div class="break-all font-mono text-sm text-white">${out.replace(/&/g,'&amp;').replace(/</g,'&lt;') || '<span class="text-slate-600">—</span>'}</div>
            </div>`;
        }).join('');
    }
    document.addEventListener('DOMContentLoaded', convert);
</script>
@endpush
