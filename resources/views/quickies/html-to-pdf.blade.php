@extends('layouts.app')

@section('title', 'HTML to PDF')
@section('description', 'Turn raw HTML markup into a downloadable PDF.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.6/dist/purify.min.js"></script>
@include('partials.pdf-render')
@endpush

@section('content')
    <x-tool-header title="HTML to PDF" subtitle="Paste HTML, preview the rendering, and export it as a PDF."
        from="from-indigo-500" to="to-violet-500" icon="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button onclick="downloadPdf()" id="btn" class="rounded-xl bg-gradient-to-r from-indigo-500 to-violet-500 px-5 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Download PDF</button>
            <button onclick="render()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Refresh preview</button>
            <label class="ml-auto flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200"><input type="checkbox" id="sanitize" checked onchange="render()" class="h-4 w-4 accent-indigo-500"> Sanitize (recommended)</label>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">HTML</label>
                <textarea id="input" rows="18" spellcheck="false" oninput="render()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-xs text-white transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Preview</label>
                <div id="preview" class="min-h-[440px] overflow-y-auto rounded-2xl border border-white/10 bg-white p-6 text-sm leading-relaxed" style="color:#111827;"></div>
            </div>
        </div>
        <p class="mt-4 text-xs text-slate-500">Scripts are always stripped from the preview and PDF for safety.</p>
    </div>
@endsection

@push('scripts')
<script>
    function render() {
        const raw = document.getElementById('input').value;
        const clean = document.getElementById('sanitize').checked ? DOMPurify.sanitize(raw) : raw.replace(/<script[\s\S]*?<\/script>/gi, '');
        document.getElementById('preview').innerHTML = clean;
    }
    async function downloadPdf() {
        const btn = document.getElementById('btn'); btn.disabled = true;
        showNotification('Generating PDF…', 'info');
        try { await window.htmlToPdfDoc(document.getElementById('preview').innerHTML, 'document.pdf', { fontFamily: "'Helvetica Neue', Arial, sans-serif" }); showNotification('PDF downloaded!', 'success'); }
        catch (e) { console.error(e); showNotification('PDF generation failed.', 'error'); }
        finally { btn.disabled = false; }
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('input').value = `<h1>Invoice #1024</h1>\n<p>Billed to: <strong>Acme Corp</strong></p>\n<table>\n  <tr><th>Item</th><th>Qty</th><th>Price</th></tr>\n  <tr><td>VAS subscription</td><td>3</td><td>$14.97</td></tr>\n  <tr><td>Setup fee</td><td>1</td><td>$5.00</td></tr>\n</table>\n<p style="margin-top:16px;"><strong>Total: $19.97</strong></p>`;
        render();
    });
</script>
@endpush
