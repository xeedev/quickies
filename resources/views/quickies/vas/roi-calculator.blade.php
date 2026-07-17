@extends('layouts.app')

@section('title', 'ROI / EPC / CPA Calculator')
@section('description', 'Calculate ROI, EPC, CPA, ARPU and margins for VAS/DCB campaigns.')

@section('content')
    <x-tool-header title="ROI / EPC / CPA Calculator" subtitle="Key performance and profitability metrics for your campaigns."
        from="from-emerald-500" to="to-green-500" icon="M3 3v18h18M7 14l3-3 4 4 5-6" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[380px_1fr]">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-300">Inputs</h3>
            <div class="space-y-3">
                @foreach (['spend' => ['Ad spend / cost', '1000'], 'revenue' => ['Revenue', '2500'], 'impressions' => ['Impressions', '100000'], 'clicks' => ['Clicks', '4000'], 'conversions' => ['Conversions', '250'], 'users' => ['Active users (for ARPU)', '250']] as $id => $meta)
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-300">{{ $meta[0] }}</label>
                        <input type="number" id="{{ $id }}" value="{{ $meta[1] }}" step="any" oninput="calc()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white transition focus:border-emerald-400/60 focus:outline-none">
                    </div>
                @endforeach
                <label class="flex items-center gap-2 pt-1 text-xs text-slate-300">Currency
                    <input id="cur" value="$" maxlength="3" oninput="calc()" class="w-16 rounded-lg border border-white/10 bg-white/5 px-2 py-1 text-center text-white focus:outline-none">
                </label>
            </div>
        </div>

        <div id="results" class="grid grid-cols-2 gap-4 self-start sm:grid-cols-3"></div>
    </div>
@endsection

@push('scripts')
<script>
    const num = (id) => parseFloat(document.getElementById(id).value) || 0;
    function card(label, value, hint, good) {
        const color = good === true ? 'text-emerald-400' : good === false ? 'text-rose-400' : 'text-white';
        return `<div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">${label}</div>
            <div class="mt-1 text-2xl font-bold ${color}">${value}</div>
            <div class="mt-0.5 text-[11px] text-slate-500">${hint}</div></div>`;
    }
    function calc() {
        const cur = document.getElementById('cur').value || '';
        const spend = num('spend'), revenue = num('revenue'), impr = num('impressions'), clicks = num('clicks'), conv = num('conversions'), users = num('users');
        const money = (v) => cur + (isFinite(v) ? v.toFixed(2) : '0.00');
        const pct = (v) => (isFinite(v) ? v.toFixed(2) : '0.00') + '%';
        const profit = revenue - spend;
        const roi = spend ? (profit / spend) * 100 : 0;
        const roas = spend ? revenue / spend : 0;
        const cpa = conv ? spend / conv : 0;
        const cpc = clicks ? spend / clicks : 0;
        const epc = clicks ? revenue / clicks : 0;
        const cpm = impr ? (spend / impr) * 1000 : 0;
        const ctr = impr ? (clicks / impr) * 100 : 0;
        const cvr = clicks ? (conv / clicks) * 100 : 0;
        const arpu = users ? revenue / users : 0;
        const margin = revenue ? (profit / revenue) * 100 : 0;
        document.getElementById('results').innerHTML =
            card('ROI', pct(roi), 'Return on investment', roi >= 0) +
            card('ROAS', roas.toFixed(2) + '×', 'Return on ad spend', roas >= 1) +
            card('Profit', money(profit), 'Revenue − spend', profit >= 0) +
            card('CPA', money(cpa), 'Cost per acquisition') +
            card('EPC', money(epc), 'Earnings per click') +
            card('CPC', money(cpc), 'Cost per click') +
            card('CPM', money(cpm), 'Cost per 1000 impr.') +
            card('CTR', pct(ctr), 'Click-through rate') +
            card('CVR', pct(cvr), 'Conversion rate') +
            card('ARPU', money(arpu), 'Avg revenue per user') +
            card('Margin', pct(margin), 'Profit margin', margin >= 0);
    }
    document.addEventListener('DOMContentLoaded', calc);
</script>
@endpush
