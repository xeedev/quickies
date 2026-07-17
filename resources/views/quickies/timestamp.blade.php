@extends('layouts.app')

@section('title', 'Timestamp Converter')
@section('description', 'Convert between Unix timestamps and human-readable dates.')

@section('content')
    <x-tool-header
        title="Timestamp Converter"
        subtitle="Convert between Unix timestamps and human-readable dates."
        from="from-amber-500"
        to="to-orange-500"
        icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

    <div class="space-y-6">
        {{-- Live now --}}
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Current Unix time</p>
                    <p id="liveNow" class="font-mono text-3xl font-bold text-white sm:text-4xl">0</p>
                </div>
                <button onclick="copyToClipboard(document.getElementById('liveNow').textContent, 'Timestamp copied')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Timestamp -> Date --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
                <h3 class="mb-4 text-base font-bold text-white">Timestamp → Date</h3>
                <div class="flex gap-2">
                    <input id="tsInput" type="text" inputmode="numeric" oninput="fromTimestamp()" placeholder="1700000000"
                        class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 font-mono text-white transition focus:border-amber-400/60 focus:outline-none focus:ring-2 focus:ring-amber-500/30">
                    <select id="tsUnit" onchange="fromTimestamp()" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-amber-400/60 focus:outline-none">
                        <option value="s" class="bg-slate-900">seconds</option>
                        <option value="ms" class="bg-slate-900">millis</option>
                    </select>
                </div>
                <button onclick="setNow()" class="mt-2 text-xs font-semibold text-amber-300 hover:text-amber-200">Use current time</button>
                <div id="tsResult" class="mt-4 space-y-2 text-sm"></div>
            </div>

            {{-- Date -> Timestamp --}}
            <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
                <h3 class="mb-4 text-base font-bold text-white">Date → Timestamp</h3>
                <input id="dateInput" type="datetime-local" step="1" oninput="fromDate()"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white transition focus:border-amber-400/60 focus:outline-none focus:ring-2 focus:ring-amber-500/30 [color-scheme:dark]">
                <div id="dateResult" class="mt-4 space-y-2 text-sm"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function row(label, value) {
        return `<div class="flex items-center justify-between gap-3 rounded-xl border border-white/10 bg-white/5 px-3 py-2">
            <div class="min-w-0"><div class="text-xs text-slate-500">${label}</div><div class="truncate font-mono text-slate-200">${value}</div></div>
            <button onclick="copyToClipboard('${String(value).replace(/'/g, "\\'")}', 'Copied')" class="flex-shrink-0 rounded-md border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold text-slate-300 transition hover:bg-white/10">Copy</button>
        </div>`;
    }

    function fromTimestamp() {
        const raw = document.getElementById('tsInput').value.trim();
        const unit = document.getElementById('tsUnit').value;
        const el = document.getElementById('tsResult');
        if (!raw || isNaN(raw)) { el.innerHTML = '<p class="text-slate-500">Enter a numeric timestamp.</p>'; return; }
        const ms = unit === 's' ? Number(raw) * 1000 : Number(raw);
        const d = new Date(ms);
        if (isNaN(d.getTime())) { el.innerHTML = '<p class="text-rose-300">Invalid timestamp.</p>'; return; }
        el.innerHTML = row('Local', d.toLocaleString()) + row('UTC', d.toUTCString()) + row('ISO 8601', d.toISOString()) + row('Relative', relative(ms));
    }

    function fromDate() {
        const val = document.getElementById('dateInput').value;
        const el = document.getElementById('dateResult');
        if (!val) { el.innerHTML = '<p class="text-slate-500">Pick a date and time.</p>'; return; }
        const d = new Date(val);
        const s = Math.floor(d.getTime() / 1000);
        el.innerHTML = row('Unix (seconds)', s) + row('Unix (millis)', d.getTime()) + row('ISO 8601', d.toISOString());
    }

    function relative(ms) {
        const diff = ms - Date.now();
        const abs = Math.abs(diff);
        const units = [['year', 31536e6], ['month', 2592e6], ['day', 864e5], ['hour', 36e5], ['minute', 6e4], ['second', 1e3]];
        for (const [name, size] of units) {
            if (abs >= size || name === 'second') {
                const v = Math.round(diff / size);
                return new Intl.RelativeTimeFormat('en', { numeric: 'auto' }).format(v, name);
            }
        }
    }

    function setNow() {
        document.getElementById('tsUnit').value = 's';
        document.getElementById('tsInput').value = Math.floor(Date.now() / 1000);
        fromTimestamp();
    }

    function tick() { document.getElementById('liveNow').textContent = Math.floor(Date.now() / 1000); }

    document.addEventListener('DOMContentLoaded', () => {
        tick(); setInterval(tick, 1000);
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('dateInput').value = now.toISOString().slice(0, 19);
        setNow();
        fromDate();
    });
</script>
@endpush
