@extends('layouts.app')

@section('title', 'Base-N Encoder')
@section('description', 'Encode text across Base16, Base32, Base58 and Base64.')

@section('content')
    <x-tool-header title="Base-N Encoder" subtitle="Encode and decode text across Base16, Base32, Base58 and Base64."
        from="from-cyan-500" to="to-blue-500" icon="M4 7V4h16v3M9 20h6M12 4v16" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">Text</label>
        <textarea id="input" rows="4" oninput="encodeAll()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white placeholder-slate-500 transition focus:border-blue-400/60 focus:outline-none focus:ring-2 focus:ring-blue-500/30" placeholder="Type text to encode…">Quickies</textarea>
        <div id="encoded" class="mt-6 space-y-3"></div>

        <div class="mt-8 border-t border-white/10 pt-6">
            <label class="mb-2 block text-sm font-semibold text-slate-200">Decode</label>
            <div class="flex flex-col gap-3 sm:flex-row">
                <select id="decodeBase" class="rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-blue-400/60 focus:outline-none">
                    <option value="16" class="bg-slate-900">Base16 (Hex)</option>
                    <option value="32" class="bg-slate-900">Base32</option>
                    <option value="58" class="bg-slate-900">Base58</option>
                    <option value="64" class="bg-slate-900" selected>Base64</option>
                </select>
                <input id="decodeInput" oninput="decode()" class="flex-1 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 font-mono text-sm text-white placeholder-slate-500 transition focus:border-blue-400/60 focus:outline-none" placeholder="Paste encoded value…">
            </div>
            <div class="mt-3 rounded-xl border border-white/10 bg-slate-950/50 p-3 font-mono text-sm text-blue-100"><span id="decoded" class="break-all">—</span></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const B32 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    const B58 = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    const toBytes = (s) => new TextEncoder().encode(s);
    const fromBytes = (b) => new TextDecoder().decode(new Uint8Array(b));

    function base16(bytes) { return [...bytes].map((b) => b.toString(16).padStart(2, '0')).join('').toUpperCase(); }
    function unbase16(s) { const out = []; s = s.replace(/\s+/g, ''); for (let i = 0; i < s.length; i += 2) out.push(parseInt(s.substr(i, 2), 16)); return out; }

    function base32(bytes) {
        let bits = 0, value = 0, out = '';
        for (const b of bytes) { value = (value << 8) | b; bits += 8; while (bits >= 5) { out += B32[(value >>> (bits - 5)) & 31]; bits -= 5; } }
        if (bits > 0) out += B32[(value << (5 - bits)) & 31];
        while (out.length % 8) out += '=';
        return out;
    }
    function unbase32(s) {
        s = s.replace(/=+$/, '').toUpperCase().replace(/\s+/g, '');
        let bits = 0, value = 0; const out = [];
        for (const c of s) { const idx = B32.indexOf(c); if (idx < 0) continue; value = (value << 5) | idx; bits += 5; if (bits >= 8) { out.push((value >>> (bits - 8)) & 255); bits -= 8; } }
        return out;
    }

    function base58(bytes) {
        let digits = [0];
        for (const b of bytes) { let carry = b; for (let i = 0; i < digits.length; i++) { carry += digits[i] << 8; digits[i] = carry % 58; carry = (carry / 58) | 0; } while (carry) { digits.push(carry % 58); carry = (carry / 58) | 0; } }
        let zeros = 0; for (const b of bytes) { if (b === 0) zeros++; else break; }
        return '1'.repeat(zeros) + digits.reverse().map((d) => B58[d]).join('');
    }
    function unbase58(s) {
        s = s.trim(); let bytes = [0];
        for (const c of s) { const val = B58.indexOf(c); if (val < 0) continue; let carry = val; for (let i = 0; i < bytes.length; i++) { carry += bytes[i] * 58; bytes[i] = carry & 255; carry >>= 8; } while (carry) { bytes.push(carry & 255); carry >>= 8; } }
        let zeros = 0; for (const c of s) { if (c === '1') zeros++; else break; }
        return new Array(zeros).fill(0).concat(bytes.reverse());
    }

    function base64(bytes) { return btoa(String.fromCharCode(...bytes)); }
    function unbase64(s) { return [...atob(s.replace(/\s+/g, ''))].map((c) => c.charCodeAt(0)); }

    function row(name, value) {
        return `<div class="rounded-2xl border border-white/10 bg-white/5 p-4">
            <div class="mb-1 flex items-center justify-between"><span class="text-xs font-bold uppercase tracking-wide text-slate-400">${name}</span>
            <button data-v="${encodeURIComponent(value)}" onclick="copyToClipboard(decodeURIComponent(this.dataset.v), '${name} copied')" class="rounded-md border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold text-slate-200 transition hover:bg-white/10">Copy</button></div>
            <div class="break-all font-mono text-sm text-white">${value || '<span class="text-slate-600">—</span>'}</div></div>`;
    }

    function encodeAll() {
        const bytes = toBytes(document.getElementById('input').value);
        document.getElementById('encoded').innerHTML =
            row('Base16 (Hex)', base16(bytes)) + row('Base32', base32(bytes)) + row('Base58', base58(bytes)) + row('Base64', base64(bytes));
    }

    function decode() {
        const base = document.getElementById('decodeBase').value;
        const val = document.getElementById('decodeInput').value.trim();
        const el = document.getElementById('decoded');
        if (!val) { el.textContent = '—'; return; }
        try {
            const bytes = base === '16' ? unbase16(val) : base === '32' ? unbase32(val) : base === '58' ? unbase58(val) : unbase64(val);
            el.textContent = fromBytes(bytes) || '—';
        } catch (e) { el.textContent = '⚠ Could not decode'; }
    }
    document.getElementById('decodeBase').addEventListener('change', decode);
    document.addEventListener('DOMContentLoaded', encodeAll);
</script>
@endpush
