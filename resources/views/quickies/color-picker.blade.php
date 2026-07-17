@extends('layouts.app')

@section('title', 'Color Picker')
@section('description', 'Pick colors, sample from your screen and export hex, RGB and HSL.')

@section('content')
    <x-tool-header
        title="Color Picker"
        subtitle="Pick a color, sample from your screen, and copy hex, RGB or HSL."
        from="from-pink-500"
        to="to-rose-500"
        icon="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[360px_1fr]">
        {{-- Picker --}}
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <div id="preview" class="mb-5 flex h-40 items-center justify-center rounded-2xl border border-white/10 shadow-inner" style="background:#ec4899;">
                <span id="previewHex" class="rounded-lg bg-black/30 px-3 py-1 font-mono text-sm font-bold text-white">#EC4899</span>
            </div>
            <div class="flex items-center gap-3">
                <input type="color" id="colorInput" value="#ec4899" class="h-12 w-16 flex-shrink-0 cursor-pointer rounded-xl border border-white/10 bg-white/5" oninput="setColor(this.value)">
                <button id="eyedropper" onclick="sampleScreen()" class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-pink-500 to-rose-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-rose-500/25 transition hover:scale-[1.02] active:scale-95">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Sample screen
                </button>
            </div>
            <button onclick="randomColor()" class="mt-3 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Random color</button>
        </div>

        {{-- Values --}}
        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div id="fmt-hex" class="rounded-2xl border border-white/10 bg-white/5 p-4"></div>
                <div id="fmt-rgb" class="rounded-2xl border border-white/10 bg-white/5 p-4"></div>
                <div id="fmt-hsl" class="rounded-2xl border border-white/10 bg-white/5 p-4"></div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300">Shades &amp; tints</h3>
                <div id="shades" class="grid grid-cols-11 gap-1.5"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function hexToRgb(hex) {
        const m = hex.replace('#', '').match(/.{2}/g).map((v) => parseInt(v, 16));
        return { r: m[0], g: m[1], b: m[2] };
    }
    function rgbToHex({ r, g, b }) {
        return '#' + [r, g, b].map((v) => Math.max(0, Math.min(255, Math.round(v))).toString(16).padStart(2, '0')).join('').toUpperCase();
    }
    function rgbToHsl({ r, g, b }) {
        r /= 255; g /= 255; b /= 255;
        const max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h = 0, s = 0, l = (max + min) / 2;
        if (max !== min) {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            if (max === r) h = (g - b) / d + (g < b ? 6 : 0);
            else if (max === g) h = (b - r) / d + 2;
            else h = (r - g) / d + 4;
            h /= 6;
        }
        return { h: Math.round(h * 360), s: Math.round(s * 100), l: Math.round(l * 100) };
    }

    function card(label, value) {
        return `
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wide text-slate-400">${label}</span>
                <button onclick="copyToClipboard('${value}', '${label} copied')" class="rounded-md border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            </div>
            <div class="mt-1 font-mono text-sm text-white">${value}</div>`;
    }

    function setColor(hex) {
        hex = hex.toUpperCase();
        const rgb = hexToRgb(hex);
        const hsl = rgbToHsl(rgb);
        document.getElementById('colorInput').value = hex.toLowerCase();
        document.getElementById('preview').style.background = hex;
        document.getElementById('previewHex').textContent = hex;
        document.getElementById('fmt-hex').innerHTML = card('HEX', hex);
        document.getElementById('fmt-rgb').innerHTML = card('RGB', `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`);
        document.getElementById('fmt-hsl').innerHTML = card('HSL', `hsl(${hsl.h}, ${hsl.s}%, ${hsl.l}%)`);
        renderShades(rgb);
    }

    function renderShades(rgb) {
        const steps = [];
        for (let i = 0; i <= 10; i++) {
            const t = (i - 5) / 5; // -1 (dark) .. 1 (light)
            const mix = (c) => t < 0 ? c * (1 + t) : c + (255 - c) * t;
            steps.push({ r: mix(rgb.r), g: mix(rgb.g), b: mix(rgb.b) });
        }
        document.getElementById('shades').innerHTML = steps.map((s) => {
            const hex = rgbToHex(s);
            return `<button onclick="setColor('${hex}')" title="${hex}" class="aspect-square rounded-md border border-white/10 transition hover:scale-110" style="background:${hex};"></button>`;
        }).join('');
    }

    async function sampleScreen() {
        if (!window.EyeDropper) {
            return showNotification('Your browser does not support screen sampling. Try Chrome or Edge.', 'warning');
        }
        try {
            const result = await new EyeDropper().open();
            setColor(result.sRGBHex);
            showNotification('Color sampled!', 'success');
        } catch (_) { /* cancelled */ }
    }

    function randomColor() {
        setColor('#' + Math.floor(Math.random() * 0xffffff).toString(16).padStart(6, '0'));
    }

    document.addEventListener('DOMContentLoaded', () => setColor('#EC4899'));
</script>
@endpush
