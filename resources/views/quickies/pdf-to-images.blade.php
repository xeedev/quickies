@extends('layouts.app')

@section('title', 'PDF to Images')
@section('description', 'Convert each PDF page into a PNG or JPG image.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.min.js"></script>
@endpush

@section('content')
    <x-tool-header title="PDF to Images" subtitle="Render every page of a PDF to a downloadable PNG or JPG."
        from="from-orange-500" to="to-amber-500" icon="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div id="dropZone" class="cursor-pointer rounded-2xl border-2 border-dashed border-amber-400/40 bg-white/5 px-6 py-10 text-center transition hover:border-amber-400 hover:bg-white/10" onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" accept="application/pdf" class="hidden" onchange="handleFile(event)">
            <svg class="mx-auto mb-3 h-14 w-14 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <h3 class="text-lg font-bold text-white">Drop a PDF here</h3>
            <p class="mt-1 text-sm text-slate-400">or click to browse</p>
        </div>

        <div id="panel" class="mt-6 hidden">
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">Format
                    <select id="format" class="rounded-lg border border-white/10 bg-slate-900 px-2 py-1 text-white focus:outline-none"><option value="png" class="bg-slate-900">PNG</option><option value="jpeg" class="bg-slate-900">JPG</option></select>
                </label>
                <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">Resolution
                    <select id="scale" class="rounded-lg border border-white/10 bg-slate-900 px-2 py-1 text-white focus:outline-none"><option value="1.5" class="bg-slate-900">108 dpi</option><option value="2" class="bg-slate-900" selected>144 dpi</option><option value="3" class="bg-slate-900">216 dpi</option></select>
                </label>
                <button onclick="render()" id="renderBtn" class="rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 px-5 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Convert</button>
                <button onclick="downloadAll()" id="allBtn" disabled class="rounded-xl border border-white/10 bg-white/5 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/10 disabled:opacity-40">Download all</button>
                <button onclick="reset()" class="ml-auto rounded-xl border border-white/10 bg-white/5 px-5 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Choose another</button>
            </div>
            <div id="progress" class="mb-4 hidden"><div class="h-2 overflow-hidden rounded-full bg-white/10"><div id="bar" class="h-full w-0 rounded-full bg-gradient-to-r from-orange-500 to-amber-500 transition-all"></div></div></div>
            <div id="grid" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.11.174/build/pdf.worker.min.js';
    let bytes = null, name = 'page', images = [];

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-amber-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-amber-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-amber-400', 'bg-white/10'); if (e.dataTransfer.files.length) loadFile(e.dataTransfer.files[0]); });

    function handleFile(e) { if (e.target.files.length) loadFile(e.target.files[0]); }
    async function loadFile(file) {
        if (file.type !== 'application/pdf') return showNotification('Please select a PDF.', 'error');
        name = file.name.replace(/\.pdf$/i, '');
        bytes = await file.arrayBuffer();
        document.getElementById('panel').classList.remove('hidden');
        render();
    }

    async function render() {
        const btn = document.getElementById('renderBtn'); btn.disabled = true;
        const fmt = document.getElementById('format').value;
        const scale = parseFloat(document.getElementById('scale').value);
        const grid = document.getElementById('grid'); grid.innerHTML = '';
        images = [];
        document.getElementById('progress').classList.remove('hidden');
        try {
            const pdf = await pdfjsLib.getDocument({ data: bytes.slice(0) }).promise;
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({ scale });
                const canvas = document.createElement('canvas');
                canvas.width = viewport.width; canvas.height = viewport.height;
                const ctx = canvas.getContext('2d');
                if (fmt === 'jpeg') { ctx.fillStyle = '#fff'; ctx.fillRect(0, 0, canvas.width, canvas.height); }
                await page.render({ canvasContext: ctx, viewport }).promise;
                const url = canvas.toDataURL('image/' + fmt, 0.92);
                images.push({ url, name: `${name}-${i}.${fmt === 'jpeg' ? 'jpg' : 'png'}` });
                grid.insertAdjacentHTML('beforeend', `<div class="rounded-2xl border border-white/10 bg-white/5 p-2 text-center">
                    <div class="checkerboard mb-2 flex items-center justify-center rounded-lg p-1"><img src="${url}" class="max-h-40 w-full object-contain"></div>
                    <a href="${url}" download="${name}-${i}.${fmt === 'jpeg' ? 'jpg' : 'png'}" class="inline-block rounded-md border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-amber-200 transition hover:bg-white/10">Page ${i} ↓</a>
                </div>`);
                document.getElementById('bar').style.width = Math.round((i / pdf.numPages) * 100) + '%';
            }
            document.getElementById('allBtn').disabled = false;
            showNotification(`Rendered ${pdf.numPages} page(s)!`, 'success');
        } catch (e) { console.error(e); showNotification('Could not render this PDF.', 'error'); }
        finally { btn.disabled = false; document.getElementById('progress').classList.add('hidden'); }
    }

    function downloadAll() {
        if (!images.length) return;
        images.forEach((img, i) => setTimeout(() => { const a = document.createElement('a'); a.href = img.url; a.download = img.name; a.click(); }, i * 150));
        showNotification(`Downloading ${images.length} images…`, 'success');
    }
    function reset() { bytes = null; images = []; document.getElementById('panel').classList.add('hidden'); document.getElementById('grid').innerHTML = ''; document.getElementById('fileInput').value = ''; document.getElementById('allBtn').disabled = true; }
</script>
@endpush
