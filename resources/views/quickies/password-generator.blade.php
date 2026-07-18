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
        {{-- Mode --}}
        <div class="mb-5 inline-flex rounded-xl border border-white/10 bg-white/5 p-1">
            <button type="button" data-mode="random" class="mode-btn rounded-lg px-4 py-1.5 text-sm font-semibold text-white">Random</button>
            <button type="button" data-mode="phrase" class="mode-btn rounded-lg px-4 py-1.5 text-sm font-semibold text-slate-400">Passphrase</button>
        </div>

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

        {{-- Random options --}}
        <div id="randomOpts" class="mt-6 space-y-6">
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
        </div>

        {{-- Passphrase options --}}
        <div id="phraseOpts" class="mt-6 hidden space-y-6">
            <div>
                <label class="mb-2 flex items-center justify-between text-sm font-semibold text-slate-200">
                    <span>Words</span>
                    <span id="wordCountValue" class="font-mono text-rose-300">4</span>
                </label>
                <input type="range" id="wordCount" min="3" max="10" value="4" class="w-full" oninput="document.getElementById('wordCountValue').textContent = this.value; regenerate();">
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-slate-200">Separator</label>
                    <select id="phraseSep" onchange="regenerate()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-rose-400/60 focus:outline-none">
                        <option value="-" class="bg-slate-900">Hyphen ( - )</option>
                        <option value="." class="bg-slate-900">Dot ( . )</option>
                        <option value="_" class="bg-slate-900">Underscore ( _ )</option>
                        <option value=" " class="bg-slate-900">Space</option>
                    </select>
                </div>
                <label class="flex cursor-pointer items-center justify-between self-end rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10">
                    <span class="text-sm font-medium text-slate-200">Capitalize words</span>
                    <input type="checkbox" id="phraseCap" checked onchange="regenerate()" class="h-5 w-5 accent-rose-500">
                </label>
                <label class="flex cursor-pointer items-center justify-between rounded-xl border border-white/10 bg-white/5 px-4 py-3 transition hover:bg-white/10 sm:col-span-2">
                    <span class="text-sm font-medium text-slate-200">Append a number</span>
                    <input type="checkbox" id="phraseNum" checked onchange="regenerate()" class="h-5 w-5 accent-rose-500">
                </label>
            </div>
        </div>

        <button onclick="regenerate()" class="mt-6 w-full rounded-2xl bg-gradient-to-r from-rose-500 to-red-500 px-6 py-3.5 font-semibold text-white shadow-lg shadow-rose-500/25 transition hover:scale-[1.01] active:scale-95">
            Generate new
        </button>

        {{-- Alternatives --}}
        <div class="mt-6">
            <p class="mb-2 text-sm font-semibold text-slate-200">Or pick another</p>
            <div id="altList" class="space-y-2"></div>
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
    const WORDS = 'time,people,year,way,day,thing,life,world,school,state,family,student,group,country,problem,hand,part,place,case,week,company,system,program,work,number,night,point,home,water,room,mother,area,money,story,fact,month,book,job,word,business,issue,side,kind,head,house,service,friend,father,power,hour,game,line,member,law,city,name,team,minute,idea,body,parent,face,level,office,door,health,person,history,party,result,change,morning,reason,research,moment,teacher,force,river,ocean,forest,mountain,garden,music,coffee,summer,winter,spring,autumn,silver,golden,purple,orange,planet,rocket,dragon,castle,bridge,island,harbor,meadow,thunder,shadow,ember,crystal,falcon,willow,copper,maple,cedar,harvest,cosmic,velvet,marble,quartz,breeze'.split(',');

    let mode = 'random';

    function genPassword() {
        const length = parseInt(document.getElementById('length').value);
        let pool = '';
        if (document.getElementById('optUpper').checked) pool += SETS.upper;
        if (document.getElementById('optLower').checked) pool += SETS.lower;
        if (document.getElementById('optNumber').checked) pool += SETS.number;
        if (document.getElementById('optSymbol').checked) pool += SETS.symbol;
        if (document.getElementById('optNoAmbiguous').checked) pool = pool.replace(AMBIGUOUS, '');
        if (!pool) return null;
        const bytes = new Uint32Array(length);
        crypto.getRandomValues(bytes);
        let pw = '';
        for (let i = 0; i < length; i++) pw += pool[bytes[i] % pool.length];
        return { pw, entropy: length * Math.log2(pool.length) };
    }

    function genPhrase() {
        const n = parseInt(document.getElementById('wordCount').value);
        const sep = document.getElementById('phraseSep').value;
        const cap = document.getElementById('phraseCap').checked;
        const num = document.getElementById('phraseNum').checked;
        const idx = crypto.getRandomValues(new Uint32Array(n));
        const words = Array.from({ length: n }, (_, i) => {
            let w = WORDS[idx[i] % WORDS.length];
            return cap ? w[0].toUpperCase() + w.slice(1) : w;
        });
        let phrase = words.join(sep);
        if (num) phrase += sep + (crypto.getRandomValues(new Uint32Array(1))[0] % 100);
        const entropy = n * Math.log2(WORDS.length) + (num ? Math.log2(100) : 0);
        return { pw: phrase, entropy };
    }

    function makeOne() {
        return mode === 'phrase' ? genPhrase() : genPassword();
    }

    function regenerate() {
        const out = document.getElementById('pwOutput');
        const r = makeOne();
        if (!r) { out.textContent = 'Select at least one character set'; updateStrength(0); document.getElementById('altList').innerHTML = ''; return; }
        out.textContent = r.pw;
        updateStrength(r.entropy);
        renderAlternatives();
    }

    function renderAlternatives() {
        const alts = Array.from({ length: 4 }, () => makeOne()).filter(Boolean);
        document.getElementById('altList').innerHTML = alts.map((a) => {
            const safe = a.pw.replace(/'/g, "\\'").replace(/"/g, '&quot;');
            return `<div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5">
                <code class="min-w-0 flex-1 truncate font-mono text-sm text-slate-300">${a.pw.replace(/</g,'&lt;')}</code>
                <button onclick="copyToClipboard('${safe}', 'Password copied')" class="flex-shrink-0 rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            </div>`;
        }).join('');
    }

    function updateStrength(entropy) {
        const bar = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        if (!entropy) { bar.style.width = '0%'; label.textContent = 'Strength'; return; }
        let pct, color, text;
        if (entropy < 40) { pct = 25; color = 'bg-rose-500'; text = 'Weak'; }
        else if (entropy < 60) { pct = 50; color = 'bg-amber-500'; text = 'Fair'; }
        else if (entropy < 90) { pct = 75; color = 'bg-lime-500'; text = 'Strong'; }
        else { pct = 100; color = 'bg-emerald-500'; text = 'Very strong'; }
        bar.className = `h-full rounded-full transition-all duration-300 ${color}`;
        bar.style.width = pct + '%';
        label.textContent = `${text} · ~${Math.round(entropy)} bits of entropy`;
    }

    (function () {
        document.querySelectorAll('[data-mode]').forEach((btn) => btn.addEventListener('click', () => {
            mode = btn.dataset.mode;
            document.querySelectorAll('[data-mode]').forEach((b) => {
                const on = b.dataset.mode === mode;
                b.classList.toggle('bg-white/10', on);
                b.classList.toggle('text-white', on);
                b.classList.toggle('text-slate-400', !on);
            });
            document.getElementById('randomOpts').classList.toggle('hidden', mode !== 'random');
            document.getElementById('phraseOpts').classList.toggle('hidden', mode !== 'phrase');
            regenerate();
        }));
        // initial active style
        document.querySelector('[data-mode="random"]').classList.add('bg-white/10');
    })();

    document.addEventListener('DOMContentLoaded', regenerate);
</script>
@endpush
