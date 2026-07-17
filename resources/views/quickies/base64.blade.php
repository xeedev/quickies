@extends('layouts.app')

@section('title', 'Base64 Encoder')
@section('description', 'Encode and decode text or files to and from Base64.')

@section('content')
    <x-tool-header
        title="Base64 Encoder / Decoder"
        subtitle="Convert text and files to and from Base64 (UTF-8 safe)."
        from="from-teal-500"
        to="to-cyan-500"
        icon="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-6 inline-flex rounded-2xl border border-white/10 bg-white/5 p-1">
            <button id="tabEncode" onclick="setMode('encode')" class="rounded-xl px-5 py-2 text-sm font-semibold text-white transition">Encode</button>
            <button id="tabDecode" onclick="setMode('decode')" class="rounded-xl px-5 py-2 text-sm font-semibold text-slate-400 transition">Decode</button>
        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label id="inputLabel" class="text-sm font-semibold text-slate-200">Plain text</label>
                    <label class="cursor-pointer rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">
                        Upload file
                        <input type="file" id="fileInput" class="hidden" onchange="encodeFile(event)">
                    </label>
                </div>
                <textarea id="inputArea" rows="10" oninput="convert()"
                    class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-teal-400/60 focus:outline-none focus:ring-2 focus:ring-teal-500/30"
                    placeholder="Enter text…"></textarea>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label id="outputLabel" class="text-sm font-semibold text-slate-200">Base64</label>
                    <button onclick="copyOut()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <textarea id="outputArea" rows="10" readonly
                    class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-teal-200 placeholder-slate-500 focus:outline-none"
                    placeholder="Result…"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let mode = 'encode';

    function setMode(m) {
        mode = m;
        document.getElementById('tabEncode').className = `rounded-xl px-5 py-2 text-sm font-semibold transition ${m === 'encode' ? 'bg-gradient-to-r from-teal-500 to-cyan-500 text-white shadow' : 'text-slate-400'}`;
        document.getElementById('tabDecode').className = `rounded-xl px-5 py-2 text-sm font-semibold transition ${m === 'decode' ? 'bg-gradient-to-r from-teal-500 to-cyan-500 text-white shadow' : 'text-slate-400'}`;
        document.getElementById('inputLabel').textContent = m === 'encode' ? 'Plain text' : 'Base64';
        document.getElementById('outputLabel').textContent = m === 'encode' ? 'Base64' : 'Plain text';
        document.getElementById('inputArea').placeholder = m === 'encode' ? 'Enter text…' : 'Paste Base64…';
        convert();
    }

    function encodeText(str) {
        return btoa(String.fromCharCode(...new TextEncoder().encode(str)));
    }

    function decodeText(b64) {
        const clean = b64.replace(/\s+/g, '');
        const bytes = Uint8Array.from(atob(clean), (c) => c.charCodeAt(0));
        return new TextDecoder().decode(bytes);
    }

    function convert() {
        const input = document.getElementById('inputArea').value;
        const out = document.getElementById('outputArea');
        if (!input) { out.value = ''; return; }
        try {
            out.value = mode === 'encode' ? encodeText(input) : decodeText(input);
        } catch (e) {
            out.value = mode === 'decode' ? '⚠ Invalid Base64 input' : '';
        }
    }

    function encodeFile(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            const b64 = e.target.result.split(',')[1] || '';
            if (mode !== 'encode') setMode('encode');
            document.getElementById('inputArea').value = `[file: ${file.name}]`;
            document.getElementById('outputArea').value = b64;
            showNotification(`Encoded ${file.name}`, 'success');
        };
        reader.readAsDataURL(file);
        event.target.value = '';
    }

    function copyOut() {
        const val = document.getElementById('outputArea').value;
        if (!val) return showNotification('Nothing to copy.', 'error');
        copyToClipboard(val, 'Result copied');
    }

    document.addEventListener('DOMContentLoaded', () => setMode('encode'));
</script>
@endpush
