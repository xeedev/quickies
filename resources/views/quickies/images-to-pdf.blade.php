@extends('layouts.app')

@section('title', 'Images to PDF')
@section('description', 'Combine multiple images into a single downloadable PDF.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
@endpush

@section('content')
    <x-tool-header
        title="Images to PDF"
        subtitle="Combine JPG, PNG and WebP images into one PDF — reorder as you like."
        from="from-red-500"
        to="to-orange-500"
        icon="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div id="dropZone"
             class="cursor-pointer rounded-2xl border-2 border-dashed border-orange-400/40 bg-white/5 px-6 py-10 text-center transition hover:border-orange-400 hover:bg-white/10"
             onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" accept="image/jpeg,image/png,image/webp" multiple class="hidden" onchange="handleFiles(event)">
            <svg class="mx-auto mb-3 h-14 w-14 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <h3 class="text-lg font-bold text-white">Drop images here</h3>
            <p class="mt-1 text-sm text-slate-400">or click to browse — add as many as you like</p>
        </div>

        <div id="settings" class="mt-6 hidden">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Page size</label>
                    <select id="pageSize" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-orange-400/60 focus:outline-none">
                        <option value="fit" class="bg-slate-900">Fit to image</option>
                        <option value="a4" class="bg-slate-900">A4</option>
                        <option value="letter" class="bg-slate-900">US Letter</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Orientation</label>
                    <select id="orientation" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-orange-400/60 focus:outline-none">
                        <option value="auto" class="bg-slate-900">Auto (per image)</option>
                        <option value="portrait" class="bg-slate-900">Portrait</option>
                        <option value="landscape" class="bg-slate-900">Landscape</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Margin <span id="marginValue" class="text-slate-400">10</span> mm</label>
                    <input type="range" id="margin" min="0" max="30" value="10" class="w-full" oninput="document.getElementById('marginValue').textContent = this.value">
                </div>
            </div>

            <h3 class="mb-3 mt-6 text-sm font-bold uppercase tracking-wide text-slate-300"><span id="imgCount">0</span> images</h3>
            <div id="imageList" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"></div>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button onclick="generatePdf()" class="flex-1 rounded-2xl bg-gradient-to-r from-red-500 to-orange-500 px-6 py-3 font-semibold text-white shadow-lg shadow-orange-500/25 transition hover:scale-[1.01] active:scale-95">Create PDF</button>
                <button onclick="reset()" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-slate-200 transition hover:bg-white/10">Clear all</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let images = []; // { dataUrl, name, w, h }

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-orange-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-orange-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-orange-400', 'bg-white/10'); handleList(Array.from(e.dataTransfer.files)); });

    function handleFiles(e) { handleList(Array.from(e.target.files)); e.target.value = ''; }

    function handleList(files) {
        const valid = files.filter((f) => ['image/jpeg', 'image/png', 'image/webp'].includes(f.type));
        if (!valid.length) return showNotification('Please add JPG, PNG or WebP images.', 'error');
        let pending = valid.length;
        valid.forEach((file) => {
            const reader = new FileReader();
            reader.onload = (ev) => {
                const img = new Image();
                img.onload = () => {
                    images.push({ dataUrl: ev.target.result, name: file.name, w: img.width, h: img.height, type: file.type });
                    if (--pending === 0) renderList();
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
        document.getElementById('settings').classList.remove('hidden');
    }

    function renderList() {
        document.getElementById('imgCount').textContent = images.length;
        document.getElementById('imageList').innerHTML = images.map((im, i) => `
            <div class="group relative overflow-hidden rounded-xl border border-white/10 bg-white/5">
                <div class="checkerboard flex h-28 items-center justify-center p-2"><img src="${im.dataUrl}" class="max-h-full max-w-full object-contain" alt=""></div>
                <div class="flex items-center justify-between gap-1 px-2 py-1.5">
                    <span class="truncate text-xs text-slate-400">${i + 1}. ${im.name}</span>
                    <div class="flex flex-shrink-0 gap-1">
                        <button onclick="move(${i}, -1)" ${i === 0 ? 'disabled' : ''} class="rounded p-1 text-slate-300 transition hover:bg-white/10 disabled:opacity-30" title="Move up">↑</button>
                        <button onclick="move(${i}, 1)" ${i === images.length - 1 ? 'disabled' : ''} class="rounded p-1 text-slate-300 transition hover:bg-white/10 disabled:opacity-30" title="Move down">↓</button>
                        <button onclick="removeImg(${i})" class="rounded p-1 text-rose-300 transition hover:bg-rose-500/20" title="Remove">✕</button>
                    </div>
                </div>
            </div>`).join('');
    }

    function move(i, dir) {
        const j = i + dir;
        if (j < 0 || j >= images.length) return;
        [images[i], images[j]] = [images[j], images[i]];
        renderList();
    }
    function removeImg(i) { images.splice(i, 1); renderList(); if (!images.length) reset(); }

    function reset() {
        images = [];
        document.getElementById('imageList').innerHTML = '';
        document.getElementById('settings').classList.add('hidden');
    }

    function generatePdf() {
        if (!images.length) return showNotification('Add at least one image.', 'error');
        showNotification('Building PDF…', 'info');
        const { jsPDF } = window.jspdf;
        const sizeOpt = document.getElementById('pageSize').value;
        const orientOpt = document.getElementById('orientation').value;
        const margin = parseFloat(document.getElementById('margin').value);
        let pdf = null;

        images.forEach((im, idx) => {
            const imgRatio = im.w / im.h;
            const autoOrient = imgRatio >= 1 ? 'landscape' : 'portrait';
            const orient = orientOpt === 'auto' ? autoOrient : orientOpt;
            let format;
            if (sizeOpt === 'fit') {
                // Page matches image at 96 DPI converted to mm.
                const pxToMm = 25.4 / 96;
                format = [im.w * pxToMm, im.h * pxToMm];
            } else {
                format = sizeOpt;
            }

            if (idx === 0) pdf = new jsPDF({ orientation: sizeOpt === 'fit' ? (imgRatio >= 1 ? 'l' : 'p') : orient, unit: 'mm', format });
            else pdf.addPage(format, sizeOpt === 'fit' ? (imgRatio >= 1 ? 'l' : 'p') : orient);

            const pw = pdf.internal.pageSize.getWidth();
            const ph = pdf.internal.pageSize.getHeight();
            const m = sizeOpt === 'fit' ? 0 : margin;
            const availW = pw - m * 2;
            const availH = ph - m * 2;
            let drawW = availW, drawH = availW / imgRatio;
            if (drawH > availH) { drawH = availH; drawW = availH * imgRatio; }
            const x = (pw - drawW) / 2;
            const y = (ph - drawH) / 2;
            const fmt = im.type === 'image/png' ? 'PNG' : im.type === 'image/webp' ? 'WEBP' : 'JPEG';
            pdf.addImage(im.dataUrl, fmt, x, y, drawW, drawH, undefined, 'FAST');
        });

        pdf.save('images.pdf');
        showNotification('PDF created!', 'success');
    }
</script>
@endpush
