@extends('layouts.app')

@section('title', 'UUID Generator')
@section('description', 'Generate RFC-4122 version 4 UUIDs in bulk.')

@section('content')
    <x-tool-header
        title="UUID Generator"
        subtitle="Generate cryptographically-random version 4 UUIDs."
        from="from-lime-500"
        to="to-green-500"
        icon="M4 6h16M4 10h16M4 14h10M4 18h10M17 15l2 2 4-4" />

    <div class="mx-auto max-w-3xl rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Version</label>
                <select id="version" onchange="generate()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-lime-400/60 focus:outline-none">
                    <option value="4" class="bg-slate-900">v4 · random (most common)</option>
                    <option value="7" class="bg-slate-900">v7 · time-ordered (sortable)</option>
                    <option value="1" class="bg-slate-900">v1 · timestamp + node</option>
                    <option value="nil" class="bg-slate-900">NIL · all zeros</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">How many? <span class="text-slate-500">(1–1000)</span></label>
                <input type="number" id="count" value="5" min="1" max="1000" oninput="generate()"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white transition focus:border-lime-400/60 focus:outline-none focus:ring-2 focus:ring-lime-500/30">
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="upper" onchange="generate()" class="h-4 w-4 accent-lime-500"> Uppercase</label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="hyphens" checked onchange="generate()" class="h-4 w-4 accent-lime-500"> Hyphens</label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="braces" onchange="generate()" class="h-4 w-4 accent-lime-500"> Braces { }</label>
            <button onclick="generate()" class="ml-auto rounded-xl bg-gradient-to-r from-lime-500 to-green-500 px-6 py-2.5 font-semibold text-slate-900 shadow-lg shadow-green-500/25 transition hover:scale-[1.02] active:scale-95">Regenerate</button>
        </div>

        <div class="mt-4 rounded-xl border border-white/10 bg-slate-950/40 px-4 py-3 text-xs text-slate-400" id="versionNote"></div>

        <div class="mt-5 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-200">Results <span id="resultCount" class="text-slate-500"></span></span>
            <div class="flex gap-2">
                <button onclick="downloadTxt()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Download .txt</button>
                <button onclick="copyAll()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy all</button>
            </div>
        </div>
        <div id="list" class="mt-3 max-h-[420px] space-y-2 overflow-y-auto"></div>
    </div>
@endsection

@push('scripts')
<script>
    const hex = (arr) => [...arr].map((x) => x.toString(16).padStart(2, '0')).join('');

    function uuidv4() {
        if (crypto.randomUUID) return crypto.randomUUID();
        const b = crypto.getRandomValues(new Uint8Array(16));
        b[6] = (b[6] & 0x0f) | 0x40;
        b[8] = (b[8] & 0x3f) | 0x80;
        const h = hex(b);
        return `${h.slice(0,8)}-${h.slice(8,12)}-${h.slice(12,16)}-${h.slice(16,20)}-${h.slice(20)}`;
    }

    function uuidv7() {
        const ts = Date.now().toString(16).padStart(12, '0'); // 48-bit ms timestamp
        const r = crypto.getRandomValues(new Uint8Array(10));
        r[0] = (r[0] & 0x0f) | 0x70; // version 7
        r[2] = (r[2] & 0x3f) | 0x80; // variant
        const rh = hex(r);
        return `${ts.slice(0,8)}-${ts.slice(8,12)}-${rh.slice(0,4)}-${rh.slice(4,8)}-${rh.slice(8,20)}`;
    }

    function uuidv1() {
        const node = crypto.getRandomValues(new Uint8Array(6)); node[0] |= 0x01; // random multicast node
        const clockSeq = crypto.getRandomValues(new Uint16Array(1))[0] & 0x3fff;
        const ns = BigInt(Date.now()) * 10000n + 122192928000000000n; // 100-ns intervals since 1582-10-15
        const timeLow = (ns & 0xffffffffn).toString(16).padStart(8, '0');
        const timeMid = ((ns >> 32n) & 0xffffn).toString(16).padStart(4, '0');
        const timeHi = (((ns >> 48n) & 0x0fffn) | 0x1000n).toString(16).padStart(4, '0');
        const csHi = (((clockSeq >> 8) & 0x3f) | 0x80).toString(16).padStart(2, '0');
        const csLo = (clockSeq & 0xff).toString(16).padStart(2, '0');
        return `${timeLow}-${timeMid}-${timeHi}-${csHi}${csLo}-${hex(node)}`;
    }

    const NIL = '00000000-0000-0000-0000-000000000000';

    const NOTES = {
        '4': 'Version 4 UUIDs are 122 bits of randomness — the safe default when you just need a unique id.',
        '7': 'Version 7 embeds a Unix millisecond timestamp, so ids sort chronologically. Great for database keys.',
        '1': 'Version 1 encodes the time and a (here randomised) node id. Ids are time-ordered but reveal when they were made.',
        'nil': 'The special all-zero UUID, used to represent "no value".',
    };

    function makeOne(ver) {
        return ver === '7' ? uuidv7() : ver === '1' ? uuidv1() : ver === 'nil' ? NIL : uuidv4();
    }

    function format(id) {
        if (!document.getElementById('hyphens').checked) id = id.replace(/-/g, '');
        if (document.getElementById('upper').checked) id = id.toUpperCase();
        if (document.getElementById('braces').checked) id = `{${id}}`;
        return id;
    }

    function generate() {
        const ver = document.getElementById('version').value;
        const count = Math.min(1000, Math.max(1, parseInt(document.getElementById('count').value) || 1));
        document.getElementById('versionNote').textContent = NOTES[ver];
        document.getElementById('resultCount').textContent = `· ${count}`;
        const items = Array.from({ length: count }, () => {
            const id = format(makeOne(ver));
            const safe = id.replace(/'/g, "\\'");
            return `
                <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5">
                    <code class="min-w-0 flex-1 truncate font-mono text-sm text-lime-200">${id}</code>
                    <button onclick="copyToClipboard('${safe}', 'UUID copied')" class="flex-shrink-0 rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>`;
        });
        document.getElementById('list').innerHTML = items.join('');
    }

    function allIds() {
        return Array.from(document.querySelectorAll('#list code')).map((c) => c.textContent);
    }

    function copyAll() {
        const ids = allIds();
        if (!ids.length) return showNotification('Generate some UUIDs first.', 'error');
        copyToClipboard(ids.join('\n'), `${ids.length} UUIDs copied`);
    }

    function downloadTxt() {
        const ids = allIds();
        if (!ids.length) return showNotification('Generate some UUIDs first.', 'error');
        const blob = new Blob([ids.join('\n')], { type: 'text/plain' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'uuids.txt';
        a.click();
        URL.revokeObjectURL(a.href);
    }

    document.addEventListener('DOMContentLoaded', generate);
</script>
@endpush
