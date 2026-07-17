@extends('layouts.app')

@section('title', 'Timezone Converter')
@section('description', 'Compare a moment in time across multiple timezones.')

@section('content')
    <x-tool-header title="Timezone Converter" subtitle="See one moment in time across the world's timezones."
        from="from-orange-500" to="to-red-500" icon="M21 12a9 9 0 11-18 0 9 9 0 0118 0z M3 12h18M12 3a15 15 0 010 18 15 15 0 010-18" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-[1fr_1fr_auto] sm:items-end">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Date &amp; time</label>
                <input type="datetime-local" id="dt" step="60" oninput="render()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white transition focus:border-orange-400/60 focus:outline-none [color-scheme:dark]">
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Source timezone</label>
                <select id="sourceTz" onchange="render()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-orange-400/60 focus:outline-none"></select>
            </div>
            <button onclick="setNow()" class="rounded-xl bg-gradient-to-r from-orange-500 to-red-500 px-5 py-2.5 font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Now</button>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <h3 class="text-sm font-bold uppercase tracking-wide text-slate-300">Around the world</h3>
            <div class="flex gap-2">
                <select id="addTz" class="rounded-lg border border-white/10 bg-white/5 px-2 py-1.5 text-xs text-white focus:outline-none"></select>
                <button onclick="addZone()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">+ Add</button>
            </div>
        </div>
        <div id="zones" class="mt-3 space-y-2"></div>
    </div>
@endsection

@push('scripts')
<script>
    const ZONES = ['UTC','America/Los_Angeles','America/Denver','America/Chicago','America/New_York','America/Sao_Paulo','Europe/London','Europe/Paris','Europe/Berlin','Europe/Moscow','Africa/Cairo','Africa/Johannesburg','Asia/Dubai','Asia/Kolkata','Asia/Singapore','Asia/Shanghai','Asia/Tokyo','Australia/Sydney','Pacific/Auckland'];
    let active = ['UTC','America/New_York','Europe/London','Asia/Tokyo'];
    const localTz = Intl.DateTimeFormat().resolvedOptions().timeZone;

    function fillSelect(el, selected) { el.innerHTML = ZONES.map((z) => `<option value="${z}" ${z === selected ? 'selected' : ''} class="bg-slate-900">${z.replace(/_/g, ' ')}</option>`).join(''); }

    function offsetInZone(date, tz) {
        const dtf = new Intl.DateTimeFormat('en-US', { timeZone: tz, hour12: false, year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const parts = Object.fromEntries(dtf.formatToParts(date).map((p) => [p.type, p.value]));
        const asUTC = Date.UTC(parts.year, parts.month - 1, parts.day, parts.hour, parts.minute, parts.second);
        return (asUTC - date.getTime()) / 60000;
    }

    function zonedTimeToUtc(localStr, tz) {
        // localStr is 'YYYY-MM-DDTHH:mm' interpreted in tz
        const [d, t] = localStr.split('T');
        const [Y, M, D] = d.split('-').map(Number);
        const [h, m] = t.split(':').map(Number);
        let utc = Date.UTC(Y, M - 1, D, h, m);
        const off = offsetInZone(new Date(utc), tz);
        return new Date(utc - off * 60000);
    }

    function render() {
        const dtVal = document.getElementById('dt').value;
        const sourceTz = document.getElementById('sourceTz').value;
        if (!dtVal) return;
        const instant = zonedTimeToUtc(dtVal, sourceTz);
        document.getElementById('zones').innerHTML = active.map((tz) => {
            const fmt = new Intl.DateTimeFormat('en-GB', { timeZone: tz, dateStyle: 'medium', timeStyle: 'short' });
            const off = offsetInZone(instant, tz) / 60;
            const offStr = (off >= 0 ? '+' : '') + off;
            return `<div class="flex items-center justify-between gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3">
                <div class="min-w-0"><div class="truncate font-semibold text-white">${tz.replace(/_/g, ' ')} ${tz === localTz ? '<span class="ml-1 rounded bg-orange-500/20 px-1.5 py-0.5 text-[10px] text-orange-200">local</span>' : ''}</div><div class="text-xs text-slate-500">UTC${offStr}</div></div>
                <div class="flex items-center gap-2"><span class="font-mono text-sm text-slate-200">${fmt.format(instant)}</span><button onclick="removeZone('${tz}')" class="rounded p-1 text-rose-300 transition hover:bg-rose-500/20">✕</button></div>
            </div>`;
        }).join('');
    }

    function addZone() { const tz = document.getElementById('addTz').value; if (!active.includes(tz)) active.push(tz); render(); }
    function removeZone(tz) { active = active.filter((z) => z !== tz); render(); }
    function setNow() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('dt').value = now.toISOString().slice(0, 16);
        document.getElementById('sourceTz').value = ZONES.includes(localTz) ? localTz : 'UTC';
        render();
    }

    document.addEventListener('DOMContentLoaded', () => {
        fillSelect(document.getElementById('sourceTz'), ZONES.includes(localTz) ? localTz : 'UTC');
        fillSelect(document.getElementById('addTz'), 'Europe/Paris');
        if (!active.includes(localTz) && ZONES.includes(localTz)) active.unshift(localTz);
        setNow();
    });
</script>
@endpush
