@extends('layouts.app')

@section('title', 'ID Generator')
@section('description', 'Generate UUID, NanoID and ULID identifiers in bulk.')

@section('content')
    <x-tool-header title="ID Generator" subtitle="Generate UUID, NanoID and ULID identifiers in bulk."
        from="from-lime-500" to="to-green-500" icon="M4 6h16M4 10h16M4 14h10M4 18h10M17 15l2 2 4-4" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 inline-flex flex-wrap gap-1 rounded-2xl border border-white/10 bg-white/5 p-1">
            <button data-tab="uuid" onclick="setType('uuid')" class="rounded-xl px-4 py-2 text-sm font-semibold transition">UUID v4</button>
            <button data-tab="uuidv1" onclick="setType('uuidv1')" class="rounded-xl px-4 py-2 text-sm font-semibold transition">UUID v1</button>
            <button data-tab="nanoid" onclick="setType('nanoid')" class="rounded-xl px-4 py-2 text-sm font-semibold transition">NanoID</button>
            <button data-tab="ulid" onclick="setType('ulid')" class="rounded-xl px-4 py-2 text-sm font-semibold transition">ULID</button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-[auto_auto_1fr_auto] sm:items-end">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Count</label>
                <input type="number" id="count" value="10" min="1" max="1000" class="w-28 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-lime-400/60 focus:outline-none">
            </div>
            <div id="sizeWrap" class="hidden">
                <label class="mb-2 block text-sm font-semibold text-slate-200">NanoID length</label>
                <input type="number" id="size" value="21" min="4" max="64" class="w-28 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-lime-400/60 focus:outline-none">
            </div>
            <label class="flex cursor-pointer items-center gap-2 self-end rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-slate-200"><input type="checkbox" id="upper" onchange="generate()" class="h-4 w-4 accent-lime-500"> Uppercase</label>
            <button onclick="generate()" class="rounded-xl bg-gradient-to-r from-lime-500 to-green-500 px-6 py-2.5 font-semibold text-slate-900 shadow-lg shadow-green-500/25 transition hover:scale-[1.02] active:scale-95">Generate</button>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-200">Results</span>
            <button onclick="copyAll()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy all</button>
        </div>
        <div id="list" class="mt-3 max-h-[420px] space-y-2 overflow-y-auto"></div>
    </div>
@endsection

@push('scripts')
<script>
    let type = 'uuid';
    const NANO = 'useandom-26T198340PX75pxJACKVERYMINDBUSHWOLF_GQZbfghjklqvwyzrict';
    const CROCKFORD = '0123456789ABCDEFGHJKMNPQRSTVWXYZ';

    function uuidv4() { return crypto.randomUUID ? crypto.randomUUID() : manualUuid(); }
    function manualUuid() { const b = crypto.getRandomValues(new Uint8Array(16)); b[6] = (b[6] & 15) | 64; b[8] = (b[8] & 63) | 128; const h = [...b].map((x) => x.toString(16).padStart(2, '0')); return `${h.slice(0,4).join('')}-${h.slice(4,6).join('')}-${h.slice(6,8).join('')}-${h.slice(8,10).join('')}-${h.slice(10).join('')}`; }
    function uuidv1() {
        // Simplified time-based UUID (not fully RFC but well-formed v1 layout).
        const now = Date.now();
        const t = (now * 10000 + 122192928000000000).toString(16).padStart(16, '0');
        const rnd = crypto.getRandomValues(new Uint8Array(8));
        const clock = ((rnd[0] & 0x3f) | 0x80).toString(16).padStart(2, '0') + rnd[1].toString(16).padStart(2, '0');
        const node = [...rnd.slice(2)].map((x) => x.toString(16).padStart(2, '0')).join('');
        return `${t.slice(8)}-${t.slice(4,8)}-1${t.slice(1,4)}-${clock}-${node.slice(0,12)}`;
    }
    function nanoid(size) { const bytes = crypto.getRandomValues(new Uint8Array(size)); let id = ''; for (let i = 0; i < size; i++) id += NANO[bytes[i] & 63]; return id; }
    function ulid() {
        let time = Date.now(); let out = '';
        for (let i = 9; i >= 0; i--) { out = CROCKFORD[time % 32] + out; time = Math.floor(time / 32); }
        const rnd = crypto.getRandomValues(new Uint8Array(16));
        for (let i = 0; i < 16; i++) out += CROCKFORD[rnd[i] & 31];
        return out;
    }

    function setType(t) {
        type = t;
        document.querySelectorAll('[data-tab]').forEach((b) => {
            const active = b.dataset.tab === t;
            b.className = `rounded-xl px-4 py-2 text-sm font-semibold transition ${active ? 'bg-gradient-to-r from-lime-500 to-green-500 text-slate-900 shadow' : 'text-slate-400'}`;
        });
        document.getElementById('sizeWrap').classList.toggle('hidden', t !== 'nanoid');
        generate();
    }

    function makeOne() {
        if (type === 'uuid') return uuidv4();
        if (type === 'uuidv1') return uuidv1();
        if (type === 'nanoid') return nanoid(parseInt(document.getElementById('size').value) || 21);
        return ulid();
    }

    function generate() {
        const count = Math.min(1000, Math.max(1, parseInt(document.getElementById('count').value) || 1));
        const upper = document.getElementById('upper').checked;
        const ids = Array.from({ length: count }, () => { let id = makeOne(); return upper ? id.toUpperCase() : id; });
        document.getElementById('list').innerHTML = ids.map((id) => `
            <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5">
                <code class="min-w-0 flex-1 truncate font-mono text-sm text-lime-200">${id}</code>
                <button onclick="copyToClipboard('${id}', 'ID copied')" class="flex-shrink-0 rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            </div>`).join('');
    }

    function copyAll() {
        const ids = [...document.querySelectorAll('#list code')].map((c) => c.textContent);
        if (!ids.length) return showNotification('Generate some IDs first.', 'error');
        copyToClipboard(ids.join('\n'), `${ids.length} IDs copied`);
    }

    document.addEventListener('DOMContentLoaded', () => setType('uuid'));
</script>
@endpush
