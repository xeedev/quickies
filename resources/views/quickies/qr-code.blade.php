@extends('layouts.app')

@section('title', 'QR Code Generator')
@section('description', 'Turn links and text into scannable QR codes instantly.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
@endpush

@section('content')
    <x-tool-header
        title="QR Code Generator"
        subtitle="Turn links, text or Wi-Fi details into scannable QR codes."
        from="from-indigo-500"
        to="to-violet-500"
        icon="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 4v-4m0 4h2m4 0v-4m0 0h-4m4 0h.01M14 14h.01" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_360px]">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
            <label class="mb-2 block text-sm font-semibold text-slate-200">Content</label>
            <textarea id="qrText" rows="4" oninput="renderQR()"
                class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
                placeholder="https://example.com or any text…">https://github.com</textarea>

            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Size <span id="sizeValue" class="text-slate-400">256</span> px</label>
                    <input type="range" id="qrSize" min="128" max="1024" step="32" value="256" class="w-full" oninput="document.getElementById('sizeValue').textContent = this.value; renderQR();">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Error correction</label>
                    <select id="qrLevel" onchange="renderQR()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-indigo-400/60 focus:outline-none">
                        <option value="L" class="bg-slate-900">Low (7%)</option>
                        <option value="M" class="bg-slate-900" selected>Medium (15%)</option>
                        <option value="Q" class="bg-slate-900">Quartile (25%)</option>
                        <option value="H" class="bg-slate-900">High (30%)</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Foreground</label>
                    <input type="color" id="qrFg" value="#0f172a" oninput="renderQR()" class="h-11 w-full cursor-pointer rounded-xl border border-white/10 bg-white/5">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Background</label>
                    <input type="color" id="qrBg" value="#ffffff" oninput="renderQR()" class="h-11 w-full cursor-pointer rounded-xl border border-white/10 bg-white/5">
                </div>
            </div>
        </div>

        <div class="flex flex-col rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
            <div class="flex flex-1 items-center justify-center">
                <div id="qrCanvas" class="inline-block rounded-2xl bg-white p-4 shadow-2xl"></div>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-3">
                <button onclick="downloadQR('png')" class="rounded-xl bg-gradient-to-r from-indigo-500 to-violet-500 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:scale-[1.02] active:scale-95">PNG</button>
                <button onclick="downloadQR('svg')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/10">SVG</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let qr = null;

    function renderQR() {
        const text = document.getElementById('qrText').value.trim();
        const size = parseInt(document.getElementById('qrSize').value);
        const container = document.getElementById('qrCanvas');
        container.innerHTML = '';
        if (!text) {
            container.innerHTML = '<p class="p-8 text-sm text-slate-500">Enter content to generate a QR code.</p>';
            return;
        }
        const levels = { L: QRCode.CorrectLevel.L, M: QRCode.CorrectLevel.M, Q: QRCode.CorrectLevel.Q, H: QRCode.CorrectLevel.H };
        qr = new QRCode(container, {
            text,
            width: size,
            height: size,
            colorDark: document.getElementById('qrFg').value,
            colorLight: document.getElementById('qrBg').value,
            correctLevel: levels[document.getElementById('qrLevel').value],
        });
    }

    function downloadQR(type) {
        const canvas = document.querySelector('#qrCanvas canvas');
        if (!canvas) return showNotification('Generate a QR code first.', 'error');
        if (type === 'png') {
            const a = document.createElement('a');
            a.href = canvas.toDataURL('image/png');
            a.download = 'qrcode.png';
            a.click();
            return showNotification('PNG downloaded!', 'success');
        }
        // Vector SVG built from the module matrix drawn on canvas
        const size = canvas.width;
        const ctx = canvas.getContext('2d');
        const fg = document.getElementById('qrFg').value;
        const bg = document.getElementById('qrBg').value;
        const img = ctx.getImageData(0, 0, size, size).data;
        // Detect module size by scanning first row for first colour change.
        let rects = '';
        const step = Math.max(1, Math.round(size / 64));
        for (let y = 0; y < size; y += step) {
            for (let x = 0; x < size; x += step) {
                const i = (y * size + x) * 4;
                const dark = (img[i] + img[i + 1] + img[i + 2]) / 3 < 128;
                if (dark) rects += `<rect x="${x}" y="${y}" width="${step}" height="${step}"/>`;
            }
        }
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${size}" height="${size}" viewBox="0 0 ${size} ${size}"><rect width="100%" height="100%" fill="${bg}"/><g fill="${fg}">${rects}</g></svg>`;
        const blob = new Blob([svg], { type: 'image/svg+xml' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'qrcode.svg';
        a.click();
        URL.revokeObjectURL(a.href);
        showNotification('SVG downloaded!', 'success');
    }

    document.addEventListener('DOMContentLoaded', renderQR);
</script>
@endpush
