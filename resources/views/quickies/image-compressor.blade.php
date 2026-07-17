@extends('layouts.app')

@section('title', 'Image Compressor')
@section('description', 'Compress JPG, PNG and WebP images while keeping quality.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
@endpush

@section('content')
    <x-tool-header
        title="Image Compressor"
        subtitle="Shrink JPG, PNG and WebP images while keeping quality."
        from="from-blue-500"
        to="to-cyan-500"
        icon="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        {{-- Upload --}}
        <div id="uploadSection">
            <div id="dropZone"
                 class="cursor-pointer rounded-2xl border-2 border-dashed border-cyan-400/40 bg-white/5 px-6 py-12 text-center transition hover:border-cyan-400 hover:bg-white/10"
                 onclick="document.getElementById('fileInput').click()">
                <input type="file" id="fileInput" accept="image/jpeg,image/png,image/webp" multiple class="hidden" onchange="handleFileSelect(event)">
                <svg class="mx-auto mb-4 h-16 w-16 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                <h3 class="text-xl font-bold text-white">Drop images here</h3>
                <p class="mt-1 text-slate-400">or click to browse — multiple files allowed</p>
                <p class="mt-3 text-xs text-slate-500">JPG · PNG · WebP</p>
            </div>
        </div>

        {{-- Processing --}}
        <div id="processingSection" class="hidden">
            <div class="mb-6 rounded-2xl border border-white/10 bg-white/5 p-5">
                <h3 class="mb-5 text-base font-bold text-white">Compression settings</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-200">Quality</label>
                        <input type="range" id="quality" min="10" max="100" value="80" class="w-full" oninput="updateQuality()">
                        <p class="mt-2 text-center text-sm text-slate-400"><span id="qualityValue">80</span>%</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-200">Max width (px)</label>
                        <input type="number" id="maxWidth" value="0" min="0" step="100"
                               class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white transition focus:border-cyan-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                        <p class="mt-2 text-center text-xs text-slate-500">0 = keep original size</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-200">Output format</label>
                        <select id="outputFormat" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white transition focus:border-cyan-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
                            <option value="" class="bg-slate-900">Keep original</option>
                            <option value="image/jpeg" class="bg-slate-900">JPG</option>
                            <option value="image/png" class="bg-slate-900">PNG</option>
                            <option value="image/webp" class="bg-slate-900">WebP</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <button onclick="compressAll()" class="flex-1 rounded-2xl bg-gradient-to-r from-blue-500 to-cyan-500 px-6 py-3 font-semibold text-white shadow-lg shadow-cyan-500/25 transition hover:scale-[1.02] active:scale-95">Compress all</button>
                    <button onclick="downloadAll()" id="downloadAllBtn" disabled class="flex-1 rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-40">Download all</button>
                </div>
            </div>

            <div id="imagesGrid" class="grid grid-cols-1 gap-5"></div>

            <div class="mt-5 text-center">
                <button onclick="resetCompressor()" class="rounded-xl border border-white/10 px-5 py-2 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white">Upload different images</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let uploadedFiles = [];
    let compressedImages = [];

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-cyan-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-cyan-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-cyan-400', 'bg-white/10');
        processFiles(Array.from(e.dataTransfer.files));
    });

    function handleFileSelect(e) { processFiles(Array.from(e.target.files)); }

    function processFiles(files) {
        const valid = files.filter((f) => ['image/jpeg', 'image/png', 'image/webp'].includes(f.type));
        if (valid.length === 0) return showNotification('Please select JPG, PNG or WebP images.', 'error');
        uploadedFiles = valid;
        compressedImages = new Array(valid.length).fill(null);
        document.getElementById('uploadSection').classList.add('hidden');
        document.getElementById('processingSection').classList.remove('hidden');
        displayImages();
        updateDownloadAllButton();
    }

    function displayImages() {
        const grid = document.getElementById('imagesGrid');
        grid.innerHTML = '';
        uploadedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => grid.appendChild(createImageCard(file, e.target.result, index));
            reader.readAsDataURL(file);
        });
    }

    function createImageCard(file, dataUrl, index) {
        const div = document.createElement('div');
        div.className = 'rounded-2xl border border-white/10 bg-white/5 p-4 sm:p-5';
        div.id = `image-${index}`;
        div.innerHTML = `
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div>
                    <h4 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300">Original</h4>
                    <div class="checkerboard mb-3 flex h-44 items-center justify-center rounded-xl p-2">
                        <img src="${dataUrl}" class="max-h-full max-w-full object-contain" alt="">
                    </div>
                    <div class="space-y-1 text-sm text-slate-400">
                        <div class="truncate"><span class="text-slate-500">Name:</span> ${file.name}</div>
                        <div><span class="text-slate-500">Size:</span> ${formatFileSize(file.size)} · ${file.type.split('/')[1].toUpperCase()}</div>
                    </div>
                </div>
                <div>
                    <h4 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300">Compressed</h4>
                    <div id="compressed-preview-${index}" class="checkerboard mb-3 flex h-44 items-center justify-center rounded-xl p-2 text-sm text-slate-500">Not compressed yet</div>
                    <div id="compressed-info-${index}" class="space-y-1 text-sm text-slate-500">Compress to see results</div>
                </div>
            </div>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <button onclick="compressImage(${index})" class="flex-1 rounded-xl bg-blue-500/20 px-4 py-2.5 text-sm font-semibold text-blue-200 transition hover:bg-blue-500/30">Compress</button>
                <button onclick="downloadImage(${index})" id="download-${index}" disabled class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-40">Download</button>
            </div>`;
        return div;
    }

    function targetExtension(mime, fallbackName) {
        if (mime === 'image/jpeg') return '.jpg';
        if (mime === 'image/png') return '.png';
        if (mime === 'image/webp') return '.webp';
        const m = fallbackName.match(/\.[^.]+$/);
        return m ? m[0] : '';
    }

    async function compressImage(index) {
        const file = uploadedFiles[index];
        const quality = parseInt(document.getElementById('quality').value) / 100;
        const maxWidth = parseInt(document.getElementById('maxWidth').value) || 0;
        const fileType = document.getElementById('outputFormat').value || undefined;
        showNotification(`Compressing ${file.name}…`, 'info');
        const options = {
            maxSizeMB: 30,
            maxWidthOrHeight: maxWidth > 0 ? maxWidth : undefined,
            useWebWorker: true,
            initialQuality: quality,
            fileType,
        };
        try {
            const compressed = await imageCompression(file, options);
            compressedImages[index] = compressed;
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById(`compressed-preview-${index}`).innerHTML = `<img src="${e.target.result}" class="max-h-full max-w-full object-contain" alt="">`;
                const reduction = ((1 - compressed.size / file.size) * 100).toFixed(1);
                const positive = compressed.size < file.size;
                document.getElementById(`compressed-info-${index}`).innerHTML = `
                    <div class="text-slate-400"><span class="text-slate-500">Size:</span> ${formatFileSize(compressed.size)} · ${(compressed.type.split('/')[1] || '').toUpperCase()}</div>
                    <div class="font-semibold ${positive ? 'text-emerald-400' : 'text-amber-400'}">${positive ? 'Saved' : 'Increased'} ${Math.abs(reduction)}% (${formatFileSize(Math.abs(file.size - compressed.size))})</div>`;
                document.getElementById(`download-${index}`).disabled = false;
                updateDownloadAllButton();
            };
            reader.readAsDataURL(compressed);
            showNotification(`${file.name} compressed!`, 'success');
        } catch (err) {
            console.error(err);
            showNotification(`Error compressing ${file.name}.`, 'error');
        }
    }

    async function compressAll() {
        for (let i = 0; i < uploadedFiles.length; i++) {
            if (!compressedImages[i]) await compressImage(i);
        }
    }

    function downloadImage(index) {
        const img = compressedImages[index];
        if (!img) return showNotification('Compress the image first.', 'error');
        const base = uploadedFiles[index].name.replace(/\.[^.]+$/, '');
        const url = URL.createObjectURL(img);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${base}-min${targetExtension(img.type, uploadedFiles[index].name)}`;
        a.click();
        URL.revokeObjectURL(url);
    }

    function downloadAll() {
        const ready = compressedImages.map((v, i) => (v ? i : -1)).filter((i) => i >= 0);
        if (ready.length === 0) return showNotification('Compress at least one image first.', 'error');
        ready.forEach((i, n) => setTimeout(() => downloadImage(i), n * 150));
        showNotification(`Downloading ${ready.length} image${ready.length > 1 ? 's' : ''}…`, 'success');
    }

    function updateDownloadAllButton() {
        const any = compressedImages.some((img) => img !== null);
        document.getElementById('downloadAllBtn').disabled = !any;
    }

    function updateQuality() {
        document.getElementById('qualityValue').textContent = document.getElementById('quality').value;
    }

    function resetCompressor() {
        document.getElementById('uploadSection').classList.remove('hidden');
        document.getElementById('processingSection').classList.add('hidden');
        document.getElementById('fileInput').value = '';
        document.getElementById('imagesGrid').innerHTML = '';
        uploadedFiles = [];
        compressedImages = [];
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }
</script>
@endpush
