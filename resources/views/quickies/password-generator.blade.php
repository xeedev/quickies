@extends('layouts.app')

@section('title', 'Password Generator')
@section('description', 'Create strong, random passwords with custom rules.')

@section('content')
    <x-tool-header
        title="Password Generator"
        subtitle="Create strong, cryptographically-random passwords."
        from="from-rose-500"
        to="to-red-500"
        icon="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />

    <div class="mx-auto max-w-3xl rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        {{-- Output --}}
        <div class="rounded-2xl border border-white/10 bg-slate-950/50 p-4">
            <div class="flex items-center gap-3">
                <code id="pwOutput" class="min-w-0 flex-1 break-all font-mono text-lg text-white sm:text-xl">·········</code>
                <button onclick="regenerate()" title="Regenerate" class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5 transition hover:bg-white/10">
                    <svg class="h-5 w-5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
                <button onclick="copyToClipboard(document.getElementById('pwOutput').textContent, 'Password copied')" title="Copy" class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/5 transition hover:bg-white/10">
                    <svg class="h-5 w-5 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                </button>
            </div>
            {{-- Strength meter --}}
            <div class="mt-4">
                <div class="h-2 overflow-hidden rounded-full bg-white/10">
                    <div id="strengthBar" class="h-full w-0 rounded-full bg-rose-500 transition-all duration-300"></div>
                </div>
                <p id="strengthLabel" class="mt-2 text-sm font-semibold text-slate-400">Strength</p>
            </div>
        </div>

        {{-- Options --}}
        <div class="mt-6 space-y-6">
            <div>
                <label class="mb-2 flex items-center justify-between text-sm font-semibold text-slate-200">
                    <span>Length</span>
                    <span id="lengthValue" class="font-mono text-rose-300">16</span>
                </label>
                <input type="range" id="length" min="4" max="64" value="16" class="w-full" oninput="document.getElementById('lengthValue').textContent = this.value; regenerate();">
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
                    <span class="text-sm font-medium text-slate-200">Uppercase (A-Z)</span>
                    <input type="checkbox" id="optUpper" checked onchange="regenerate()" class="h-5 w-5 accent-rose-500">
                </label>
                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
                    <span class="text-sm font-medium text-slate-200">Lowercase (a-z)</span>
                    <input type="checkbox" id="optLower" checked onchange="regenerate()" class="h-5 w-5 accent-rose-500">
                </label>
                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
                    <span class="text-sm font-medium text-slate-200">Numbers (0-9)</span>
                    <input type="checkbox" id="optNumber" checked onchange="regenerate()" class="h-5 w-5 accent-rose-500">
                </label>
                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
                    <span class="text-sm font-medium text-slate-200">Symbols (!@#$)</span>
                    <input type="checkbox" id="optSymbol" checked onchange="regenerate()" class="h-5 w-5 accent-rose-500">
                </label>
                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10 sm:col-span-2">
                    <span class="text-sm font-medium text-slate-200">Exclude ambiguous (Il1O0)</span>
                    <input type="checkbox" id="optNoAmbiguous" onchange="regenerate()" class="h-5 w-5 accent-rose-500">
                </label>
            </div>

            <button onclick="regenerate()" class="w-full rounded-2xl bg-gradient-to-r from-rose-500 to-red-500 px-6 py-3.5 font-semibold text-white shadow-lg shadow-rose-500/25 transition hover:scale-[1.01] active:scale-95">
                Generate new password
            </button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const SETS = {
        upper: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        lower: 'abcdefghijklmnopqrstuvwxyz',
        number: '0123456789',
        symbol: '!@#$%^&*()-_=+[]{};:,.<>?/',
    };
    const AMBIGUOUS = /[Il1O0]/g;

    function regenerate() {
        const length = parseInt(document.getElementById('length').value);
        let pool = '';
        if (document.getElementById('optUpper').checked) pool += SETS.upper;
        if (document.getElementById('optLower').checked) pool += SETS.lower;
        if (document.getElementById('optNumber').checked) pool += SETS.number;
        if (document.getElementById('optSymbol').checked) pool += SETS.symbol;
        if (document.getElementById('optNoAmbiguous').checked) pool = pool.replace(AMBIGUOUS, '');

        const output = document.getElementById('pwOutput');
        if (!pool) {
            output.textContent = 'Select at least one character set';
            updateStrength('');
            return;
        }
        const bytes = new Uint32Array(length);
        crypto.getRandomValues(bytes);
        let pw = '';
        for (let i = 0; i < length; i++) pw += pool[bytes[i] % pool.length];
        output.textContent = pw;
        updateStrength(pw, pool.length);
    }

    function updateStrength(pw, poolSize = 0) {
        const bar = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        if (!pw) { bar.style.width = '0%'; label.textContent = 'Strength'; return; }
        const entropy = pw.length * Math.log2(poolSize || 1);
        let pct, color, text;
        if (entropy < 40) { pct = 25; color = 'bg-rose-500'; text = 'Weak'; }
        else if (entropy < 60) { pct = 50; color = 'bg-amber-500'; text = 'Fair'; }
        else if (entropy < 90) { pct = 75; color = 'bg-lime-500'; text = 'Strong'; }
        else { pct = 100; color = 'bg-emerald-500'; text = 'Very strong'; }
        bar.className = `h-full rounded-full transition-all duration-300 ${color}`;
        bar.style.width = pct + '%';
        label.textContent = `${text} · ~${Math.round(entropy)} bits of entropy`;
    }

    document.addEventListener('DOMContentLoaded', regenerate);
</script>
@endpush
