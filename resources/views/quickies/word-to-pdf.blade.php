@extends('layouts.app')

@section('title', 'Word to PDF')
@section('description', 'Convert Word .docx documents into a formatted PDF file.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/mammoth@1.8.0/mammoth.browser.min.js"></script>
@include('partials.pdf-render')
<style>
    /* Force safe rgb/hex colours so html2canvas never sees Tailwind's oklch() values. */
    #docContent, #docContent * { color: #0f172a !important; border-color: #cbd5e1 !important; background-image: none !important; }
    #docContent { background: #ffffff !important; box-shadow: none !important; }
    #docContent a { color: #1d4ed8 !important; }
    #docContent th, #docContent td { border: 1px solid #cbd5e1 !important; }
    #docContent h1, #docContent h2, #docContent h3 { color: #0f172a !important; }
</style>
@endpush

@section('content')
    <x-tool-header
        title="Word to PDF"
        subtitle="Convert a Word .docx document into a downloadable PDF."
        from="from-blue-600"
        to="to-sky-500"
        icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div id="dropZone"
             class="cursor-pointer rounded-2xl border-2 border-dashed border-sky-400/40 bg-white/5 px-6 py-10 text-center transition hover:border-sky-400 hover:bg-white/10"
             onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="hidden" onchange="handleFile(event)">
            <svg class="mx-auto mb-3 h-14 w-14 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h3 class="text-lg font-bold text-white">Drop a .docx here</h3>
            <p class="mt-1 text-sm text-slate-400">or click to browse</p>
            <p class="mt-3 text-xs text-slate-500">Modern Word documents (.docx) only</p>
        </div>

        <div id="panel" class="mt-6 hidden">
            <div class="mb-4 flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm">
                <span class="truncate text-slate-300" id="fileName"></span>
                <button onclick="downloadPdf()" id="downloadBtn" class="rounded-xl bg-gradient-to-r from-blue-600 to-sky-500 px-5 py-2 font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95 disabled:opacity-50">Download PDF</button>
            </div>
            <p class="mb-2 text-sm font-semibold text-slate-200">Preview</p>
            <div class="max-h-[500px] overflow-y-auto rounded-2xl border border-white/10 p-2" style="background:#e2e8f0;">
                <div id="docContent" class="mx-auto px-10 py-8" style="width: 720px; max-width: 100%; background:#ffffff; color:#0f172a; font-family: Georgia, 'Times New Roman', serif; line-height: 1.6;"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let docName = 'document';

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-sky-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-sky-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-sky-400', 'bg-white/10'); if (e.dataTransfer.files.length) loadFile(e.dataTransfer.files[0]); });

    function handleFile(e) { if (e.target.files.length) loadFile(e.target.files[0]); }

    async function loadFile(file) {
        if (!file.name.toLowerCase().endsWith('.docx')) return showNotification('Please select a .docx file (old .doc is not supported).', 'error');
        docName = file.name.replace(/\.docx$/i, '');
        document.getElementById('fileName').textContent = file.name;
        showNotification('Reading document…', 'info');
        try {
            const arrayBuffer = await file.arrayBuffer();
            const result = await mammoth.convertToHtml({ arrayBuffer });
            document.getElementById('docContent').innerHTML = result.value || '<p>(empty document)</p>';
            document.getElementById('panel').classList.remove('hidden');
            showNotification('Document loaded — preview ready.', 'success');
        } catch (err) {
            console.error(err);
            showNotification('Could not read this Word file.', 'error');
        }
    }

    async function downloadPdf() {
        const btn = document.getElementById('downloadBtn');
        btn.disabled = true;
        showNotification('Generating PDF…', 'info');
        try {
            await window.htmlToPdfDoc(document.getElementById('docContent').innerHTML, docName + '.pdf');
            showNotification('PDF downloaded!', 'success');
        } catch (e) {
            console.error(e);
            showNotification('PDF generation failed.', 'error');
        } finally {
            btn.disabled = false;
        }
    }
</script>
@endpush
