@extends('layouts.app')

@section('title', 'Password Strength')
@section('description', 'Check password strength, entropy and estimated crack time.')

@section('content')
    <x-tool-header title="Password Strength Checker" subtitle="Estimate entropy, crack time and spot weaknesses — all offline."
        from="from-rose-500" to="to-pink-500" icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />

    <div class="mx-auto max-w-3xl rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">Password</label>
        <div class="relative">
            <input id="pw" type="text" oninput="check()" spellcheck="false" autocomplete="off"
                class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3.5 pr-12 font-mono text-lg text-white placeholder-slate-500 transition focus:border-rose-400/60 focus:outline-none focus:ring-2 focus:ring-rose-500/30"
                placeholder="Type a password to test…">
        </div>

        <div class="mt-5">
            <div class="h-2.5 overflow-hidden rounded-full bg-white/10"><div id="bar" class="h-full w-0 rounded-full transition-all duration-300"></div></div>
            <div class="mt-2 flex items-center justify-between text-sm">
                <span id="label" class="font-bold text-slate-400">—</span>
                <span id="crack" class="text-slate-400"></span>
            </div>
        </div>

        <div id="stats" class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4"></div>
        <div id="checks" class="mt-6 space-y-2"></div>

        <p class="mt-6 text-xs text-slate-500">All checks run locally — your password never leaves your device.</p>
    </div>
@endsection

@push('scripts')
<script>
    const COMMON = ['password','123456','12345678','qwerty','abc123','111111','letmein','admin','welcome','monkey','dragon','iloveyou','password1','000000'];

    function check() {
        const pw = document.getElementById('pw').value;
        const bar = document.getElementById('bar');
        const label = document.getElementById('label');
        const crack = document.getElementById('crack');

        if (!pw) { bar.style.width = '0%'; label.textContent = '—'; label.className = 'font-bold text-slate-400'; crack.textContent = ''; document.getElementById('stats').innerHTML = ''; document.getElementById('checks').innerHTML = ''; return; }

        let pool = 0;
        if (/[a-z]/.test(pw)) pool += 26;
        if (/[A-Z]/.test(pw)) pool += 26;
        if (/[0-9]/.test(pw)) pool += 10;
        if (/[^a-zA-Z0-9]/.test(pw)) pool += 33;
        const entropy = pw.length * Math.log2(pool || 1);

        const isCommon = COMMON.includes(pw.toLowerCase());
        const hasRepeat = /(.)\1{2,}/.test(pw);
        const hasSeq = /(012|123|234|345|456|567|678|789|abc|bcd|cde|qwe|asd)/i.test(pw);
        let effective = entropy;
        if (isCommon) effective = Math.min(effective, 8);
        if (hasRepeat) effective -= 10;
        if (hasSeq) effective -= 8;
        effective = Math.max(0, effective);

        let pct, color, text;
        if (isCommon) { pct = 8; color = 'bg-rose-500'; text = 'Compromised'; }
        else if (effective < 40) { pct = 25; color = 'bg-rose-500'; text = 'Weak'; }
        else if (effective < 60) { pct = 50; color = 'bg-amber-500'; text = 'Fair'; }
        else if (effective < 90) { pct = 75; color = 'bg-lime-500'; text = 'Strong'; }
        else { pct = 100; color = 'bg-emerald-500'; text = 'Very strong'; }
        bar.className = `h-full rounded-full transition-all duration-300 ${color}`;
        bar.style.width = pct + '%';
        label.textContent = text;
        label.className = `font-bold ${color.replace('bg-', 'text-')}`;

        const guesses = Math.pow(2, effective);
        const perSec = 1e10; // offline fast attacker
        crack.textContent = 'Crack time: ' + humanTime(guesses / perSec);

        const stat = (l, v) => `<div class="rounded-xl border border-white/10 bg-white/5 p-3 text-center"><div class="text-lg font-bold text-white">${v}</div><div class="text-xs text-slate-400">${l}</div></div>`;
        document.getElementById('stats').innerHTML = stat('Length', pw.length) + stat('Charset', pool) + stat('Entropy', Math.round(effective) + ' bits') + stat('Uniques', new Set(pw).size);

        const chk = (ok, txt) => `<div class="flex items-center gap-2 text-sm ${ok ? 'text-emerald-300' : 'text-slate-400'}"><span>${ok ? '✓' : '○'}</span>${txt}</div>`;
        document.getElementById('checks').innerHTML =
            chk(pw.length >= 12, 'At least 12 characters') +
            chk(/[a-z]/.test(pw) && /[A-Z]/.test(pw), 'Upper and lower case letters') +
            chk(/[0-9]/.test(pw), 'Contains numbers') +
            chk(/[^a-zA-Z0-9]/.test(pw), 'Contains symbols') +
            chk(!isCommon, 'Not a common password') +
            chk(!hasRepeat && !hasSeq, 'No obvious repeats or sequences');
    }

    function humanTime(seconds) {
        if (seconds < 1) return 'instantly';
        const units = [['centuries', 3153600000], ['years', 31536000], ['days', 86400], ['hours', 3600], ['minutes', 60], ['seconds', 1]];
        for (const [name, s] of units) { if (seconds >= s) { const v = Math.round(seconds / s); return `${v.toLocaleString()} ${name}`; } }
        return 'instantly';
    }
    document.addEventListener('DOMContentLoaded', check);
</script>
@endpush
