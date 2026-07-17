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
        <textarea id="hashInput" rows="5" oninput="computeAll()"
            class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-fuchsia-400/60 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30"
            placeholder="Type or paste text here…">hello world</textarea>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/10">
                <input type="checkbox" id="uppercase" onchange="computeAll()" class="h-4 w-4 accent-fuchsia-500">
                Uppercase output
            </label>
        </div>

        <div id="results" class="mt-6 space-y-3"></div>
    </div>
@endsection

@push('scripts')
<script>
    const ALGOS = ['SHA-1', 'SHA-256', 'SHA-384', 'SHA-512'];

    function buildRows() {
        document.getElementById('results').innerHTML = ALGOS.map((a) => `
            <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                <div class="mb-2 flex items-center justify-between">
                    <span class="rounded-lg bg-fuchsia-500/20 px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide text-fuchsia-200">${a}</span>
                    <button onclick="copyHash('${a}')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <code id="hash-${a}" class="block break-all font-mono text-sm text-slate-300"></code>
            </div>`).join('');
    }

    async function computeAll() {
        const text = document.getElementById('hashInput').value;
        const upper = document.getElementById('uppercase').checked;
        const data = new TextEncoder().encode(text);
        for (const algo of ALGOS) {
            const buf = await crypto.subtle.digest(algo, data);
            let hex = Array.from(new Uint8Array(buf)).map((b) => b.toString(16).padStart(2, '0')).join('');
            if (upper) hex = hex.toUpperCase();
            document.getElementById(`hash-${algo}`).textContent = hex;
        }
    }

    function copyHash(algo) {
        copyToClipboard(document.getElementById(`hash-${algo}`).textContent, `${algo} hash copied`);
    }

    document.addEventListener('DOMContentLoaded', () => { buildRows(); computeAll(); });
</script>
@endpush
