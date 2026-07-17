@extends('layouts.app')

@section('title', 'Markdown to PDF')
@section('description', 'Render Markdown into a clean, paginated PDF.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/marked@12.0.2/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
@include('partials.pdf-render')
@endpush

@section('content')
    <x-tool-header title="Markdown to PDF" subtitle="Write Markdown, preview it, and export a clean multi-page PDF."
        from="from-cyan-500" to="to-sky-500" icon="M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1zm2 10V9l3 3 3-3v6m3 0l2-2m0 0l-2-2m2 2h-4" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap gap-2">
            <button onclick="downloadPdf()" id="btn" class="rounded-xl bg-gradient-to-r from-cyan-500 to-sky-500 px-5 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Download PDF</button>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Import .md<input type="file" accept=".md,.markdown,.txt" class="hidden" onchange="importMd(event)"></label>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Markdown</label>
                <textarea id="input" rows="18" spellcheck="false" oninput="render()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-cyan-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/30"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Preview</label>
                <div id="preview" class="md-body min-h-[440px] overflow-y-auto rounded-2xl border border-white/10 bg-white p-6 text-sm leading-relaxed" style="color:#111827;"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function render() {
        document.getElementById('preview').innerHTML = DOMPurify.sanitize(marked.parse(document.getElementById('input').value, { breaks: true, gfm: true }));
    }
    function importMd(e) { const f = e.target.files[0]; if (!f) return; const r = new FileReader(); r.onload = (ev) => { document.getElementById('input').value = ev.target.result; render(); }; r.readAsText(f); e.target.value = ''; }
    async function downloadPdf() {
        const btn = document.getElementById('btn'); btn.disabled = true;
        showNotification('Generating PDF…', 'info');
        try { await window.htmlToPdfDoc(document.getElementById('preview').innerHTML, 'markdown.pdf', { fontFamily: "'Helvetica Neue', Arial, sans-serif" }); showNotification('PDF downloaded!', 'success'); }
        catch (e) { console.error(e); showNotification('PDF generation failed.', 'error'); }
        finally { btn.disabled = false; }
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('input').value = `# Project Report\n\n## Summary\nThis document was generated with **Quickies**.\n\n- Fast\n- Private\n- Free\n\n> Everything runs in your browser.\n\n| Metric | Value |\n| --- | --- |\n| Conversions | 1,240 |\n| Revenue | $3,980 |\n\n\`\`\`js\nconsole.log('Hello, PDF!');\n\`\`\``;
        render();
    });
</script>
@endpush
