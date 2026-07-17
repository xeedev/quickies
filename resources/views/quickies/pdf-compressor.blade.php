@extends('layouts.app')

@section('title', 'PDF Compressor')
@section('description', 'Shrink PDF file size by re-rendering pages at a lower quality.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
@endpush

@section('content')
    <x-tool-header
        title="PDF Compressor"
        subtitle="Reduce PDF size by re-rendering each page — great for scans and image-heavy PDFs."
        from="from-rose-500"
        to="to-pink-500"
        icon="M9 12h6m-3-3v6m-7 5h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div id="dropZone"
             class="cursor-pointer rounded-2xl border-2 border-dashed border-pink-400/40 bg-white/5 px-6 py-10 text-center transition hover:border-pink-400 hover:bg-white/10"
             onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" accept="application/pdf" class="hidden" onchange="handleFile(event)">
            <svg class="mx-auto mb-3 h-14 w-14 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-3-3v6m-7 5h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <h3 class="text-lg font-bold text-white">Drop a PDF here</h3>
            <p class="mt-1 text-sm text-slate-400">or click to browse</p>
        </div>

        <div id="panel" class="mt-6 hidden">
            <div class="mb-4 rounded-2xl border border-white/10 bg-white/5 p-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="truncate text-slate-300" id="fileName"></span>
                    <span class="text-slate-400" id="fileMeta"></span>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Quality <span id="qualityValue" class="text-slate-400">70</span>%</label>
                    <input type="range" id="quality" min="20" max="95" value="70" class="w-full" oninput="document.getElementById('qualityValue').textContent = this.value">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Resolution</label>
                    <select id="scale" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-pink-400/60 focus:outline-none">
                        <option value="1" class="bg-slate-900">Low (72 dpi)</option>
                        <option value="1.5" class="bg-slate-900" selected>Medium (108 dpi)</option>
                        <option value="2" class="bg-slate-900">High (144 dpi)</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button id="compressBtn" onclick="compress()" class="flex-1 rounded-2xl bg-gradient-to-r from-rose-500 to-pink-500 px-6 py-3 font-semibold text-white shadow-lg shadow-pink-500/25 transition hover:scale-[1.01] active:scale-95 disabled:cursor-not-allowed disabled:opacity-50">Compress PDF</button>
                <button onclick="reset()" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-slate-200 transition hover:bg-white/10">Choose another</button>
            </div>

            <div id="progress" class="mt-5 hidden">
                <div class="h-2 overflow-hidden rounded-full bg-white/10"><div id="progressBar" class="h-full w-0 rounded-full bg-gradient-to-r from-rose-500 to-pink-500 transition-all"></div></div>
                <p id="progressText" class="mt-2 text-center text-sm text-slate-400"></p>
            </div>

            <div id="result" class="mt-5 hidden rounded-2xl border border-white/10 bg-white/5 p-5 text-center"></div>
        </div>

        <div class="mt-6 flex items-start gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-slate-400">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Pages are re-rendered as images, so text becomes non-selectable. Best for scanned or image-heavy PDFs.</span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    let currentFile = null;
    let originalBytes = 0;

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-pink-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-pink-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-pink-400', 'bg-white/10'); if (e.dataTransfer.files.length) loadFile(e.dataTransfer.files[0]); });

    function handleFile(e) { if (e.target.files.length) loadFile(e.target.files[0]); }

    function loadFile(file) {
        if (file.type !== 'application/pdf') return showNotification('Please select a PDF file.', 'error');
        currentFile = file;
        originalBytes = file.size;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileMeta').textContent = formatSize(file.size);
        document.getElementById('panel').classList.remove('hidden');
        document.getElementById('result').classList.add('hidden');
    }

    async function compress() {
        if (!currentFile) return;
        const quality = parseInt(document.getElementById('quality').value) / 100;
        const scale = parseFloat(document.getElementById('scale').value);
        const btn = document.getElementById('compressBtn');
        btn.disabled = true;
        document.getElementById('progress').classList.remove('hidden');
        document.getElementById('result').classList.add('hidden');

        try {
            const data = await currentFile.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data }).promise;
            const { jsPDF } = window.jspdf;
            let out = null;

            for (let i = 1; i <= pdf.numPages; i++) {
                setProgress((i - 1) / pdf.numPages, `Rendering page ${i} of ${pdf.numPages}…`);
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                await page.render({ canvasContext: ctx, viewport }).promise;
                const jpeg = canvas.toDataURL('image/jpeg', quality);

                const orient = viewport.width >= viewport.height ? 'l' : 'p';
                const format = [viewport.width, viewport.height];
                if (i === 1) out = new jsPDF({ orientation: orient, unit: 'px', format, hotfixes: ['px_scaling'] });
                else out.addPage(format, orient);
                out.addImage(jpeg, 'JPEG', 0, 0, viewport.width, viewport.height, undefined, 'FAST');
            }

            setProgress(1, 'Finalising…');
            const blob = out.output('blob');
            showResult(blob);
        } catch (err) {
            console.error(err);
            showNotification('Could not process this PDF.', 'error');
        } finally {
            btn.disabled = false;
            document.getElementById('progress').classList.add('hidden');
        }
    }

    function setProgress(ratio, text) {
        document.getElementById('progressBar').style.width = Math.round(ratio * 100) + '%';
        document.getElementById('progressText').textContent = text;
    }

    function showResult(blob) {
        const saved = originalBytes - blob.size;
        const pct = ((saved / originalBytes) * 100).toFixed(1);
        const smaller = blob.size < originalBytes;
        const url = URL.createObjectURL(blob);
        const name = currentFile.name.replace(/\.pdf$/i, '') + '-compressed.pdf';
        const box = document.getElementById('result');
        box.classList.remove('hidden');
        box.innerHTML = `
            <div class="flex flex-wrap items-center justify-center gap-6 text-center">
                <div><div class="text-xs text-slate-500">Original</div><div class="text-lg font-bold text-white">${formatSize(originalBytes)}</div></div>
                <svg class="h-5 w-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                <div><div class="text-xs text-slate-500">Compressed</div><div class="text-lg font-bold text-white">${formatSize(blob.size)}</div></div>
                <div class="rounded-xl px-3 py-1 text-sm font-bold ${smaller ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300'}">${smaller ? 'Saved ' + pct + '%' : 'No savings — try lower quality'}</div>
            </div>
            <a href="${url}" download="${name}" class="mt-5 inline-block rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-500 px-6 py-3 font-semibold text-white shadow-lg shadow-emerald-500/25 transition hover:scale-[1.02]">Download compressed PDF</a>`;
        showNotification('Compression complete!', 'success');
    }

    function reset() {
        currentFile = null;
        document.getElementById('panel').classList.add('hidden');
        document.getElementById('fileInput').value = '';
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }
</script>
@endpush
