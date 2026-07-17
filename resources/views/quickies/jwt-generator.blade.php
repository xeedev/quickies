@extends('layouts.app')

@section('title', 'JWT Generator')
@section('description', 'Sign HS256, HS384 and HS512 JSON Web Tokens in the browser.')

@section('content')
    <x-tool-header title="JWT Generator" subtitle="Sign HMAC (HS256/384/512) JSON Web Tokens locally."
        from="from-blue-500" to="to-indigo-500" icon="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Payload (JSON)</label>
                <textarea id="payload" rows="8" spellcheck="false" oninput="sign()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"></textarea>
                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-200">Algorithm</label>
                        <select id="alg" onchange="sign()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-indigo-400/60 focus:outline-none">
                            <option class="bg-slate-900">HS256</option>
                            <option class="bg-slate-900">HS384</option>
                            <option class="bg-slate-900">HS512</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold text-slate-200">Secret</label>
                        <input id="secret" oninput="sign()" value="your-256-bit-secret" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 font-mono text-sm text-white focus:border-indigo-400/60 focus:outline-none">
                    </div>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <button onclick="addClaim('iat')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">+ iat (now)</button>
                    <button onclick="addClaim('exp')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">+ exp (1h)</button>
                    <button onclick="addClaim('nbf')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold text-slate-200 transition hover:bg-white/10">+ nbf (now)</button>
                </div>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-200">Signed token</label>
                    <button onclick="copyToClipboard(document.getElementById('token').textContent, 'Token copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <div id="token" class="min-h-[160px] break-all rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm leading-relaxed"></div>
                <p id="err" class="mt-2 hidden text-sm font-semibold text-rose-300"></p>
            </div>
        </div>
        <div class="mt-6 flex items-start gap-2 rounded-xl border border-amber-400/20 bg-amber-400/5 px-4 py-3 text-sm text-amber-200/90">
            <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path></svg>
            <span>Signing happens locally in your browser. Never use production secrets on any web page.</span>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const b64url = (buf) => btoa(String.fromCharCode(...new Uint8Array(buf))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
    const b64urlStr = (str) => btoa(unescape(encodeURIComponent(str))).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');

    async function sign() {
        const err = document.getElementById('err');
        const tokenEl = document.getElementById('token');
        let payloadObj;
        try { payloadObj = JSON.parse(document.getElementById('payload').value); err.classList.add('hidden'); }
        catch (e) { err.textContent = 'Payload must be valid JSON.'; err.classList.remove('hidden'); tokenEl.textContent = ''; return; }

        const alg = document.getElementById('alg').value;
        const secret = document.getElementById('secret').value;
        const header = { alg, typ: 'JWT' };
        const headerB64 = b64urlStr(JSON.stringify(header));
        const payloadB64 = b64urlStr(JSON.stringify(payloadObj));
        const data = `${headerB64}.${payloadB64}`;
        const hash = { HS256: 'SHA-256', HS384: 'SHA-384', HS512: 'SHA-512' }[alg];
        try {
            const key = await crypto.subtle.importKey('raw', new TextEncoder().encode(secret), { name: 'HMAC', hash }, false, ['sign']);
            const sig = await crypto.subtle.sign('HMAC', key, new TextEncoder().encode(data));
            const token = `${data}.${b64url(sig)}`;
            tokenEl.innerHTML = `<span class="text-rose-300">${headerB64}</span>.<span class="text-indigo-300">${payloadB64}</span>.<span class="text-emerald-300">${b64url(sig)}</span>`;
            tokenEl.dataset.token = token;
        } catch (e) { err.textContent = e.message; err.classList.remove('hidden'); }
    }

    function addClaim(claim) {
        let obj; try { obj = JSON.parse(document.getElementById('payload').value); } catch (e) { obj = {}; }
        const now = Math.floor(Date.now() / 1000);
        if (claim === 'iat') obj.iat = now;
        if (claim === 'nbf') obj.nbf = now;
        if (claim === 'exp') obj.exp = now + 3600;
        document.getElementById('payload').value = JSON.stringify(obj, null, 2);
        sign();
    }

    // Override copy to use the raw token text
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('payload').value = '{\n  "sub": "1234567890",\n  "name": "Ada Lovelace",\n  "admin": true\n}';
        sign();
        const copyBtn = document.querySelector('[onclick*="token"]');
    });
</script>
@endpush
