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
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-[1fr_auto_auto] sm:items-end">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">How many?</label>
                <input type="number" id="count" value="5" min="1" max="500"
                    class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white transition focus:border-lime-400/60 focus:outline-none focus:ring-2 focus:ring-lime-500/30">
            </div>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-medium text-slate-200 transition hover:bg-white/10">
                <input type="checkbox" id="upper" onchange="generate()" class="h-4 w-4 accent-lime-500">
                Uppercase
            </label>
            <button onclick="generate()" class="rounded-xl bg-gradient-to-r from-lime-500 to-green-500 px-6 py-2.5 font-semibold text-slate-900 shadow-lg shadow-green-500/25 transition hover:scale-[1.02] active:scale-95">Generate</button>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-200">Results</span>
            <button onclick="copyAll()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy all</button>
        </div>
        <div id="list" class="mt-3 space-y-2"></div>
    </div>
@endsection

@push('scripts')
<script>
    function uuidv4() {
        if (crypto.randomUUID) return crypto.randomUUID();
        const b = crypto.getRandomValues(new Uint8Array(16));
        b[6] = (b[6] & 0x0f) | 0x40;
        b[8] = (b[8] & 0x3f) | 0x80;
        const h = [...b].map((x) => x.toString(16).padStart(2, '0'));
        return `${h[0]}${h[1]}${h[2]}${h[3]}-${h[4]}${h[5]}-${h[6]}${h[7]}-${h[8]}${h[9]}-${h[10]}${h[11]}${h[12]}${h[13]}${h[14]}${h[15]}`;
    }

    function generate() {
        const count = Math.min(500, Math.max(1, parseInt(document.getElementById('count').value) || 1));
        const upper = document.getElementById('upper').checked;
        const items = Array.from({ length: count }, () => {
            const id = upper ? uuidv4().toUpperCase() : uuidv4();
            return `
                <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5">
                    <code class="min-w-0 flex-1 truncate font-mono text-sm text-lime-200">${id}</code>
                    <button onclick="copyToClipboard('${id}', 'UUID copied')" class="flex-shrink-0 rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>`;
        });
        document.getElementById('list').innerHTML = items.join('');
    }

    function copyAll() {
        const ids = Array.from(document.querySelectorAll('#list code')).map((c) => c.textContent);
        if (!ids.length) return showNotification('Generate some UUIDs first.', 'error');
        copyToClipboard(ids.join('\n'), `${ids.length} UUIDs copied`);
    }

    document.addEventListener('DOMContentLoaded', generate);
</script>
@endpush
