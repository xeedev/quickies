@extends('layouts.app')

@section('title', 'Favicon Generator')
@section('description', 'Generate favicons in every size from a single image.')

@section('content')
    <x-tool-header title="Favicon Generator" subtitle="Create favicons in every size from one image, plus the HTML."
        from="from-cyan-500" to="to-blue-500" icon="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div id="dropZone" class="cursor-pointer rounded-2xl border-2 border-dashed border-blue-400/40 bg-white/5 px-6 py-10 text-center transition hover:border-blue-400 hover:bg-white/10" onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="hidden" onchange="handleFile(event)">
            <svg class="mx-auto mb-3 h-14 w-14 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <h3 class="text-lg font-bold text-white">Drop a square image here</h3>
            <p class="mt-1 text-sm text-slate-400">or click to browse — 512×512 or larger works best</p>
        </div>

        <div id="panel" class="mt-6 hidden">
            <div class="mb-4 flex items-center gap-3">
                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200"><input type="checkbox" id="rounded" onchange="regen()" class="h-4 w-4 accent-blue-500"> Rounded corners</label>
                <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">Background <input type="color" id="bg" value="#ffffff" oninput="regen()" class="h-6 w-8 cursor-pointer rounded border-0 bg-transparent"></label>
                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200"><input type="checkbox" id="transparent" checked onchange="regen()" class="h-4 w-4 accent-blue-500"> Transparent</label>
            </div>
            <div id="sizes" class="grid grid-cols-3 gap-4 sm:grid-cols-4 lg:grid-cols-6"></div>

            <div class="mt-6">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-200">HTML snippet</span>
                    <button onclick="copyToClipboard(document.getElementById('html').textContent, 'HTML copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <pre id="html" class="overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-xs text-blue-100"></pre>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const SIZES = [16, 32, 48, 64, 96, 128, 180, 192, 256, 512];
    let sourceImg = null;

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-blue-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-blue-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-blue-400', 'bg-white/10'); if (e.dataTransfer.files.length) loadFile(e.dataTransfer.files[0]); });

    function handleFile(e) { if (e.target.files.length) loadFile(e.target.files[0]); }

    function loadFile(file) {
        if (!file.type.startsWith('image/')) return showNotification('Please select an image.', 'error');
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => { sourceImg = img; document.getElementById('panel').classList.remove('hidden'); regen(); };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function draw(size) {
        const canvas = document.createElement('canvas');
        canvas.width = canvas.height = size;
        const ctx = canvas.getContext('2d');
        const rounded = document.getElementById('rounded').checked;
        const transparent = document.getElementById('transparent').checked;
        if (rounded) { const r = size * 0.2; ctx.beginPath(); ctx.moveTo(r, 0); ctx.arcTo(size, 0, size, size, r); ctx.arcTo(size, size, 0, size, r); ctx.arcTo(0, size, 0, 0, r); ctx.arcTo(0, 0, size, 0, r); ctx.closePath(); ctx.clip(); }
        if (!transparent) { ctx.fillStyle = document.getElementById('bg').value; ctx.fillRect(0, 0, size, size); }
        // cover
        const scale = Math.max(size / sourceImg.width, size / sourceImg.height);
        const w = sourceImg.width * scale, h = sourceImg.height * scale;
        ctx.drawImage(sourceImg, (size - w) / 2, (size - h) / 2, w, h);
        return canvas;
    }

    function regen() {
        document.getElementById('sizes').innerHTML = SIZES.map((s) => {
            const canvas = draw(s);
            const url = canvas.toDataURL('image/png');
            return `<div class="rounded-2xl border border-white/10 bg-white/5 p-3 text-center">
                <div class="checkerboard mx-auto mb-2 flex h-20 items-center justify-center rounded-lg"><img src="${url}" style="width:${Math.min(s,64)}px;height:${Math.min(s,64)}px;image-rendering:auto"></div>
                <div class="text-xs font-semibold text-slate-300">${s}×${s}</div>
                <a href="${url}" download="favicon-${s}.png" class="mt-1 inline-block rounded-md border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold text-blue-200 transition hover:bg-white/10">PNG</a>
            </div>`;
        }).join('');
        document.getElementById('html').textContent =
`<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/favicon-180.png">
<link rel="icon" type="image/png" sizes="192x192" href="/favicon-192.png">`;
    }
</script>
@endpush
