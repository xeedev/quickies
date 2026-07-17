@extends('layouts.app')

@section('title', 'Color Palette')
@section('description', 'Preview color codes and compare how they pair together.')

@section('content')
    <x-tool-header
        title="Color Palette"
        subtitle="Preview colors and compare how they pair together."
        from="from-fuchsia-500"
        to="to-pink-500"
        icon="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        {{-- Input --}}
        <label for="colorInput" class="mb-2 block text-sm font-semibold text-slate-200">
            Enter color codes <span class="font-normal text-slate-400">(one per line, comma or space separated)</span>
        </label>
        <textarea id="colorInput" rows="4"
            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-fuchsia-400/60 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30"
            placeholder="#FF6B9D, #C44569, rgb(74,144,226)…">#FF6B9D
#C44569
#FFA07A
#4A90E2
#50C878</textarea>

        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
            <button onclick="generatePalette()"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-fuchsia-500 to-pink-500 px-6 py-3 font-semibold text-white shadow-lg shadow-pink-500/25 transition hover:scale-[1.02] hover:shadow-pink-500/40 active:scale-95">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Generate palette
            </button>
            <button onclick="addRandomColor()"
                class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-slate-200 transition hover:bg-white/10">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add random color
            </button>
        </div>

        {{-- Color cards --}}
        <div id="colorGrid" class="mt-8 grid grid-cols-1 gap-4"></div>

        {{-- Matrix --}}
        <div id="matrixView" class="mt-10 hidden">
            <h2 class="mb-4 flex items-center gap-2 text-xl font-bold text-white">
                <svg class="h-6 w-6 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Combination matrix
            </h2>
            <p class="mb-4 text-sm text-slate-400">Each cell shows the row color as text over the column color as background.</p>
            <div id="matrixGrid" class="-mx-2 overflow-x-auto px-2 pb-2"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', generatePalette);

    function parseColors() {
        return document.getElementById('colorInput').value
            .split(/[\n,]+/)
            .map((c) => c.trim())
            .filter(Boolean);
    }

    function normalizeColor(input) {
        const el = document.createElement('div');
        el.style.color = '';
        el.style.color = input;
        if (el.style.color === '') return null; // invalid
        document.body.appendChild(el);
        const rgb = getComputedStyle(el).color;
        el.remove();
        const m = rgb.match(/\d+/g);
        if (!m) return null;
        return { r: +m[0], g: +m[1], b: +m[2], css: input };
    }

    function toHex({ r, g, b }) {
        return '#' + [r, g, b].map((v) => v.toString(16).padStart(2, '0')).join('').toUpperCase();
    }

    function getContrastColor({ r, g, b }) {
        const lum = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        return lum > 0.55 ? '#0f172a' : '#ffffff';
    }

    function generatePalette() {
        const grid = document.getElementById('colorGrid');
        const matrixView = document.getElementById('matrixView');
        const raw = parseColors();

        const colors = raw.map((c) => ({ input: c, rgb: normalizeColor(c) })).filter((c) => c.rgb);

        if (colors.length === 0) {
            grid.innerHTML = '<p class="rounded-2xl border border-white/10 bg-white/5 px-4 py-8 text-center text-slate-400">Enter at least one valid color code above.</p>';
            matrixView.classList.add('hidden');
            return;
        }

        grid.innerHTML = colors.map((color) => {
            const hex = toHex(color.rgb);
            const textOn = getContrastColor(color.rgb);
            const swatches = colors.map((bg) => {
                if (bg.input === color.input) return '';
                return `
                    <div class="flex items-center gap-2 rounded-lg border border-white/10 px-3 py-1.5" style="background:${bg.input};">
                        <span class="text-sm font-bold" style="color:${color.input};">Aa</span>
                        <span class="font-mono text-[11px] opacity-80" style="color:${color.input};">${toHex(bg.rgb)}</span>
                    </div>`;
            }).join('');
            return `
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                    <div class="flex flex-col sm:flex-row">
                        <button type="button" onclick="copyToClipboard('${hex}', 'Copied ${hex}')" class="relative h-24 w-full flex-shrink-0 sm:h-auto sm:w-40" style="background:${color.input};" title="Copy ${hex}">
                            <span class="absolute bottom-2 left-3 rounded-md bg-black/30 px-2 py-0.5 font-mono text-xs font-bold" style="color:${textOn};">${hex}</span>
                        </button>
                        <div class="flex-1 p-4 sm:p-5">
                            <div class="mb-3 flex items-center justify-between gap-3">
                                <span class="font-mono text-base font-bold text-white">${hex}</span>
                                <button onclick="copyToClipboard('${hex}', 'Copied ${hex}')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                            </div>
                            <div class="flex flex-wrap gap-2">${swatches || '<span class="text-xs text-slate-500">Add more colors to compare.</span>'}</div>
                        </div>
                    </div>
                </div>`;
        }).join('');

        if (colors.length > 1) {
            matrixView.classList.remove('hidden');
            generateMatrix(colors);
        } else {
            matrixView.classList.add('hidden');
        }
    }

    function generateMatrix(colors) {
        const cell = (c) => `<div class="mx-auto h-12 w-12 rounded-lg border border-white/10 shadow" style="background:${c.input};" title="${toHex(c.rgb)}"></div>`;
        let html = '<table class="w-full border-separate border-spacing-1"><thead><tr><th class="p-1"></th>';
        colors.forEach((c) => { html += `<th class="p-1">${cell(c)}</th>`; });
        html += '</tr></thead><tbody>';
        colors.forEach((fg) => {
            html += `<tr><td class="p-1">${cell(fg)}</td>`;
            colors.forEach((bg) => {
                if (fg.input === bg.input) {
                    html += '<td class="p-1"><div class="flex h-14 items-center justify-center rounded-lg bg-white/5 text-slate-500">—</div></td>';
                } else {
                    html += `<td class="p-1"><div class="flex h-14 items-center justify-center rounded-lg" style="background:${bg.input};color:${fg.input};"><span class="text-lg font-bold">Aa</span></div></td>`;
                }
            });
            html += '</tr>';
        });
        html += '</tbody></table>';
        document.getElementById('matrixGrid').innerHTML = html;
    }

    function addRandomColor() {
        const hex = '#' + Math.floor(Math.random() * 0xffffff).toString(16).padStart(6, '0').toUpperCase();
        const ta = document.getElementById('colorInput');
        ta.value = (ta.value.trim() + '\n' + hex).trim();
        generatePalette();
    }
</script>
@endpush
