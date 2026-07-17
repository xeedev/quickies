@extends('layouts.app')

@section('title', 'Box Shadow Generator')
@section('description', 'Craft CSS box-shadows with a live preview.')

@section('content')
    <x-tool-header title="Box Shadow Generator" subtitle="Craft layered CSS box-shadows with a live preview."
        from="from-violet-500" to="to-purple-500" icon="M7 7h10v10H7V7z M11 11h6v6h-6" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_380px]">
        <div class="flex min-h-[320px] items-center justify-center rounded-3xl border border-white/10 bg-white/5 p-10 backdrop-blur-xl" id="stage" style="background:#0f172a;">
            <div id="preview" class="h-40 w-40 rounded-2xl" style="background:#8b5cf6;"></div>
        </div>

        <div class="space-y-5">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="space-y-4">
                    <div><label class="mb-1 flex justify-between text-sm font-semibold text-slate-200"><span>Offset X</span><span id="xVal" class="text-slate-400">0px</span></label><input type="range" id="x" min="-100" max="100" value="0" class="w-full" oninput="render()"></div>
                    <div><label class="mb-1 flex justify-between text-sm font-semibold text-slate-200"><span>Offset Y</span><span id="yVal" class="text-slate-400">20px</span></label><input type="range" id="y" min="-100" max="100" value="20" class="w-full" oninput="render()"></div>
                    <div><label class="mb-1 flex justify-between text-sm font-semibold text-slate-200"><span>Blur</span><span id="blurVal" class="text-slate-400">40px</span></label><input type="range" id="blur" min="0" max="200" value="40" class="w-full" oninput="render()"></div>
                    <div><label class="mb-1 flex justify-between text-sm font-semibold text-slate-200"><span>Spread</span><span id="spreadVal" class="text-slate-400">-10px</span></label><input type="range" id="spread" min="-100" max="100" value="-10" class="w-full" oninput="render()"></div>
                    <div><label class="mb-1 flex justify-between text-sm font-semibold text-slate-200"><span>Opacity</span><span id="opacityVal" class="text-slate-400">40%</span></label><input type="range" id="opacity" min="0" max="100" value="40" class="w-full" oninput="render()"></div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200">Shadow</label>
                            <input type="color" id="shadowColor" value="#8b5cf6" oninput="render()" class="h-10 w-full cursor-pointer rounded-lg border border-white/10 bg-white/5">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-semibold text-slate-200">Box</label>
                            <input type="color" id="boxColor" value="#8b5cf6" oninput="render()" class="h-10 w-full cursor-pointer rounded-lg border border-white/10 bg-white/5">
                        </div>
                    </div>
                    <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-200"><input type="checkbox" id="inset" onchange="render()" class="h-4 w-4 accent-violet-500"> Inset shadow</label>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-200">CSS</span>
                    <button onclick="copyToClipboard(document.getElementById('css').textContent, 'CSS copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <code id="css" class="block break-all rounded-xl bg-slate-950/50 p-3 font-mono text-xs text-violet-200"></code>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function hexToRgba(hex, a) { const m = hex.replace('#', '').match(/.{2}/g).map((v) => parseInt(v, 16)); return `rgba(${m[0]}, ${m[1]}, ${m[2]}, ${a})`; }

    function render() {
        const x = +document.getElementById('x').value, y = +document.getElementById('y').value, blur = +document.getElementById('blur').value, spread = +document.getElementById('spread').value, opacity = +document.getElementById('opacity').value;
        const inset = document.getElementById('inset').checked;
        ['x', 'y', 'blur', 'spread'].forEach((k) => document.getElementById(k + 'Val').textContent = document.getElementById(k).value + 'px');
        document.getElementById('opacityVal').textContent = opacity + '%';
        const color = hexToRgba(document.getElementById('shadowColor').value, (opacity / 100).toFixed(2));
        const shadow = `${inset ? 'inset ' : ''}${x}px ${y}px ${blur}px ${spread}px ${color}`;
        document.getElementById('preview').style.boxShadow = shadow;
        document.getElementById('preview').style.background = document.getElementById('boxColor').value;
        document.getElementById('css').textContent = `box-shadow: ${shadow};`;
    }
    document.addEventListener('DOMContentLoaded', render);
</script>
@endpush
