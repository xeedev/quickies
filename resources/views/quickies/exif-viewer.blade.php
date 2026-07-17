@extends('layouts.app')

@section('title', 'EXIF Viewer')
@section('description', 'Inspect image EXIF metadata, camera settings and GPS data.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/exifr@7.1.3/dist/full.umd.js"></script>
@endpush

@section('content')
    <x-tool-header title="EXIF Metadata Viewer" subtitle="Inspect camera, lens and GPS metadata embedded in your photos."
        from="from-teal-500" to="to-cyan-500" icon="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div id="dropZone" class="cursor-pointer rounded-2xl border-2 border-dashed border-cyan-400/40 bg-white/5 px-6 py-10 text-center transition hover:border-cyan-400 hover:bg-white/10" onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" accept="image/jpeg,image/tiff,image/png,image/heic,image/webp" class="hidden" onchange="handleFile(event)">
            <svg class="mx-auto mb-3 h-14 w-14 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
            <h3 class="text-lg font-bold text-white">Drop a photo here</h3>
            <p class="mt-1 text-sm text-slate-400">or click to browse — JPEG usually carries the most metadata</p>
        </div>

        <div id="panel" class="mt-6 hidden">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[300px_1fr]">
                <div>
                    <div class="checkerboard overflow-hidden rounded-2xl border border-white/10">
                        <img id="preview" class="w-full object-contain" alt="">
                    </div>
                    <div id="gpsMap" class="mt-3"></div>
                </div>
                <div>
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-wide text-slate-300">Metadata</h3>
                        <button onclick="copyToClipboard(JSON.stringify(window.__exif || {}, null, 2), 'Metadata copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy JSON</button>
                    </div>
                    <div id="meta" class="max-h-[420px] space-y-1.5 overflow-y-auto"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-cyan-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-cyan-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-cyan-400', 'bg-white/10'); if (e.dataTransfer.files.length) loadFile(e.dataTransfer.files[0]); });

    function handleFile(e) { if (e.target.files.length) loadFile(e.target.files[0]); }

    async function loadFile(file) {
        if (!file.type.startsWith('image/')) return showNotification('Please select an image.', 'error');
        document.getElementById('preview').src = URL.createObjectURL(file);
        document.getElementById('panel').classList.remove('hidden');
        showNotification('Reading metadata…', 'info');
        try {
            const data = await exifr.parse(file, { gps: true, xmp: true, iptc: true, tiff: true, exif: true });
            window.__exif = data || {};
            render(file, data);
        } catch (err) { console.error(err); showNotification('Could not read metadata.', 'error'); render(file, null); }
    }

    function fmt(v) {
        if (v instanceof Date) return v.toLocaleString();
        if (Array.isArray(v)) return v.join(', ');
        if (typeof v === 'number') return Number.isInteger(v) ? v : v.toFixed(4);
        return String(v);
    }

    function render(file, data) {
        const meta = document.getElementById('meta');
        const gpsMap = document.getElementById('gpsMap');
        gpsMap.innerHTML = '';
        const rows = [['File name', file.name], ['File size', (file.size / 1024).toFixed(1) + ' KB'], ['MIME type', file.type]];
        if (data) {
            const keys = ['Make', 'Model', 'LensModel', 'DateTimeOriginal', 'ExposureTime', 'FNumber', 'ISO', 'FocalLength', 'ExposureProgram', 'Flash', 'Orientation', 'ImageWidth', 'ImageHeight', 'Software', 'latitude', 'longitude'];
            keys.forEach((k) => { if (data[k] !== undefined) rows.push([k, fmt(data[k])]); });
            Object.entries(data).forEach(([k, v]) => { if (!keys.includes(k) && typeof v !== 'object') rows.push([k, fmt(v)]); });
            if (data.latitude && data.longitude) {
                gpsMap.innerHTML = `<a href="https://www.google.com/maps?q=${data.latitude},${data.longitude}" target="_blank" rel="noopener" class="block rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-center text-sm font-semibold text-cyan-300 transition hover:bg-white/10">📍 View location on map</a>`;
            }
        }
        meta.innerHTML = rows.map(([k, v]) => `<div class="flex items-start justify-between gap-4 rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm"><span class="text-slate-400">${k}</span><span class="min-w-0 break-all text-right font-mono text-slate-200">${String(v).replace(/</g, '&lt;')}</span></div>`).join('');
        if (!data || Object.keys(data).length === 0) meta.innerHTML += '<div class="mt-2 rounded-lg border border-amber-400/20 bg-amber-400/5 px-3 py-2 text-sm text-amber-200">No EXIF metadata found — it may have been stripped by the app that exported this image.</div>';
    }
</script>
@endpush
