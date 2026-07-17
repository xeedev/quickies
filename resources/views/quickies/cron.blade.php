@extends('layouts.app')

@section('title', 'Cron Builder')
@section('description', 'Build and explain cron expressions in plain English.')

@section('content')
    <x-tool-header title="Cron Builder" subtitle="Build cron expressions and read them in plain English."
        from="from-purple-500" to="to-fuchsia-500" icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="flex flex-col items-stretch gap-3 sm:flex-row sm:items-center">
            <input id="expr" oninput="describe()" spellcheck="false" class="flex-1 rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 text-center font-mono text-xl tracking-widest text-fuchsia-200 transition focus:border-fuchsia-400/60 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30" value="*/5 * * * *">
            <button onclick="copyToClipboard(document.getElementById('expr').value, 'Cron copied')" class="rounded-2xl border border-white/10 bg-white/5 px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
        </div>
        <div id="human" class="mt-4 rounded-2xl border border-fuchsia-400/20 bg-fuchsia-500/5 px-4 py-3 text-center text-lg font-semibold text-fuchsia-100"></div>

        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
            @foreach (['Minute' => '0-59', 'Hour' => '0-23', 'Day (month)' => '1-31', 'Month' => '1-12', 'Day (week)' => '0-6'] as $label => $range)
                <div>
                    <label class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-400">{{ $label }}</label>
                    <input data-field="{{ $loop->index }}" oninput="syncFromFields()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-center font-mono text-sm text-white focus:border-fuchsia-400/60 focus:outline-none" value="*">
                    <p class="mt-1 text-center text-[11px] text-slate-500">{{ $range }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <h3 class="mb-2 text-sm font-bold uppercase tracking-wide text-slate-300">Presets</h3>
            <div class="flex flex-wrap gap-2">
                @foreach (['Every minute' => '* * * * *', 'Every 5 min' => '*/5 * * * *', 'Hourly' => '0 * * * *', 'Daily midnight' => '0 0 * * *', 'Weekdays 9am' => '0 9 * * 1-5', 'Weekly Sun' => '0 0 * * 0', 'Monthly 1st' => '0 0 1 * *', 'Yearly' => '0 0 1 1 *'] as $name => $val)
                    <button onclick="setExpr('{{ $val }}')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">{{ $name }}</button>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <h3 class="mb-2 text-sm font-bold uppercase tracking-wide text-slate-300">Next runs</h3>
            <div id="nextRuns" class="grid grid-cols-1 gap-2 sm:grid-cols-2"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const DAYS = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    function setExpr(v) { document.getElementById('expr').value = v; describe(); }

    function parseField(f, min, max) {
        const out = new Set();
        f.split(',').forEach((part) => {
            let step = 1, range = part;
            if (part.includes('/')) { [range, step] = part.split('/'); step = parseInt(step); }
            let lo = min, hi = max;
            if (range === '*') { /* all */ }
            else if (range.includes('-')) { [lo, hi] = range.split('-').map(Number); }
            else { lo = hi = parseInt(range); }
            for (let i = lo; i <= hi; i += step) if (i >= min && i <= max) out.add(i);
        });
        return out;
    }

    function fieldText(f, type) {
        if (f === '*') return null;
        if (/^\*\/(\d+)$/.test(f)) return `every ${RegExp.$1} ${type}`;
        if (type === 'month') return f.split(',').map((p) => MONTHS[+p - 1] || p).join(', ');
        if (type === 'day-of-week') return f.split(',').map((p) => DAYS[+p % 7] || p).join(', ');
        return f;
    }

    function describe() {
        const expr = document.getElementById('expr').value.trim();
        const parts = expr.split(/\s+/);
        const human = document.getElementById('human');
        if (parts.length !== 5) { human.textContent = 'A cron expression needs exactly 5 fields.'; document.getElementById('nextRuns').innerHTML = ''; return; }
        const [mi, ho, dom, mo, dow] = parts;

        // Sync field inputs
        document.querySelectorAll('[data-field]').forEach((el, i) => el.value = parts[i]);

        let text = 'At ';
        if (mi === '*' && ho === '*') text = 'Every minute';
        else {
            const time = (mi.match(/^\d+$/) && ho.match(/^\d+$/)) ? `${ho.padStart(2, '0')}:${mi.padStart(2, '0')}` : null;
            if (time) text = `At ${time}`;
            else {
                const m = fieldText(mi, 'minutes'); const h = fieldText(ho, 'hours');
                text = 'Run ' + [m && `minute ${m}`, h && `hour ${h}`].filter(Boolean).join(', ') || 'Every minute';
            }
        }
        const parts2 = [];
        const domT = fieldText(dom, 'day'); if (domT) parts2.push(`on day ${domT} of the month`);
        const moT = fieldText(mo, 'month'); if (moT) parts2.push(`in ${moT}`);
        const dowT = fieldText(dow, 'day-of-week'); if (dowT) parts2.push(`on ${dowT}`);
        human.textContent = (text + ' ' + parts2.join(', ')).trim().replace(/\s+/g, ' ') + '.';

        computeNext(parts);
    }

    function syncFromFields() {
        const vals = [...document.querySelectorAll('[data-field]')].map((el) => el.value.trim() || '*');
        document.getElementById('expr').value = vals.join(' ');
        describe();
    }

    function computeNext(parts) {
        try {
            const [mi, ho, dom, mo, dow] = parts;
            const mins = parseField(mi, 0, 59), hours = parseField(ho, 0, 23), doms = parseField(dom, 1, 31), mons = parseField(mo, 1, 12), dows = parseField(dow, 0, 6);
            const results = [];
            let d = new Date();
            d.setSeconds(0, 0);
            d.setMinutes(d.getMinutes() + 1);
            let guard = 0;
            while (results.length < 4 && guard++ < 500000) {
                const domOk = dom === '*' || doms.has(d.getDate());
                const dowOk = dow === '*' || dows.has(d.getDay());
                const dayOk = (dom === '*' || dow === '*') ? (domOk && dowOk) : (domOk || dowOk);
                if (mins.has(d.getMinutes()) && hours.has(d.getHours()) && mons.has(d.getMonth() + 1) && dayOk) {
                    results.push(new Date(d));
                }
                d.setMinutes(d.getMinutes() + 1);
            }
            document.getElementById('nextRuns').innerHTML = results.map((r) => `<div class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 font-mono text-sm text-slate-200">${r.toLocaleString()}</div>`).join('') || '<div class="text-sm text-slate-500">No upcoming runs found.</div>';
        } catch (e) { document.getElementById('nextRuns').innerHTML = ''; }
    }

    document.addEventListener('DOMContentLoaded', describe);
</script>
@endpush
