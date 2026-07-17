@extends('layouts.app')

@section('title', 'Word to PDF')
@section('description', 'Convert Word .docx documents into a formatted PDF file.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/mammoth@1.8.0/mammoth.browser.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
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
            <div class="max-h-[500px] overflow-y-auto rounded-2xl border border-white/10 bg-slate-200 p-2">
                <div id="docContent" class="mx-auto bg-white px-10 py-8 text-slate-900 shadow" style="width: 720px; max-width: 100%; font-family: Georgia, 'Times New Roman', serif; line-height: 1.6;"></div>
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

    function downloadPdf() {
        const { jsPDF } = window.jspdf;
        const el = document.getElementById('docContent');
        const btn = document.getElementById('downloadBtn');
        btn.disabled = true;
        showNotification('Generating PDF…', 'info');
        const pdf = new jsPDF({ orientation: 'p', unit: 'pt', format: 'a4' });
        pdf.html(el, {
            callback: (doc) => { doc.save(docName + '.pdf'); btn.disabled = false; showNotification('PDF downloaded!', 'success'); },
            margin: [36, 36, 36, 36],
            autoPaging: 'text',
            width: 523,
            windowWidth: 720,
        });
    }
</script>
@endpush
