@extends('layouts.app')

@section('title', 'Subscription Revenue & LTV Calculator')
@section('description', 'Model subscription revenue, churn, LTV and payback for DCB services.')

@section('content')
    <x-tool-header title="Revenue & LTV Calculator" subtitle="Model subscription revenue, churn, lifetime value and payback."
        from="from-emerald-500" to="to-teal-500" icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[380px_1fr]">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <h3 class="mb-4 text-sm font-bold uppercase tracking-wide text-slate-300">Inputs</h3>
            <div class="space-y-3">
                @foreach (['subs' => ['New subscribers', '1000'], 'price' => ['Price per period', '4.99'], 'churn' => ['Churn rate % / period', '25'], 'cpa' => ['Acquisition cost (CPA)', '3.00'], 'margin' => ['Gross margin %', '70'], 'periods' => ['Horizon (periods)', '12']] as $id => $meta)
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-slate-300">{{ $meta[0] }}</label>
                        <input type="number" id="{{ $id }}" value="{{ $meta[1] }}" step="any" oninput="calc()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white transition focus:border-teal-400/60 focus:outline-none">
                    </div>
                @endforeach
                <label class="flex items-center gap-2 pt-1 text-xs text-slate-300">Currency
                    <input id="cur" value="$" maxlength="3" oninput="calc()" class="w-16 rounded-lg border border-white/10 bg-white/5 px-2 py-1 text-center text-white focus:outline-none">
                </label>
            </div>
        </div>

        <div class="space-y-4">
            <div id="results" class="grid grid-cols-2 gap-4 sm:grid-cols-3"></div>
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
                <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300">Revenue by period</h3>
                <div id="table" class="overflow-x-auto"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const num = (id) => parseFloat(document.getElementById(id).value) || 0;
    function card(label, value, hint, good) {
        const color = good === true ? 'text-emerald-400' : good === false ? 'text-rose-400' : 'text-white';
        return `<div class="rounded-2xl border border-white/10 bg-white/5 p-4"><div class="text-xs font-semibold uppercase tracking-wide text-slate-400">${label}</div><div class="mt-1 text-2xl font-bold ${color}">${value}</div><div class="mt-0.5 text-[11px] text-slate-500">${hint}</div></div>`;
    }
    function calc() {
        const cur = document.getElementById('cur').value || '';
        const subs = num('subs'), price = num('price'), churn = num('churn') / 100, cpa = num('cpa'), margin = num('margin') / 100, periods = Math.max(1, Math.min(120, num('periods')));
        const money = (v) => cur + (isFinite(v) ? v.toLocaleString(undefined, { maximumFractionDigits: 2 }) : '0');
        const avgLifetime = churn > 0 ? 1 / churn : periods;
        const ltv = price * margin * avgLifetime;
        const ltvGross = price * avgLifetime;
        const cacRatio = cpa ? ltv / cpa : 0;
        const paybackPeriods = price * margin > 0 ? cpa / (price * margin) : 0;

        let active = subs, totalRev = 0, totalProfit = 0, rows = '';
        for (let p = 1; p <= periods; p++) {
            const rev = active * price;
            const profit = rev * margin - (p === 1 ? subs * cpa : 0);
            totalRev += rev; totalProfit += profit;
            if (p <= 12 || p === periods) rows += `<tr class="border-t border-white/10"><td class="px-3 py-1.5 text-slate-400">${p}</td><td class="px-3 py-1.5 font-mono text-slate-200">${Math.round(active).toLocaleString()}</td><td class="px-3 py-1.5 font-mono text-slate-200">${money(rev)}</td><td class="px-3 py-1.5 font-mono ${profit >= 0 ? 'text-emerald-300' : 'text-rose-300'}">${money(profit)}</td></tr>`;
            active = active * (1 - churn);
        }

        document.getElementById('results').innerHTML =
            card('LTV (net)', money(ltv), 'Per subscriber', true) +
            card('LTV : CAC', cacRatio.toFixed(2) + '×', 'Aim for 3×+', cacRatio >= 3) +
            card('Avg lifetime', avgLifetime.toFixed(1), 'periods') +
            card('Payback', paybackPeriods.toFixed(1), 'periods to recover CPA', paybackPeriods <= avgLifetime) +
            card('Total revenue', money(totalRev), `over ${periods} periods`) +
            card('Total profit', money(totalProfit), 'after CPA & COGS', totalProfit >= 0);

        document.getElementById('table').innerHTML = `<table class="w-full text-sm"><thead><tr class="text-left text-xs uppercase tracking-wide text-slate-400"><th class="px-3 py-2">Period</th><th class="px-3 py-2">Active</th><th class="px-3 py-2">Revenue</th><th class="px-3 py-2">Profit</th></tr></thead><tbody>${rows}</tbody></table>`;
    }
    document.addEventListener('DOMContentLoaded', calc);
</script>
@endpush
