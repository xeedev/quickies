@extends('layouts.app')

@section('title', 'Lorem Ipsum')
@section('description', 'Generate placeholder paragraphs, sentences and words.')

@section('content')
    <x-tool-header
        title="Lorem Ipsum Generator"
        subtitle="Generate placeholder paragraphs, sentences or words."
        from="from-slate-500"
        to="to-slate-400"
        icon="M4 6h16M4 12h16M4 18h7" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-[auto_1fr_auto] sm:items-end">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Type</label>
                <select id="unit" onchange="generate()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-slate-300/60 focus:outline-none">
                    <option value="paragraphs" class="bg-slate-900">Paragraphs</option>
                    <option value="sentences" class="bg-slate-900">Sentences</option>
                    <option value="words" class="bg-slate-900">Words</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Amount <span id="amountValue" class="text-slate-400">3</span></label>
                <input type="range" id="amount" min="1" max="30" value="3" class="w-full" oninput="document.getElementById('amountValue').textContent = this.value; generate();">
            </div>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10">
                <input type="checkbox" id="startClassic" checked onchange="generate()" class="h-4 w-4 accent-slate-400">
                Start with “Lorem ipsum”
            </label>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-200">Output</span>
            <button onclick="copyToClipboard(document.getElementById('output').innerText, 'Text copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
        </div>
        <div id="output" class="mt-3 space-y-4 rounded-2xl border border-white/10 bg-slate-950/40 p-5 text-sm leading-relaxed text-slate-300"></div>
    </div>
@endsection

@push('scripts')
<script>
    const WORDS = 'lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua enim ad minim veniam quis nostrud exercitation ullamco laboris nisi aliquip ex ea commodo consequat duis aute irure in reprehenderit voluptate velit esse cillum fugiat nulla pariatur excepteur sint occaecat cupidatat non proident sunt culpa qui officia deserunt mollit anim id est laborum'.split(' ');

    const rand = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;
    const pick = () => WORDS[rand(0, WORDS.length - 1)];

    function makeSentence() {
        const len = rand(6, 14);
        const words = Array.from({ length: len }, pick);
        let s = words.join(' ');
        return s.charAt(0).toUpperCase() + s.slice(1) + '.';
    }

    function makeParagraph() {
        return Array.from({ length: rand(3, 6) }, makeSentence).join(' ');
    }

    function generate() {
        const unit = document.getElementById('unit').value;
        const amount = parseInt(document.getElementById('amount').value);
        const classic = document.getElementById('startClassic').checked;
        const output = document.getElementById('output');
        let html = '';

        if (unit === 'words') {
            const words = Array.from({ length: amount }, pick);
            if (classic) { words[0] = 'Lorem'; if (words[1]) words[1] = 'ipsum'; }
            html = `<p>${words.join(' ')}</p>`;
        } else if (unit === 'sentences') {
            const arr = Array.from({ length: amount }, makeSentence);
            if (classic) arr[0] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.';
            html = `<p>${arr.join(' ')}</p>`;
        } else {
            const arr = Array.from({ length: amount }, makeParagraph);
            if (classic) arr[0] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ' + arr[0];
            html = arr.map((p) => `<p>${p}</p>`).join('');
        }
        output.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', generate);
</script>
@endpush
