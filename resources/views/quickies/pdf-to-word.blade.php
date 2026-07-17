@extends('layouts.app')

@section('title', 'PDF to Word')
@section('description', 'Extract text from a PDF into an editable Word document.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
@endpush

@section('content')
    <x-tool-header
        title="PDF to Word"
        subtitle="Extract text from a PDF into an editable Word (.doc) document."
        from="from-sky-500"
        to="to-blue-600"
        icon="M12 9v6m3-3H9m-4 9h14a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div id="dropZone"
             class="cursor-pointer rounded-2xl border-2 border-dashed border-blue-400/40 bg-white/5 px-6 py-10 text-center transition hover:border-blue-400 hover:bg-white/10"
             onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" accept="application/pdf" class="hidden" onchange="handleFile(event)">
            <svg class="mx-auto mb-3 h-14 w-14 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v6m3-3H9m-4 9h14a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <h3 class="text-lg font-bold text-white">Drop a PDF here</h3>
            <p class="mt-1 text-sm text-slate-400">or click to browse</p>
        </div>

        <div id="panel" class="mt-6 hidden">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm">
                <span class="truncate text-slate-300" id="fileMeta"></span>
                <div class="flex gap-2">
                    <button onclick="copyToClipboard(document.getElementById('textOut').value, 'Text copied')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 font-semibold text-slate-200 transition hover:bg-white/10">Copy text</button>
                    <button onclick="downloadDoc()" id="downloadBtn" class="rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-5 py-2 font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Download .doc</button>
                </div>
            </div>
            <textarea id="textOut" rows="16" spellcheck="false"
                class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-slate-200 focus:border-blue-400/60 focus:outline-none"
                placeholder="Extracted text will appear here…"></textarea>
        </div>

        <div class="mt-6 flex items-start gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-400">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Extracts the text layer only — scanned/image PDFs without embedded text can't be converted. Complex layouts may reflow.</span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    let docName = 'document';
    let pages = [];

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-blue-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-blue-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-blue-400', 'bg-white/10'); if (e.dataTransfer.files.length) loadFile(e.dataTransfer.files[0]); });

    function handleFile(e) { if (e.target.files.length) loadFile(e.target.files[0]); }

    async function loadFile(file) {
        if (file.type !== 'application/pdf') return showNotification('Please select a PDF file.', 'error');
        docName = file.name.replace(/\.pdf$/i, '');
        showNotification('Extracting text…', 'info');
        try {
            const data = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data }).promise;
            pages = [];
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const content = await page.getTextContent();
                pages.push(itemsToText(content.items));
            }
            const text = pages.join('\n\n');
            document.getElementById('textOut').value = text;
            document.getElementById('fileMeta').textContent = `${file.name} · ${pdf.numPages} page${pdf.numPages > 1 ? 's' : ''}`;
            document.getElementById('panel').classList.remove('hidden');
            showNotification(text.trim() ? 'Text extracted!' : 'No embedded text found in this PDF.', text.trim() ? 'success' : 'warning');
        } catch (err) {
            console.error(err);
            showNotification('Could not read this PDF.', 'error');
        }
    }

    function itemsToText(items) {
        let out = '';
        let lastY = null;
        items.forEach((item) => {
            const y = item.transform[5];
            if (lastY !== null && Math.abs(y - lastY) > 2) out += '\n';
            out += item.str;
            if (item.hasEOL) out += '\n';
            lastY = y;
        });
        return out.replace(/[ \t]+\n/g, '\n').trim();
    }

    function downloadDoc() {
        const text = document.getElementById('textOut').value;
        const escaped = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        const paragraphs = escaped.split(/\n{2,}/).map((p) => `<p>${p.replace(/\n/g, '<br>')}</p>`).join('');
        const html = `<!DOCTYPE html><html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="utf-8"><title>${docName}</title></head><body style="font-family:Calibri,Arial,sans-serif;font-size:11pt;line-height:1.5;">${paragraphs}</body></html>`;
        const blob = new Blob(['\ufeff', html], { type: 'application/msword' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = docName + '.doc';
        a.click();
        URL.revokeObjectURL(a.href);
        showNotification('Word document downloaded!', 'success');
    }
</script>
@endpush
