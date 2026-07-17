@extends('layouts.app')

@section('title', 'Gradient Generator')
@section('description', 'Design CSS gradients and copy the generated code.')

@section('content')
    <x-tool-header title="CSS Gradient Generator" subtitle="Design beautiful CSS gradients with a live preview."
        from="from-pink-500" to="to-violet-500" icon="M4 4h16v16H4V4z M4 12h16" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_380px]">
        <div id="preview" class="min-h-[280px] rounded-3xl border border-white/10 shadow-2xl lg:min-h-full"></div>

        <div class="space-y-5">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="mb-4 flex gap-2">
                    <button id="typeLinear" onclick="setType('linear')" class="flex-1 rounded-xl px-4 py-2 text-sm font-semibold transition">Linear</button>
                    <button id="typeRadial" onclick="setType('radial')" class="flex-1 rounded-xl px-4 py-2 text-sm font-semibold transition">Radial</button>
                    <button id="typeConic" onclick="setType('conic')" class="flex-1 rounded-xl px-4 py-2 text-sm font-semibold transition">Conic</button>
                </div>
                <div id="angleWrap">
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Angle <span id="angleValue" class="text-slate-400">90</span>°</label>
                    <input type="range" id="angle" min="0" max="360" value="90" class="w-full" oninput="document.getElementById('angleValue').textContent = this.value; render();">
                </div>
                <div class="mt-4">
                    <div class="mb-2 flex items-center justify-between">
                        <label class="text-sm font-semibold text-slate-200">Color stops</label>
                        <button onclick="addStop()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">+ Add</button>
                    </div>
                    <div id="stops" class="space-y-2"></div>
                </div>
                <button onclick="randomize()" class="mt-4 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10">🎲 Randomize</button>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-semibold text-slate-200">CSS</span>
                    <button onclick="copyToClipboard(document.getElementById('css').textContent, 'CSS copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <code id="css" class="block break-all rounded-xl bg-slate-950/50 p-3 font-mono text-xs text-pink-200"></code>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let type = 'linear';
    let stops = [{ color: '#ec4899', pos: 0 }, { color: '#8b5cf6', pos: 100 }];

    function setType(t) {
        type = t;
        ['Linear', 'Radial', 'Conic'].forEach((n) => {
            const active = n.toLowerCase() === t;
            document.getElementById('type' + n).className = `flex-1 rounded-xl px-4 py-2 text-sm font-semibold transition ${active ? 'bg-gradient-to-r from-pink-500 to-violet-500 text-white shadow' : 'text-slate-400'}`;
        });
        document.getElementById('angleWrap').style.display = t === 'radial' ? 'none' : 'block';
        render();
    }

    function renderStops() {
        document.getElementById('stops').innerHTML = stops.map((s, i) => `
            <div class="flex items-center gap-2">
                <input type="color" value="${s.color}" oninput="updateStop(${i}, 'color', this.value)" class="h-9 w-11 flex-shrink-0 cursor-pointer rounded-lg border border-white/10 bg-white/5">
                <input type="range" min="0" max="100" value="${s.pos}" oninput="updateStop(${i}, 'pos', this.value)" class="flex-1">
                <span class="w-10 text-right font-mono text-xs text-slate-400">${s.pos}%</span>
                ${stops.length > 2 ? `<button onclick="removeStop(${i})" class="flex-shrink-0 rounded p-1 text-rose-300 transition hover:bg-rose-500/20">✕</button>` : '<span class="w-6"></span>'}
            </div>`).join('');
    }
    function updateStop(i, key, val) { stops[i][key] = key === 'pos' ? parseInt(val) : val; render(); }
    function addStop() { stops.push({ color: '#22d3ee', pos: 50 }); render(); }
    function removeStop(i) { stops.splice(i, 1); render(); }

    function gradientCss() {
        const list = [...stops].sort((a, b) => a.pos - b.pos).map((s) => `${s.color} ${s.pos}%`).join(', ');
        if (type === 'linear') return `linear-gradient(${document.getElementById('angle').value}deg, ${list})`;
        if (type === 'radial') return `radial-gradient(circle, ${list})`;
        return `conic-gradient(from ${document.getElementById('angle').value}deg, ${list})`;
    }

    function render() {
        renderStops();
        const g = gradientCss();
        document.getElementById('preview').style.background = g;
        document.getElementById('css').textContent = `background: ${g};`;
    }

    function randomize() {
        const n = Math.floor(Math.random() * 3) + 2;
        stops = Array.from({ length: n }, (_, i) => ({ color: '#' + Math.floor(Math.random() * 0xffffff).toString(16).padStart(6, '0'), pos: Math.round((i / (n - 1)) * 100) }));
        document.getElementById('angle').value = Math.floor(Math.random() * 360);
        document.getElementById('angleValue').textContent = document.getElementById('angle').value;
        render();
    }

    document.addEventListener('DOMContentLoaded', () => setType('linear'));
</script>
@endpush
