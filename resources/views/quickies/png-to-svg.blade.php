@extends('layouts.app')

@section('title', 'Image to SVG')
@section('description', 'Trace PNG, JPG, WebP and GIF images into scalable vector graphics.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/imagetracerjs@1.2.6/imagetracer_v1.2.6.js"></script>
@endpush

@section('content')
    <x-tool-header
        title="Image to SVG"
        subtitle="Trace PNG, JPG, WebP and GIF images into scalable vectors."
        from="from-green-500"
        to="to-emerald-500"
        icon="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        {{-- Upload --}}
        <div id="uploadSection">
            <div id="dropZone"
                 class="cursor-pointer rounded-2xl border-2 border-dashed border-emerald-400/40 bg-white/5 px-6 py-12 text-center transition hover:border-emerald-400 hover:bg-white/10"
                 onclick="document.getElementById('fileInput').click()">
                <input type="file" id="fileInput" accept="image/png,image/jpeg,image/webp,image/gif,image/bmp" class="hidden" onchange="handleFileSelect(event)">
                <svg class="mx-auto mb-4 h-16 w-16 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <h3 class="text-xl font-bold text-white">Drop an image here</h3>
                <p class="mt-1 text-slate-400">or click to browse</p>
                <p class="mt-3 text-xs text-slate-500">PNG · JPG · WebP · GIF · BMP</p>
            </div>
        </div>

        {{-- Processing --}}
        <div id="processingSection" class="hidden">
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 sm:p-5">
                    <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300">Original</h3>
                    <div class="checkerboard flex min-h-[240px] items-center justify-center rounded-xl p-3">
                        <img id="originalImage" class="max-h-[360px] max-w-full object-contain" alt="Original">
                    </div>
                    <p id="originalSize" class="mt-3 text-sm text-slate-400"></p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 sm:p-5">
                    <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300">Vector (SVG)</h3>
                    <div id="svgPreview" class="checkerboard flex min-h-[240px] items-center justify-center overflow-hidden rounded-xl p-3 [&_svg]:max-h-[360px] [&_svg]:max-w-full"></div>
                    <p id="svgSize" class="mt-3 text-sm text-slate-400"></p>
                </div>
            </div>

            <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-5">
                <h3 class="mb-5 text-base font-bold text-white">Conversion settings</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-200">Colors</label>
                        <input type="range" id="colorLimit" min="2" max="64" value="16" class="w-full" oninput="updateSettings()">
                        <p class="mt-2 text-center text-sm text-slate-400"><span id="colorLimitValue">16</span> colors</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-200">Detail</label>
                        <input type="range" id="threshold" min="0" max="255" value="128" class="w-full" oninput="updateSettings()">
                        <p class="mt-2 text-center text-sm text-slate-400"><span id="thresholdValue">128</span></p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-200">Smoothing</label>
                        <input type="range" id="blur" min="0" max="10" value="1" class="w-full" oninput="updateSettings()">
                        <p class="mt-2 text-center text-sm text-slate-400"><span id="blurValue">1</span> px</p>
                    </div>
                </div>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <button onclick="convertImage()" class="flex-1 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-3 font-semibold text-white shadow-lg shadow-emerald-500/25 transition hover:scale-[1.02] active:scale-95">Convert to SVG</button>
                    <button onclick="downloadSVG()" id="downloadBtn" disabled class="flex-1 rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-40">Download SVG</button>
                </div>
            </div>

            <div class="mt-5 text-center">
                <button onclick="resetConverter()" class="rounded-xl border border-white/10 px-5 py-2 text-sm text-slate-300 transition hover:bg-white/5 hover:text-white">Upload a different image</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let originalFile = null;
    let svgString = null;

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-emerald-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-emerald-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('border-emerald-400', 'bg-white/10');
        if (e.dataTransfer.files.length) processFile(e.dataTransfer.files[0]);
    });

    function handleFileSelect(e) {
        if (e.target.files.length) processFile(e.target.files[0]);
    }

    function processFile(file) {
        if (!file.type.startsWith('image/') || file.type === 'image/svg+xml') {
            showNotification('Please select a raster image (PNG, JPG, WebP, GIF or BMP).', 'error');
            return;
        }
        originalFile = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            document.getElementById('originalImage').src = e.target.result;
            document.getElementById('originalSize').textContent = `${file.name} · ${formatFileSize(file.size)}`;
            document.getElementById('uploadSection').classList.add('hidden');
            document.getElementById('processingSection').classList.remove('hidden');
            setTimeout(convertImage, 300);
        };
        reader.readAsDataURL(file);
    }

    function updateSettings() {
        document.getElementById('colorLimitValue').textContent = document.getElementById('colorLimit').value;
        document.getElementById('thresholdValue').textContent = document.getElementById('threshold').value;
        document.getElementById('blurValue').textContent = document.getElementById('blur').value;
    }

    function convertImage() {
        showNotification('Tracing image…', 'info');
        const img = document.getElementById('originalImage');
        const blur = parseInt(document.getElementById('blur').value);
        const options = {
            numberofcolors: parseInt(document.getElementById('colorLimit').value),
            colorsampling: 1,
            colorquantcycles: 3,
            ltres: parseInt(document.getElementById('threshold').value) / 255,
            qtres: 1,
            pathomit: 8,
            blurradius: blur,
            blurdelta: 20,
            scale: 1,
        };
        try {
            ImageTracer.imageToSVG(img.src, (svgstr) => {
                svgString = svgstr;
                document.getElementById('svgPreview').innerHTML = svgstr;
                document.getElementById('svgSize').textContent = `SVG · ${formatFileSize(new Blob([svgstr]).size)}`;
                document.getElementById('downloadBtn').disabled = false;
                showNotification('Conversion complete!', 'success');
            }, options);
        } catch (err) {
            console.error(err);
            showNotification('Conversion failed. Try a different image.', 'error');
        }
    }

    function downloadSVG() {
        if (!svgString) return showNotification('Convert the image first.', 'error');
        const blob = new Blob([svgString], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = originalFile.name.replace(/\.[^.]+$/, '') + '.svg';
        a.click();
        URL.revokeObjectURL(url);
        showNotification('SVG downloaded!', 'success');
    }

    function resetConverter() {
        document.getElementById('uploadSection').classList.remove('hidden');
        document.getElementById('processingSection').classList.add('hidden');
        document.getElementById('fileInput').value = '';
        document.getElementById('svgPreview').innerHTML = '';
        document.getElementById('downloadBtn').disabled = true;
        originalFile = null;
        svgString = null;
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }
</script>
@endpush
