@extends('layouts.app')

@section('title', 'Contrast Checker')
@section('description', 'Check WCAG colour contrast ratios for accessibility.')

@section('content')
    <x-tool-header title="Contrast Checker" subtitle="Check colour contrast against WCAG AA and AAA standards."
        from="from-purple-500" to="to-fuchsia-500" icon="M12 3v18m0-18a9 9 0 000 18 9 9 0 000-18z" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[360px_1fr]">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <div class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Foreground (text)</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="fg" value="#1e293b" oninput="syncFromPicker('fg')" class="h-11 w-14 cursor-pointer rounded-xl border border-white/10 bg-white/5">
                        <input id="fgHex" value="#1E293B" oninput="update()" class="flex-1 rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 font-mono text-sm text-white focus:border-fuchsia-400/60 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Background</label>
                    <div class="flex items-center gap-3">
                        <input type="color" id="bg" value="#f8fafc" oninput="syncFromPicker('bg')" class="h-11 w-14 cursor-pointer rounded-xl border border-white/10 bg-white/5">
                        <input id="bgHex" value="#F8FAFC" oninput="update()" class="flex-1 rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 font-mono text-sm text-white focus:border-fuchsia-400/60 focus:outline-none">
                    </div>
                </div>
                <button onclick="swap()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10">⇅ Swap colours</button>
            </div>
        </div>

        <div class="space-y-5">
            <div id="preview" class="flex min-h-[160px] flex-col items-center justify-center gap-2 rounded-3xl border border-white/10 p-8 text-center">
                <span class="text-2xl font-bold" id="previewLarge">Large heading text</span>
                <span class="text-sm" id="previewSmall">The quick brown fox jumps over the lazy dog.</span>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-300">Contrast ratio</span>
                    <span id="ratio" class="font-mono text-3xl font-bold text-white">—</span>
                </div>
                <div id="grades" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function hexToRgb(hex) { const m = hex.replace('#', '').match(/.{2}/g); if (!m || m.length < 3) return null; return m.slice(0,3).map((v) => parseInt(v, 16)); }
    function lum([r, g, b]) { const a = [r, g, b].map((v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); }); return 0.2126 * a[0] + 0.7152 * a[1] + 0.0722 * a[2]; }

    function syncFromPicker(which) {
        document.getElementById(which + 'Hex').value = document.getElementById(which).value.toUpperCase();
        update();
    }
    function swap() {
        const fg = document.getElementById('fgHex').value, bg = document.getElementById('bgHex').value;
        document.getElementById('fgHex').value = bg; document.getElementById('bgHex').value = fg;
        update();
    }

    function badge(ok, label) {
        return `<div class="rounded-xl border p-3 text-center ${ok ? 'border-emerald-400/30 bg-emerald-500/10' : 'border-rose-400/30 bg-rose-500/10'}">
            <div class="text-lg font-bold ${ok ? 'text-emerald-300' : 'text-rose-300'}">${ok ? 'Pass' : 'Fail'}</div>
            <div class="text-xs text-slate-400">${label}</div></div>`;
    }

    function update() {
        const fgHex = document.getElementById('fgHex').value.trim();
        const bgHex = document.getElementById('bgHex').value.trim();
        const fg = hexToRgb(fgHex), bg = hexToRgb(bgHex);
        if (!fg || !bg) return;
        document.getElementById('fg').value = '#' + fg.map((v) => v.toString(16).padStart(2, '0')).join('');
        document.getElementById('bg').value = '#' + bg.map((v) => v.toString(16).padStart(2, '0')).join('');

        const ratio = (Math.max(lum(fg), lum(bg)) + 0.05) / (Math.min(lum(fg), lum(bg)) + 0.05);
        document.getElementById('ratio').textContent = ratio.toFixed(2) + ':1';

        const preview = document.getElementById('preview');
        preview.style.background = bgHex;
        document.getElementById('previewLarge').style.color = fgHex;
        document.getElementById('previewSmall').style.color = fgHex;

        document.getElementById('grades').innerHTML =
            badge(ratio >= 4.5, 'AA · normal') +
            badge(ratio >= 3, 'AA · large') +
            badge(ratio >= 7, 'AAA · normal') +
            badge(ratio >= 4.5, 'AAA · large');
    }
    document.addEventListener('DOMContentLoaded', update);
</script>
@endpush
