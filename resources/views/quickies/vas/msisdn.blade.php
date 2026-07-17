@extends('layouts.app')

@section('title', 'MSISDN Validator & Formatter')
@section('description', 'Validate and format phone numbers to E.164 with carrier hints.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/libphonenumber-js@1.11.4/bundle/libphonenumber-max.js"></script>
@endpush

@section('content')
    <x-tool-header title="MSISDN Validator & Formatter" subtitle="Validate, normalise and format phone numbers to international standards."
        from="from-blue-500" to="to-indigo-500" icon="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11 11 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="flex flex-col gap-3 sm:flex-row">
            <input id="default-country" placeholder="Default country (e.g. US, GB, IN)" maxlength="2" oninput="this.value=this.value.toUpperCase(); analyze()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white placeholder-slate-500 focus:border-indigo-400/60 focus:outline-none sm:w-64">
        </div>
        <label class="mb-2 mt-4 block text-sm font-semibold text-slate-200">Phone numbers <span class="font-normal text-slate-400">(one per line)</span></label>
        <textarea id="input" rows="8" oninput="analyze()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30" placeholder="+14155552671&#10;07911 123456&#10;+91 98765 43210"></textarea>
        <div id="results" class="mt-6 space-y-2"></div>
    </div>
@endsection

@push('scripts')
<script>
    function analyze() {
        const lib = window.libphonenumber;
        const def = document.getElementById('default-country').value.trim() || undefined;
        const lines = document.getElementById('input').value.split('\n').map((l) => l.trim()).filter(Boolean);
        const out = document.getElementById('results');
        if (!lines.length) { out.innerHTML = '<p class="text-sm text-slate-500">Enter one or more phone numbers above.</p>'; return; }
        out.innerHTML = lines.map((line) => {
            let p;
            try { p = lib.parsePhoneNumber(line, def); } catch (e) { p = null; }
            const valid = p && p.isValid();
            const badge = valid ? '<span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-xs font-bold text-emerald-300">VALID</span>' : '<span class="rounded-full bg-rose-500/20 px-2.5 py-0.5 text-xs font-bold text-rose-300">INVALID</span>';
            if (!p) return `<div class="rounded-2xl border border-white/10 bg-white/5 p-4"><div class="flex items-center justify-between"><span class="font-mono text-slate-200">${line.replace(/</g,'&lt;')}</span>${badge}</div><p class="mt-1 text-xs text-slate-500">Could not parse this number.</p></div>`;
            const rows = [['E.164', p.number], ['International', p.formatInternational()], ['National', p.formatNational()], ['Country', `${p.country || '—'} (+${p.countryCallingCode})`], ['Type', p.getType() || '—'], ['URI', p.getURI()]];
            return `<div class="rounded-2xl border ${valid ? 'border-emerald-400/20' : 'border-rose-400/20'} bg-white/5 p-4">
                <div class="mb-2 flex items-center justify-between"><span class="font-mono text-slate-200">${line.replace(/</g,'&lt;')}</span>${badge}</div>
                <div class="grid grid-cols-1 gap-1.5 sm:grid-cols-2">${rows.map(([k, v]) => `<div class="flex items-center justify-between gap-2 rounded-lg bg-black/20 px-3 py-1.5 text-sm"><span class="text-slate-500">${k}</span><span class="min-w-0 truncate font-mono text-slate-200">${String(v).replace(/</g,'&lt;')}</span></div>`).join('')}</div>
            </div>`;
        }).join('');
    }
    document.addEventListener('DOMContentLoaded', () => { document.getElementById('input').value = '+14155552671\n+447911123456\n+919876543210'; analyze(); });
</script>
@endpush
