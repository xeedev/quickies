@extends('layouts.app')

@section('title', 'URL Encoder')
@section('description', 'Encode and decode URLs and URI components.')

@section('content')
    <x-tool-header title="URL Encoder / Decoder" subtitle="Percent-encode and decode URLs and URI components."
        from="from-blue-500" to="to-indigo-500" icon="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex flex-wrap items-center gap-2">
            <button onclick="encode('component')" class="rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Encode component</button>
            <button onclick="encode('uri')" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Encode full URI</button>
            <button onclick="decode()" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Decode</button>
            <span id="status" class="ml-auto text-sm font-semibold"></span>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Input</label>
                <textarea id="input" rows="10" spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30" placeholder="https://example.com/search?q=hello world&x=1"></textarea>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-200">Output</label>
                    <button onclick="copyToClipboard(document.getElementById('output').value, 'Copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <textarea id="output" rows="10" readonly spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-indigo-100 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function setStatus(ok, msg) { const el = document.getElementById('status'); el.textContent = msg; el.className = `ml-auto text-sm font-semibold ${ok ? 'text-emerald-300' : 'text-rose-300'}`; }
    function encode(mode) {
        const v = document.getElementById('input').value;
        document.getElementById('output').value = mode === 'uri' ? encodeURI(v) : encodeURIComponent(v);
        setStatus(true, 'Encoded');
    }
    function decode() {
        try { document.getElementById('output').value = decodeURIComponent(document.getElementById('input').value.replace(/\+/g, ' ')); setStatus(true, 'Decoded'); }
        catch (e) { setStatus(false, 'Malformed input'); }
    }
    document.addEventListener('DOMContentLoaded', () => { document.getElementById('input').value = 'https://example.com/search?q=hello world&lang=en'; encode('component'); });
</script>
@endpush
