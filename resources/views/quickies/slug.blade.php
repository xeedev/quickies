@extends('layouts.app')

@section('title', 'Slug Generator')
@section('description', 'Create clean URL-friendly slugs from any text.')

@section('content')
    <x-tool-header title="Slug Generator" subtitle="Turn titles into clean, URL-friendly slugs."
        from="from-lime-500" to="to-green-500" icon="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">Text <span class="text-slate-500">— one slug per line</span></label>
        <textarea id="input" rows="4" oninput="run()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-lime-400/60 focus:outline-none focus:ring-2 focus:ring-lime-500/30" placeholder="My Awesome Blog Post Title!">My Awesome Blog Post Title!</textarea>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Separator</label>
                <select id="sep" onchange="run()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-lime-400/60 focus:outline-none">
                    <option value="-" class="bg-slate-900">Hyphen ( - )</option>
                    <option value="_" class="bg-slate-900">Underscore ( _ )</option>
                    <option value="." class="bg-slate-900">Dot ( . )</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Max length <span class="text-slate-500">(0 = none)</span></label>
                <input type="number" id="maxlen" value="0" min="0" max="200" oninput="run()" class="w-full rounded-xl border border-white/10 bg-white/5 px-3 py-2.5 text-sm text-white focus:border-lime-400/60 focus:outline-none">
            </div>
            <label class="flex cursor-pointer items-center gap-2 self-end rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="lower" checked onchange="run()" class="h-4 w-4 accent-lime-500"> Lowercase</label>
            <label class="flex cursor-pointer items-center gap-2 self-end rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-slate-200 transition hover:bg-white/10"><input type="checkbox" id="strip" checked onchange="run()" class="h-4 w-4 accent-lime-500"> Strip stop-words</label>
        </div>

        {{-- Single-line result --}}
        <div id="single" class="mt-6">
            <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                <code id="output" class="min-w-0 flex-1 break-all font-mono text-lg text-lime-200"></code>
                <button onclick="copyToClipboard(document.getElementById('output').textContent, 'Slug copied')" class="flex-shrink-0 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            </div>
            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                <span id="charCount"></span>
                <span class="truncate">Preview: <span class="font-mono text-slate-400">https://example.com/</span><span id="previewSlug" class="font-mono text-lime-300"></span></span>
            </div>
        </div>

        {{-- Multi-line results --}}
        <div id="multi" class="mt-6 hidden">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-200">Slugs <span id="multiCount" class="text-slate-500"></span></span>
                <button onclick="copyAllSlugs()" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy all</button>
            </div>
            <div id="multiList" class="max-h-[360px] space-y-2 overflow-y-auto"></div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const STOP = new Set(['a','an','the','and','or','but','of','to','in','on','for','with','at','by','from','is','are']);

    function slugify(text) {
        const sep = document.getElementById('sep').value;
        const lower = document.getElementById('lower').checked;
        const strip = document.getElementById('strip').checked;
        const maxlen = Math.max(0, parseInt(document.getElementById('maxlen').value) || 0);
        let s = text.normalize('NFKD').replace(/[\u0300-\u036f]/g, '');
        if (lower) s = s.toLowerCase();
        let words = s.replace(/[^a-zA-Z0-9\s-]/g, ' ').split(/\s+/).filter(Boolean);
        if (strip && words.length > 1) words = words.filter((w) => !STOP.has(w.toLowerCase()));
        let slug = words.join(sep).replace(new RegExp(`\\${sep}+`, 'g'), sep);
        if (maxlen > 0 && slug.length > maxlen) {
            slug = slug.slice(0, maxlen);
            const cut = slug.lastIndexOf(sep);
            if (cut > 0) slug = slug.slice(0, cut); // don't end mid-word
        }
        return slug;
    }

    function run() {
        const lines = document.getElementById('input').value.split('\n').map((l) => l.trim()).filter(Boolean);
        const single = document.getElementById('single');
        const multi = document.getElementById('multi');

        if (lines.length <= 1) {
            single.classList.remove('hidden');
            multi.classList.add('hidden');
            const slug = slugify(lines[0] || '') || '—';
            document.getElementById('output').textContent = slug;
            document.getElementById('previewSlug').textContent = slug === '—' ? '' : slug;
            document.getElementById('charCount').textContent = `${slug === '—' ? 0 : slug.length} characters`;
        } else {
            single.classList.add('hidden');
            multi.classList.remove('hidden');
            const slugs = lines.map(slugify).filter(Boolean);
            document.getElementById('multiCount').textContent = `· ${slugs.length}`;
            document.getElementById('multiList').innerHTML = slugs.map((s) => {
                const safe = s.replace(/'/g, "\\'");
                return `<div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5">
                    <code class="min-w-0 flex-1 truncate font-mono text-sm text-lime-200">${s}</code>
                    <button onclick="copyToClipboard('${safe}', 'Slug copied')" class="flex-shrink-0 rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>`;
            }).join('');
        }
    }

    function copyAllSlugs() {
        const slugs = Array.from(document.querySelectorAll('#multiList code')).map((c) => c.textContent);
        if (!slugs.length) return showNotification('Nothing to copy.', 'error');
        copyToClipboard(slugs.join('\n'), `${slugs.length} slugs copied`);
    }

    document.addEventListener('DOMContentLoaded', run);
</script>
@endpush
