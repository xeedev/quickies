@extends('layouts.app')

@section('title', 'Slug Generator')
@section('description', 'Create clean URL-friendly slugs from any text.')

@section('content')
    <x-tool-header title="Slug Generator" subtitle="Turn titles into clean, URL-friendly slugs."
        from="from-lime-500" to="to-green-500" icon="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">Text</label>
        <textarea id="input" rows="4" oninput="run()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-lime-400/60 focus:outline-none focus:ring-2 focus:ring-lime-500/30" placeholder="My Awesome Blog Post Title!">My Awesome Blog Post Title!</textarea>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Separator</label>
                <select id="sep" onchange="run()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-lime-400/60 focus:outline-none">
                    <option value="-" class="bg-slate-900">Hyphen ( - )</option>
                    <option value="_" class="bg-slate-900">Underscore ( _ )</option>
                    <option value="." class="bg-slate-900">Dot ( . )</option>
                </select>
            </div>
            <label class="flex cursor-pointer items-center gap-2 self-end rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="lower" checked onchange="run()" class="h-4 w-4 accent-lime-500"> Lowercase</label>
            <label class="flex cursor-pointer items-center gap-2 self-end rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="strip" checked onchange="run()" class="h-4 w-4 accent-lime-500"> Strip stop-words</label>
        </div>

        <div class="mt-6 flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/50 p-4">
            <code id="output" class="min-w-0 flex-1 break-all font-mono text-lg text-lime-200"></code>
            <button onclick="copyToClipboard(document.getElementById('output').textContent, 'Slug copied')" class="flex-shrink-0 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const STOP = new Set(['a','an','the','and','or','but','of','to','in','on','for','with','at','by','from','is','are']);
    function run() {
        let s = document.getElementById('input').value;
        const sep = document.getElementById('sep').value;
        const lower = document.getElementById('lower').checked;
        const strip = document.getElementById('strip').checked;
        s = s.normalize('NFKD').replace(/[\u0300-\u036f]/g, '');
        if (lower) s = s.toLowerCase();
        let words = s.replace(/[^a-zA-Z0-9\s-]/g, ' ').split(/\s+/).filter(Boolean);
        if (strip) words = words.filter((w) => !STOP.has(w.toLowerCase()));
        document.getElementById('output').textContent = words.join(sep).replace(new RegExp(`\\${sep}+`, 'g'), sep) || '—';
    }
    document.addEventListener('DOMContentLoaded', run);
</script>
@endpush
