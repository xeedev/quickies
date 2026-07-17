@extends('layouts.app')

@section('title', 'A/B Test Significance Calculator')
@section('description', 'Test conversion-rate differences for statistical significance.')

@section('content')
    <x-tool-header title="A/B Test Significance" subtitle="Check whether the difference between two variants is statistically significant."
        from="from-fuchsia-500" to="to-pink-500" icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <h3 class="mb-3 font-bold text-slate-200">Control (A)</h3>
                <label class="mb-1 block text-xs font-semibold text-slate-300">Visitors</label>
                <input type="number" id="aVisitors" value="5000" oninput="calc()" class="mb-3 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-fuchsia-400/60 focus:outline-none">
                <label class="mb-1 block text-xs font-semibold text-slate-300">Conversions</label>
                <input type="number" id="aConv" value="500" oninput="calc()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-fuchsia-400/60 focus:outline-none">
                <div id="aRate" class="mt-3 text-center text-2xl font-bold text-white"></div>
            </div>
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                <h3 class="mb-3 font-bold text-slate-200">Variant (B)</h3>
                <label class="mb-1 block text-xs font-semibold text-slate-300">Visitors</label>
                <input type="number" id="bVisitors" value="5000" oninput="calc()" class="mb-3 w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-fuchsia-400/60 focus:outline-none">
                <label class="mb-1 block text-xs font-semibold text-slate-300">Conversions</label>
                <input type="number" id="bConv" value="580" oninput="calc()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-fuchsia-400/60 focus:outline-none">
                <div id="bRate" class="mt-3 text-center text-2xl font-bold text-white"></div>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <label class="text-sm text-slate-300">Confidence</label>
            <select id="conf" onchange="calc()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-white focus:outline-none">
                <option value="0.90" class="bg-slate-900">90%</option>
                <option value="0.95" class="bg-slate-900" selected>95%</option>
                <option value="0.99" class="bg-slate-900">99%</option>
            </select>
        </div>

        <div id="verdict" class="mt-6 rounded-2xl border p-6 text-center"></div>
        <div id="details" class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4"></div>
    </div>
@endsection

@push('scripts')
<script>
    const num = (id) => parseFloat(document.getElementById(id).value) || 0;
    // Normal CDF via erf approximation
    function normCdf(z) {
        const t = 1 / (1 + 0.2316419 * Math.abs(z));
        const d = 0.3989423 * Math.exp(-z * z / 2);
        let p = d * t * (0.3193815 + t * (-0.3565638 + t * (1.781478 + t * (-1.821256 + t * 1.330274))));
        return z > 0 ? 1 - p : p;
    }
    function card(label, value) { return `<div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"><div class="text-lg font-bold text-white">${value}</div><div class="text-xs text-slate-400">${label}</div></div>`; }

    function calc() {
        const aV = num('aVisitors'), aC = num('aConv'), bV = num('bVisitors'), bC = num('bConv');
        if (!aV || !bV) return;
        const pA = aC / aV, pB = bC / bV;
        document.getElementById('aRate').textContent = (pA * 100).toFixed(2) + '%';
        document.getElementById('bRate').textContent = (pB * 100).toFixed(2) + '%';
        const uplift = pA ? ((pB - pA) / pA) * 100 : 0;

        const se = Math.sqrt(pA * (1 - pA) / aV + pB * (1 - pB) / bV);
        const z = se ? (pB - pA) / se : 0;
        const pValue = 2 * (1 - normCdf(Math.abs(z)));
        const confidence = (1 - pValue) * 100;
        const target = parseFloat(document.getElementById('conf').value);
        const significant = pValue < (1 - target);

        const verdict = document.getElementById('verdict');
        const winner = pB > pA ? 'B' : 'A';
        if (significant) {
            verdict.className = 'mt-6 rounded-2xl border border-emerald-400/30 bg-emerald-500/10 p-6 text-center';
            verdict.innerHTML = `<div class="text-2xl font-bold text-emerald-300">Significant result ✓</div><p class="mt-2 text-slate-300">Variant <b>${winner}</b> ${uplift >= 0 ? 'improves' : 'changes'} conversion by <b>${uplift.toFixed(2)}%</b> with ${confidence.toFixed(1)}% confidence.</p>`;
        } else {
            verdict.className = 'mt-6 rounded-2xl border border-amber-400/30 bg-amber-500/10 p-6 text-center';
            verdict.innerHTML = `<div class="text-2xl font-bold text-amber-300">Not yet significant</div><p class="mt-2 text-slate-300">Only ${confidence.toFixed(1)}% confidence — collect more data before deciding.</p>`;
        }
        document.getElementById('details').innerHTML =
            card('Uplift', (uplift >= 0 ? '+' : '') + uplift.toFixed(2) + '%') +
            card('Z-score', z.toFixed(3)) +
            card('P-value', pValue.toFixed(4)) +
            card('Confidence', confidence.toFixed(1) + '%');
    }
    document.addEventListener('DOMContentLoaded', calc);
</script>
@endpush
