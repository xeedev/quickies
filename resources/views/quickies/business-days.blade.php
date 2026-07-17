@extends('layouts.app')

@section('title', 'Business Days Calculator')
@section('description', 'Count working days between two dates, skipping weekends.')

@section('content')
    <x-tool-header title="Business Days Calculator" subtitle="Count working days between dates or add business days to a date."
        from="from-red-500" to="to-rose-500" icon="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Between two dates --}}
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <h3 class="mb-4 text-base font-bold text-white">Days between two dates</h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div><label class="mb-1 block text-xs font-semibold text-slate-300">Start</label><input type="date" id="start" oninput="between()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-white focus:border-rose-400/60 focus:outline-none [color-scheme:dark]"></div>
                <div><label class="mb-1 block text-xs font-semibold text-slate-300">End</label><input type="date" id="end" oninput="between()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-white focus:border-rose-400/60 focus:outline-none [color-scheme:dark]"></div>
            </div>
            <label class="mt-3 flex cursor-pointer items-center gap-2 text-sm text-slate-200"><input type="checkbox" id="inclusive" onchange="between()" class="h-4 w-4 accent-rose-500"> Include end date</label>
            <div id="betweenResult" class="mt-4 grid grid-cols-2 gap-3"></div>
        </div>

        {{-- Add business days --}}
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <h3 class="mb-4 text-base font-bold text-white">Add business days</h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div><label class="mb-1 block text-xs font-semibold text-slate-300">From date</label><input type="date" id="addStart" oninput="addDays()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-white focus:border-rose-400/60 focus:outline-none [color-scheme:dark]"></div>
                <div><label class="mb-1 block text-xs font-semibold text-slate-300">Business days</label><input type="number" id="addN" value="10" oninput="addDays()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-white focus:border-rose-400/60 focus:outline-none"></div>
            </div>
            <div id="addResult" class="mt-4 rounded-2xl border border-white/10 bg-white/5 p-4 text-center"></div>
        </div>
    </div>

    <div class="mt-6 rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
        <label class="mb-2 block text-sm font-semibold text-slate-200">Public holidays to exclude <span class="font-normal text-slate-400">(one ISO date per line, e.g. 2026-12-25)</span></label>
        <textarea id="holidays" rows="3" oninput="between(); addDays();" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-rose-400/60 focus:outline-none focus:ring-2 focus:ring-rose-500/30" placeholder="2026-01-01&#10;2026-12-25"></textarea>
    </div>
@endsection

@push('scripts')
<script>
    const iso = (d) => d.toISOString().slice(0, 10);
    function holidaySet() { return new Set(document.getElementById('holidays').value.split('\n').map((l) => l.trim()).filter(Boolean)); }
    function isBusiness(d, hol) { const day = d.getUTCDay(); return day !== 0 && day !== 6 && !hol.has(iso(d)); }

    function stat(label, value) { return `<div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"><div class="text-2xl font-bold text-white">${value}</div><div class="text-xs text-slate-400">${label}</div></div>`; }

    function between() {
        const s = document.getElementById('start').value, e = document.getElementById('end').value;
        const el = document.getElementById('betweenResult');
        if (!s || !e) { el.innerHTML = '<p class="col-span-2 text-sm text-slate-500">Pick both dates.</p>'; return; }
        let start = new Date(s + 'T00:00:00Z'), end = new Date(e + 'T00:00:00Z');
        if (end < start) [start, end] = [end, start];
        const hol = holidaySet();
        const inclusive = document.getElementById('inclusive').checked;
        let total = 0, business = 0, weekend = 0, holidays = 0;
        const cur = new Date(start);
        const last = new Date(end); if (!inclusive) last.setUTCDate(last.getUTCDate() - 1);
        while (cur <= last) {
            total++;
            const day = cur.getUTCDay();
            if (day === 0 || day === 6) weekend++;
            else if (hol.has(iso(cur))) holidays++;
            else business++;
            cur.setUTCDate(cur.getUTCDate() + 1);
        }
        el.innerHTML = stat('Business', business) + stat('Total', total) + stat('Weekends', weekend) + stat('Holidays', holidays);
    }

    function addDays() {
        const s = document.getElementById('addStart').value;
        const n = parseInt(document.getElementById('addN').value) || 0;
        const el = document.getElementById('addResult');
        if (!s) { el.innerHTML = '<p class="text-sm text-slate-500">Pick a start date.</p>'; return; }
        const hol = holidaySet();
        const d = new Date(s + 'T00:00:00Z');
        let remaining = Math.abs(n), dir = n < 0 ? -1 : 1;
        while (remaining > 0) { d.setUTCDate(d.getUTCDate() + dir); if (isBusiness(d, hol)) remaining--; }
        const fmt = new Intl.DateTimeFormat('en-GB', { timeZone: 'UTC', weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        el.innerHTML = `<div class="text-xs text-slate-400">${n} business day${Math.abs(n) === 1 ? '' : 's'} ${n < 0 ? 'before' : 'after'}</div><div class="mt-1 text-xl font-bold text-white">${fmt.format(d)}</div>`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        const today = new Date(); const t = iso(today);
        const plus = new Date(today); plus.setDate(plus.getDate() + 30);
        document.getElementById('start').value = t;
        document.getElementById('end').value = iso(plus);
        document.getElementById('addStart').value = t;
        between(); addDays();
    });
</script>
@endpush
