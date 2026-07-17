@extends('layouts.app')

@section('title', 'Markdown Previewer')
@section('description', 'Write Markdown and preview the rendered HTML instantly.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.2/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
<style>
    .md-body h1 { font-size: 1.75rem; font-weight: 700; margin: 1rem 0 0.5rem; color: #fff; }
    .md-body h2 { font-size: 1.4rem; font-weight: 700; margin: 1rem 0 0.5rem; color: #fff; }
    .md-body h3 { font-size: 1.15rem; font-weight: 600; margin: 0.85rem 0 0.4rem; color: #fff; }
    .md-body p { margin: 0.5rem 0; }
    .md-body a { color: #67e8f9; text-decoration: underline; }
    .md-body ul { list-style: disc; padding-left: 1.5rem; margin: 0.5rem 0; }
    .md-body ol { list-style: decimal; padding-left: 1.5rem; margin: 0.5rem 0; }
    .md-body code { background: rgba(255,255,255,0.1); padding: 0.1rem 0.35rem; border-radius: 0.35rem; font-family: ui-monospace, monospace; font-size: 0.9em; }
    .md-body pre { background: rgba(2,6,23,0.6); border: 1px solid rgba(255,255,255,0.1); padding: 1rem; border-radius: 0.75rem; overflow-x: auto; margin: 0.75rem 0; }
    .md-body pre code { background: none; padding: 0; }
    .md-body blockquote { border-left: 3px solid #22d3ee; padding-left: 1rem; color: #cbd5e1; margin: 0.75rem 0; }
    .md-body table { border-collapse: collapse; width: 100%; margin: 0.75rem 0; }
    .md-body th, .md-body td { border: 1px solid rgba(255,255,255,0.15); padding: 0.5rem 0.75rem; }
    .md-body img { max-width: 100%; border-radius: 0.5rem; }
    .md-body hr { border-color: rgba(255,255,255,0.15); margin: 1rem 0; }
</style>
@endpush

@section('content')
    <x-tool-header
        title="Markdown Previewer"
        subtitle="Write Markdown on the left, preview safe rendered HTML on the right."
        from="from-cyan-500"
        to="to-sky-500"
        icon="M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1zm2 10V9l3 3 3-3v6m3 0l2-2m0 0l-2-2m2 2h-4" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap gap-2">
            <button onclick="copyToClipboard(document.getElementById('mdInput').value, 'Markdown copied')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy Markdown</button>
            <button onclick="copyHtml()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy HTML</button>
            <button onclick="downloadHtml()" class="rounded-xl bg-gradient-to-r from-cyan-500 to-sky-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Download HTML</button>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Markdown</label>
                <textarea id="mdInput" rows="18" spellcheck="false" oninput="render()"
                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-cyan-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/30"
                    placeholder="# Hello"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Preview</label>
                <div id="mdPreview" class="md-body min-h-[440px] overflow-y-auto rounded-2xl border border-white/10 bg-slate-950/40 p-5 text-sm leading-relaxed text-slate-300"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const SAMPLE = `# Markdown Previewer

Write **bold**, _italic_ and \\\`inline code\\\`.

## Features
- Live preview
- Safe HTML output
- Tables & code blocks

> Blockquotes look great too.

\\\`\\\`\\\`js
console.log('Hello, Quickies!');
\\\`\\\`\\\`

| Tool | Type |
| ---- | ---- |
| Markdown | Text |

[Back to dashboard](/)`;

    function render() {
        const raw = document.getElementById('mdInput').value;
        const html = DOMPurify.sanitize(marked.parse(raw, { breaks: true, gfm: true }));
        document.getElementById('mdPreview').innerHTML = html;
    }

    function copyHtml() {
        copyToClipboard(document.getElementById('mdPreview').innerHTML, 'HTML copied');
    }

    function downloadHtml() {
        const body = document.getElementById('mdPreview').innerHTML;
        const doc = `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Markdown export</title></head><body>${body}</body></html>`;
        const blob = new Blob([doc], { type: 'text/html' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'markdown.html';
        a.click();
        URL.revokeObjectURL(a.href);
        showNotification('HTML downloaded!', 'success');
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('mdInput').value = SAMPLE;
        render();
    });
</script>
@endpush
