@extends('layouts.app')

@section('title', 'SVG Optimizer')
@section('description', 'Clean and minify SVG files to reduce their size.')

@section('content')
    <x-tool-header title="SVG Optimizer" subtitle="Strip metadata and whitespace to shrink SVG files — right in your browser."
        from="from-emerald-500" to="to-green-500" icon="M13 10V3L4 14h7v7l9-11h-7z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button onclick="optimize()" class="rounded-xl bg-gradient-to-r from-emerald-500 to-green-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Optimize</button>
            <label class="cursor-pointer rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Upload SVG<input type="file" accept="image/svg+xml,.svg" class="hidden" onchange="upload(event)"></label>
            <button onclick="download()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Download</button>
            <button onclick="copyToClipboard(document.getElementById('output').value, 'SVG copied')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            <span id="stats" class="ml-auto text-sm font-semibold text-emerald-300"></span>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Input SVG</label>
                <textarea id="input" rows="14" spellcheck="false" oninput="optimize()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-xs text-white transition focus:border-emerald-400/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-slate-200"><input type="checkbox" id="optComments" checked onchange="optimize()" class="h-3.5 w-3.5 accent-emerald-500"> comments</label>
                    <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-slate-200"><input type="checkbox" id="optMeta" checked onchange="optimize()" class="h-3.5 w-3.5 accent-emerald-500"> metadata</label>
                    <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-slate-200"><input type="checkbox" id="optWhitespace" checked onchange="optimize()" class="h-3.5 w-3.5 accent-emerald-500"> whitespace</label>
                    <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-slate-200"><input type="checkbox" id="optNumbers" checked onchange="optimize()" class="h-3.5 w-3.5 accent-emerald-500"> round numbers</label>
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Optimized</label>
                <div id="preview" class="checkerboard mb-3 flex h-32 items-center justify-center overflow-hidden rounded-2xl border border-white/10 [&_svg]:max-h-full [&_svg]:max-w-full"></div>
                <textarea id="output" rows="8" readonly spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-xs text-emerald-100 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function upload(e) {
        const file = e.target.files[0]; if (!file) return;
        const reader = new FileReader();
        reader.onload = (ev) => { document.getElementById('input').value = ev.target.result; optimize(); };
        reader.readAsText(file);
    }

    function optimize() {
        let svg = document.getElementById('input').value;
        const original = new Blob([svg]).size;
        if (!svg.trim()) { document.getElementById('output').value = ''; document.getElementById('preview').innerHTML = ''; document.getElementById('stats').textContent = ''; return; }

        if (document.getElementById('optComments').checked) svg = svg.replace(/<!--[\s\S]*?-->/g, '');
        if (document.getElementById('optMeta').checked) {
            svg = svg.replace(/<metadata[\s\S]*?<\/metadata>/gi, '')
                     .replace(/<\?xml[\s\S]*?\?>/gi, '')
                     .replace(/<!DOCTYPE[^>]*>/gi, '')
                     .replace(/\s(xmlns:(?!xlink)[a-z]+)="[^"]*"/gi, '')
                     .replace(/<(sodipodi|inkscape)[\s\S]*?(\/>|<\/\1:[^>]*>)/gi, '')
                     .replace(/\s(inkscape|sodipodi):[a-z-]+="[^"]*"/gi, '')
                     .replace(/<title[\s\S]*?<\/title>/gi, '')
                     .replace(/<desc[\s\S]*?<\/desc>/gi, '');
        }
        if (document.getElementById('optNumbers').checked) {
            svg = svg.replace(/(\d+\.\d{3,})/g, (m) => parseFloat(parseFloat(m).toFixed(2)).toString());
        }
        if (document.getElementById('optWhitespace').checked) {
            svg = svg.replace(/>\s+</g, '><').replace(/\s{2,}/g, ' ').replace(/\s+\/>/g, '/>').trim();
        }

        document.getElementById('output').value = svg;
        // Safe preview: only render if it parses as SVG
        const parsed = new DOMParser().parseFromString(svg, 'image/svg+xml');
        if (!parsed.querySelector('parsererror') && parsed.querySelector('svg')) {
            document.getElementById('preview').innerHTML = '';
            document.getElementById('preview').appendChild(parsed.documentElement.cloneNode(true));
        }
        const optimized = new Blob([svg]).size;
        const saved = ((1 - optimized / original) * 100).toFixed(1);
        document.getElementById('stats').textContent = `${(original/1024).toFixed(1)} KB → ${(optimized/1024).toFixed(1)} KB (saved ${saved}%)`;
    }

    function download() {
        const svg = document.getElementById('output').value;
        if (!svg) return showNotification('Nothing to download.', 'error');
        const blob = new Blob([svg], { type: 'image/svg+xml' });
        const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'optimized.svg'; a.click(); URL.revokeObjectURL(a.href);
        showNotification('SVG downloaded!', 'success');
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('input').value = `<?xml version="1.0"?>\n<!-- Made with love -->\n<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">\n    <metadata>ignore me</metadata>\n    <circle cx="50.000000" cy="50.000000" r="40.123456" fill="#22c55e" />\n</svg>`;
        optimize();
    });
</script>
@endpush
