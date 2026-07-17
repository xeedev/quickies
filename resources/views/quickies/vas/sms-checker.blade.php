@extends('layouts.app')

@section('title', 'SMS Encoding Checker')
@section('description', 'Count SMS segments and detect GSM-7 vs UCS-2 encoding.')

@section('content')
    <x-tool-header title="SMS Character & Encoding Checker" subtitle="See how your message splits into SMS segments and which encoding it uses."
        from="from-cyan-500" to="to-blue-500" icon="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4v-4z" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_320px]">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <label class="mb-2 block text-sm font-semibold text-slate-200">Message</label>
            <textarea id="msg" rows="8" oninput="analyze()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-cyan-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/30" placeholder="Type your SMS content…">Your OTP is 1234. Reply STOP to unsubscribe.</textarea>
            <div id="nonGsm" class="mt-3 hidden rounded-xl border border-amber-400/20 bg-amber-400/5 px-4 py-3 text-sm text-amber-200"></div>
        </div>

        <div class="space-y-3">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-5 text-center">
                <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Encoding</div>
                <div id="encoding" class="mt-1 text-2xl font-bold text-cyan-300">GSM-7</div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center"><div id="chars" class="text-2xl font-bold text-white">0</div><div class="text-xs text-slate-400">Characters</div></div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center"><div id="segments" class="text-2xl font-bold text-white">0</div><div class="text-xs text-slate-400">Segments</div></div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center"><div id="perSeg" class="text-2xl font-bold text-white">0</div><div class="text-xs text-slate-400">Per segment</div></div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center"><div id="remaining" class="text-2xl font-bold text-white">0</div><div class="text-xs text-slate-400">Remaining</div></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const GSM = "@£$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞ ÆæßÉ !\"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà";
    const GSM_EXT = "^{}\\[~]|€";

    function analyze() {
        const msg = document.getElementById('msg').value;
        let isGsm = true;
        const nonGsmChars = new Set();
        let gsmLen = 0;
        for (const ch of msg) {
            if (GSM.includes(ch)) gsmLen += 1;
            else if (GSM_EXT.includes(ch)) gsmLen += 2;
            else { isGsm = false; nonGsmChars.add(ch); }
        }
        // count actual code points for UCS-2
        const codePoints = [...msg].length;
        const encoding = isGsm ? 'GSM-7' : 'UCS-2';
        const single = isGsm ? 160 : 70;
        const multi = isGsm ? 153 : 67;
        const length = isGsm ? gsmLen : codePoints;
        let segments, per;
        if (length === 0) { segments = 0; per = single; }
        else if (length <= single) { segments = 1; per = single; }
        else { segments = Math.ceil(length / multi); per = multi; }
        const remaining = length === 0 ? single : (segments === 1 ? single - length : segments * multi - length);

        document.getElementById('encoding').textContent = encoding;
        document.getElementById('encoding').className = `mt-1 text-2xl font-bold ${isGsm ? 'text-cyan-300' : 'text-amber-300'}`;
        document.getElementById('chars').textContent = length;
        document.getElementById('segments').textContent = segments;
        document.getElementById('perSeg').textContent = per;
        document.getElementById('remaining').textContent = remaining;

        const warn = document.getElementById('nonGsm');
        if (!isGsm) { warn.classList.remove('hidden'); warn.innerHTML = `Message contains non-GSM characters (<span class="font-mono">${[...nonGsmChars].slice(0, 20).join(' ').replace(/</g,'&lt;')}</span>), forcing UCS-2 encoding — this reduces each segment to 70 characters.`; }
        else warn.classList.add('hidden');
    }
    document.addEventListener('DOMContentLoaded', analyze);
</script>
@endpush
