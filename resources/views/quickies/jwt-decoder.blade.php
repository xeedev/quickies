@extends('layouts.app')

@section('title', 'JWT Decoder')
@section('description', 'Decode a JSON Web Token header and payload in your browser.')

@section('content')
    <x-tool-header
        title="JWT Decoder"
        subtitle="Decode a JSON Web Token's header and payload — locally and safely."
        from="from-indigo-500"
        to="to-blue-500"
        icon="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">Paste your JWT</label>
        <textarea id="jwtInput" rows="5" spellcheck="false" oninput="decode()"
            class="w-full break-all rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
            placeholder="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0.signature"></textarea>
        <p id="jwtError" class="mt-2 hidden text-sm font-semibold text-rose-300"></p>

        <div class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-semibold text-rose-300">Header</span>
                    <button onclick="copyPart('header')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <pre id="jwtHeader" class="min-h-[120px] overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm text-rose-200"></pre>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <span class="text-sm font-semibold text-indigo-300">Payload</span>
                    <button onclick="copyPart('payload')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <pre id="jwtPayload" class="min-h-[120px] overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm text-indigo-200"></pre>
            </div>
        </div>

        <div id="claims" class="mt-5"></div>

        <div class="mt-6 flex items-start gap-2 rounded-xl border border-amber-400/20 bg-amber-400/5 px-4 py-3 text-sm text-amber-200/90">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path></svg>
            <span>Decoding does not verify the signature. Never paste production secrets or sensitive tokens into any online tool.</span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let parts = { header: '', payload: '' };

    function b64urlDecode(str) {
        str = str.replace(/-/g, '+').replace(/_/g, '/');
        while (str.length % 4) str += '=';
        return decodeURIComponent(Array.prototype.map.call(atob(str), (c) => '%' + c.charCodeAt(0).toString(16).padStart(2, '0')).join(''));
    }

    function decode() {
        const token = document.getElementById('jwtInput').value.trim();
        const err = document.getElementById('jwtError');
        const hEl = document.getElementById('jwtHeader');
        const pEl = document.getElementById('jwtPayload');
        const claims = document.getElementById('claims');
        parts = { header: '', payload: '' };

        if (!token) { hEl.textContent = ''; pEl.textContent = ''; err.classList.add('hidden'); claims.innerHTML = ''; return; }
        const segs = token.split('.');
        if (segs.length < 2) { err.textContent = 'A JWT needs at least a header and payload separated by dots.'; err.classList.remove('hidden'); hEl.textContent = ''; pEl.textContent = ''; claims.innerHTML = ''; return; }

        try {
            const header = JSON.parse(b64urlDecode(segs[0]));
            const payload = JSON.parse(b64urlDecode(segs[1]));
            parts.header = JSON.stringify(header, null, 2);
            parts.payload = JSON.stringify(payload, null, 2);
            hEl.textContent = parts.header;
            pEl.textContent = parts.payload;
            err.classList.add('hidden');
            renderClaims(payload);
        } catch (e) {
            err.textContent = 'Could not decode token: ' + e.message;
            err.classList.remove('hidden');
            hEl.textContent = ''; pEl.textContent = ''; claims.innerHTML = '';
        }
    }

    function renderClaims(p) {
        const rows = [];
        const fmt = (t) => new Date(t * 1000).toLocaleString();
        if (p.iat) rows.push(['Issued at (iat)', fmt(p.iat)]);
        if (p.nbf) rows.push(['Not before (nbf)', fmt(p.nbf)]);
        if (p.exp) {
            const expired = p.exp * 1000 < Date.now();
            rows.push(['Expires (exp)', `${fmt(p.exp)} <span class="ml-2 rounded px-1.5 py-0.5 text-xs font-bold ${expired ? 'bg-rose-500/20 text-rose-300' : 'bg-emerald-500/20 text-emerald-300'}">${expired ? 'EXPIRED' : 'VALID'}</span>`]);
        }
        document.getElementById('claims').innerHTML = rows.length
            ? '<div class="grid grid-cols-1 gap-2 sm:grid-cols-3">' + rows.map(([k, v]) => `<div class="rounded-xl border border-white/10 bg-white/5 p-3"><div class="text-xs text-slate-500">${k}</div><div class="mt-0.5 text-sm text-slate-200">${v}</div></div>`).join('') + '</div>'
            : '';
    }

    function copyPart(which) {
        if (!parts[which]) return showNotification('Nothing to copy.', 'error');
        copyToClipboard(parts[which], which.charAt(0).toUpperCase() + which.slice(1) + ' copied');
    }

    document.addEventListener('DOMContentLoaded', decode);
</script>
@endpush
