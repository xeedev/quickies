@extends('layouts.app')

@section('title', 'Hash Generator')
@section('description', 'Generate SHA-1, SHA-256, SHA-384 and SHA-512 hashes in your browser.')

@section('content')
    <x-tool-header
        title="Hash Generator"
        subtitle="Generate SHA-1, SHA-256, SHA-384 and SHA-512 hashes locally."
        from="from-fuchsia-500"
        to="to-purple-500"
        icon="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />

    <div class="mx-auto max-w-4xl rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">Text to hash</label>
        <textarea id="hashInput" rows="5" oninput="onTextInput()"
            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-fuchsia-400/60 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30"
            placeholder="Type or paste text here…">hello world</textarea>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                Hash a file
                <input type="file" id="fileInput" class="hidden" onchange="handleFile(event)">
            </label>
            <span id="fileChip" class="hidden items-center gap-2 rounded-xl border border-fuchsia-400/30 bg-fuchsia-500/10 px-3 py-2 text-sm text-fuchsia-200">
                <span id="fileName" class="max-w-[220px] truncate font-mono"></span>
                <button type="button" onclick="clearFile()" class="text-fuchsia-300 hover:text-white">✕</button>
            </span>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10">
                <input type="checkbox" id="useHmac" onchange="toggleHmac()" class="h-4 w-4 accent-fuchsia-500">
                HMAC
            </label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10">
                <input type="checkbox" id="uppercase" onchange="computeAll()" class="h-4 w-4 accent-fuchsia-500">
                Uppercase
            </label>
        </div>

        <div id="hmacRow" class="mt-3 hidden">
            <input id="hmacKey" oninput="computeAll()" placeholder="HMAC secret key"
                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 font-mono text-sm text-white placeholder-slate-500 focus:border-fuchsia-400/60 focus:outline-none">
        </div>

        <div id="results" class="mt-6 space-y-3"></div>
    </div>
@endsection

@push('scripts')
<script>
    const ALGOS = ['SHA-1', 'SHA-256', 'SHA-384', 'SHA-512'];
    let fileBuffer = null;

    function buildRows() {
        document.getElementById('results').innerHTML = ALGOS.map((a) => `
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="rounded-lg bg-fuchsia-500/20 px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide text-fuchsia-200" id="algoLabel-${a}">${a}</span>
                    <button onclick="copyHash('${a}')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <code id="hash-${a}" class="block break-all font-mono text-sm text-slate-300"></code>
            </div>`).join('');
    }

    const toHex = (buf, upper) => {
        let hex = Array.from(new Uint8Array(buf)).map((b) => b.toString(16).padStart(2, '0')).join('');
        return upper ? hex.toUpperCase() : hex;
    };

    async function computeAll() {
        const upper = document.getElementById('uppercase').checked;
        const useHmac = document.getElementById('useHmac').checked;
        const keyText = document.getElementById('hmacKey').value;
        const data = fileBuffer || new TextEncoder().encode(document.getElementById('hashInput').value);

        for (const algo of ALGOS) {
            const el = document.getElementById(`hash-${algo}`);
            const label = document.getElementById(`algoLabel-${algo}`);
            try {
                let buf;
                if (useHmac) {
                    label.textContent = 'HMAC-' + algo;
                    const key = await crypto.subtle.importKey('raw', new TextEncoder().encode(keyText), { name: 'HMAC', hash: algo }, false, ['sign']);
                    buf = await crypto.subtle.sign('HMAC', key, data);
                } else {
                    label.textContent = algo;
                    buf = await crypto.subtle.digest(algo, data);
                }
                el.textContent = toHex(buf, upper);
            } catch (e) {
                el.textContent = '—';
            }
        }
    }

    function onTextInput() { if (!fileBuffer) computeAll(); }

    function toggleHmac() {
        document.getElementById('hmacRow').classList.toggle('hidden', !document.getElementById('useHmac').checked);
        computeAll();
    }

    async function handleFile(e) {
        const f = e.target.files[0];
        if (!f) return;
        fileBuffer = await f.arrayBuffer();
        document.getElementById('fileName').textContent = f.name + ' · ' + (f.size < 1024 ? f.size + ' B' : (f.size / 1024).toFixed(1) + ' KB');
        document.getElementById('fileChip').classList.remove('hidden');
        document.getElementById('fileChip').classList.add('inline-flex');
        showNotification('Hashing ' + f.name, 'info');
        computeAll();
    }

    function clearFile() {
        fileBuffer = null;
        document.getElementById('fileInput').value = '';
        const chip = document.getElementById('fileChip');
        chip.classList.add('hidden');
        chip.classList.remove('inline-flex');
        computeAll();
    }

    function copyHash(algo) {
        copyToClipboard(document.getElementById(`hash-${algo}`).textContent, `${algo} hash copied`);
    }

    document.addEventListener('DOMContentLoaded', () => { buildRows(); computeAll(); });
</script>
@endpush
