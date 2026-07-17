@extends('layouts.app')

@section('title', 'Image Cropper')
@section('description', 'Crop, rotate and flip images, then export to PNG, JPG or WebP.')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css">
<style>
    .cropper-view-box { outline-color: rgba(249, 115, 22, 0.9); }
    .cropper-line, .cropper-point { background-color: rgb(249, 115, 22); }
    .cropper-point { width: 8px; height: 8px; }
</style>
@endpush

@section('content')
    <x-tool-header
        title="Image Cropper"
        subtitle="Crop, rotate and flip any image, then export to PNG, JPG or WebP."
        from="from-orange-500"
        to="to-amber-500"
        icon="M3 4h3M3 4v3m18-3h-3m3 0v3M3 20h3m-3 0v-3m18 3h-3m3 0v-3M8 8h8v8H8z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        {{-- Upload --}}
        <div id="uploadSection">
            <div id="dropZone"
                 class="cursor-pointer rounded-2xl border-2 border-dashed border-orange-400/40 bg-white/5 px-6 py-12 text-center transition hover:border-orange-400 hover:bg-white/10"
                 onclick="document.getElementById('fileInput').click()">
                <input type="file" id="fileInput" accept="image/png,image/jpeg,image/webp,image/gif,image/bmp,image/avif" class="hidden" onchange="handleFileSelect(event)">
                <svg class="mx-auto mb-4 h-16 w-16 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <h3 class="text-xl font-bold text-white">Drop an image here</h3>
                <p class="mt-1 text-slate-400">or click to browse</p>
                <p class="mt-3 text-xs text-slate-500">PNG · JPG · WebP · GIF · BMP · AVIF</p>
            </div>
        </div>

        {{-- Cropping --}}
        <div id="croppingSection" class="hidden">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_320px]">
                {{-- Cropper --}}
                <div class="rounded-2xl border border-white/10 bg-black/30 p-3">
                    <div class="checkerboard overflow-hidden rounded-xl">
                        <img id="cropImage" class="block max-w-full" style="max-height: 60vh;" alt="Crop">
                    </div>
                </div>

                {{-- Controls --}}
                <div class="space-y-5">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300">Aspect ratio</h3>
                        <div class="grid grid-cols-4 gap-2">
                            <button data-ratio onclick="setAspectRatio(this, NaN)" class="aspect-btn rounded-lg border border-orange-400/40 bg-orange-500/20 px-2 py-2 text-xs font-semibold text-orange-200 transition">Free</button>
                            <button data-ratio onclick="setAspectRatio(this, 1)" class="aspect-btn rounded-lg border border-white/10 bg-white/5 px-2 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/10">1:1</button>
                            <button data-ratio onclick="setAspectRatio(this, 16/9)" class="aspect-btn rounded-lg border border-white/10 bg-white/5 px-2 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/10">16:9</button>
                            <button data-ratio onclick="setAspectRatio(this, 4/3)" class="aspect-btn rounded-lg border border-white/10 bg-white/5 px-2 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/10">4:3</button>
                            <button data-ratio onclick="setAspectRatio(this, 3/2)" class="aspect-btn rounded-lg border border-white/10 bg-white/5 px-2 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/10">3:2</button>
                            <button data-ratio onclick="setAspectRatio(this, 2/3)" class="aspect-btn rounded-lg border border-white/10 bg-white/5 px-2 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/10">2:3</button>
                            <button data-ratio onclick="setAspectRatio(this, 9/16)" class="aspect-btn rounded-lg border border-white/10 bg-white/5 px-2 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/10">9:16</button>
                            <button data-ratio onclick="setAspectRatio(this, 21/9)" class="aspect-btn rounded-lg border border-white/10 bg-white/5 px-2 py-2 text-xs font-semibold text-slate-300 transition hover:bg-white/10">21:9</button>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300">Transform</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="rotate(-90)" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">↺ Rotate left</button>
                            <button onclick="rotate(90)" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">↻ Rotate right</button>
                            <button onclick="flipHorizontal()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">⇋ Flip H</button>
                            <button onclick="flipVertical()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">⇅ Flip V</button>
                            <button onclick="zoomBy(0.1)" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">＋ Zoom in</button>
                            <button onclick="zoomBy(-0.1)" class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">－ Zoom out</button>
                            <button onclick="resetCrop()" class="col-span-2 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Reset transforms</button>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                        <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300">Export</h3>
                        <div class="mb-3 flex items-center justify-between rounded-lg bg-white/5 px-3 py-2 text-sm">
                            <span class="text-slate-400">Selection</span>
                            <span class="font-mono text-white"><span id="cropWidth">0</span> × <span id="cropHeight">0</span> px</span>
                        </div>
                        <label class="mb-2 block text-xs font-semibold text-slate-300">Output format</label>
                        <select id="outputFormat" onchange="toggleQuality()" class="mb-3 w-full rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm text-white transition focus:border-orange-400/60 focus:outline-none">
                            <option value="image/png" class="bg-slate-900">PNG (transparency)</option>
                            <option value="image/jpeg" class="bg-slate-900">JPG</option>
                            <option value="image/webp" class="bg-slate-900">WebP</option>
                        </select>
                        <div id="qualityWrap" class="mb-3 hidden">
                            <label class="mb-2 block text-xs font-semibold text-slate-300">Quality <span id="qualityValue">92</span>%</label>
                            <input type="range" id="quality" min="10" max="100" value="92" class="w-full" oninput="document.getElementById('qualityValue').textContent = this.value">
                        </div>
                        <button onclick="cropAndDownload()" class="w-full rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 px-4 py-3 font-semibold text-white shadow-lg shadow-orange-500/25 transition hover:scale-[1.02] active:scale-95">Crop &amp; download</button>
                    </div>

                    <button onclick="resetCropper()" class="w-full rounded-xl border border-white/10 px-4 py-2.5 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white">Upload a different image</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<script>
    let cropper = null;
    let originalFileName = 'image';
    let scaleX = 1;
    let scaleY = 1;

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-orange-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-orange-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-orange-400', 'bg-white/10');
        if (e.dataTransfer.files.length) processFile(e.dataTransfer.files[0]);
    });

    function handleFileSelect(e) { if (e.target.files.length) processFile(e.target.files[0]); }

    function processFile(file) {
        if (!file.type.startsWith('image/') || file.type === 'image/svg+xml') {
            return showNotification('Please select a raster image file.', 'error');
        }
        originalFileName = file.name.replace(/\.[^.]+$/, '') || 'image';
        const reader = new FileReader();
        reader.onload = (e) => {
            const image = document.getElementById('cropImage');
            image.src = e.target.result;
            document.getElementById('uploadSection').classList.add('hidden');
            document.getElementById('croppingSection').classList.remove('hidden');
            image.onload = () => {
                if (cropper) cropper.destroy();
                scaleX = 1; scaleY = 1;
                cropper = new Cropper(image, {
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.85,
                    background: false,
                    responsive: true,
                    crop(event) {
                        document.getElementById('cropWidth').textContent = Math.round(event.detail.width);
                        document.getElementById('cropHeight').textContent = Math.round(event.detail.height);
                    },
                });
            };
        };
        reader.readAsDataURL(file);
    }

    function setAspectRatio(btn, ratio) {
        if (cropper) cropper.setAspectRatio(ratio);
        document.querySelectorAll('[data-ratio]').forEach((b) => {
            b.classList.remove('bg-orange-500/20', 'text-orange-200', 'border-orange-400/40');
            b.classList.add('bg-white/5', 'text-slate-300', 'border-white/10');
        });
        btn.classList.remove('bg-white/5', 'text-slate-300', 'border-white/10');
        btn.classList.add('bg-orange-500/20', 'text-orange-200', 'border-orange-400/40');
    }

    function rotate(deg) { if (cropper) cropper.rotate(deg); }
    function zoomBy(v) { if (cropper) cropper.zoom(v); }
    function flipHorizontal() { if (cropper) { scaleX = -scaleX; cropper.scaleX(scaleX); } }
    function flipVertical() { if (cropper) { scaleY = -scaleY; cropper.scaleY(scaleY); } }
    function resetCrop() { if (cropper) { cropper.reset(); scaleX = 1; scaleY = 1; } }

    function toggleQuality() {
        const lossy = document.getElementById('outputFormat').value !== 'image/png';
        document.getElementById('qualityWrap').classList.toggle('hidden', !lossy);
    }

    function cropAndDownload() {
        if (!cropper) return showNotification('Load an image first.', 'error');
        const format = document.getElementById('outputFormat').value;
        const quality = parseInt(document.getElementById('quality').value) / 100;
        showNotification('Processing…', 'info');

        let canvas = cropper.getCroppedCanvas({ imageSmoothingEnabled: true, imageSmoothingQuality: 'high' });

        // JPG has no alpha channel — flatten transparency onto white.
        if (format === 'image/jpeg') {
            const flat = document.createElement('canvas');
            flat.width = canvas.width;
            flat.height = canvas.height;
            const ctx = flat.getContext('2d');
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, flat.width, flat.height);
            ctx.drawImage(canvas, 0, 0);
            canvas = flat;
        }

        const ext = format === 'image/jpeg' ? 'jpg' : format === 'image/webp' ? 'webp' : 'png';
        canvas.toBlob((blob) => {
            if (!blob) return showNotification('Export failed for this format.', 'error');
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${originalFileName}-cropped.${ext}`;
            a.click();
            URL.revokeObjectURL(url);
            showNotification('Image downloaded!', 'success');
        }, format, format === 'image/png' ? undefined : quality);
    }

    function resetCropper() {
        if (cropper) { cropper.destroy(); cropper = null; }
        document.getElementById('uploadSection').classList.remove('hidden');
        document.getElementById('croppingSection').classList.add('hidden');
        document.getElementById('fileInput').value = '';
        document.getElementById('cropImage').src = '';
        scaleX = 1; scaleY = 1;
    }
</script>
@endpush
