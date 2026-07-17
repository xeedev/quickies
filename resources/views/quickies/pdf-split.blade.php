@extends('layouts.app')

@section('title', 'PDF Split')
@section('description', 'Extract, remove or split pages from a PDF.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
@endpush

@section('content')
    <x-tool-header title="PDF Split" subtitle="Extract a page range, remove pages, or split every page into its own file."
        from="from-red-500" to="to-orange-500" icon="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5m4.75-11.396a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div id="dropZone" class="cursor-pointer rounded-2xl border-2 border-dashed border-orange-400/40 bg-white/5 px-6 py-10 text-center transition hover:border-orange-400 hover:bg-white/10" onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" accept="application/pdf" class="hidden" onchange="handleFile(event)">
            <svg class="mx-auto mb-3 h-14 w-14 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <h3 class="text-lg font-bold text-white">Drop a PDF here</h3>
            <p class="mt-1 text-sm text-slate-400">or click to browse</p>
        </div>

        <div id="panel" class="mt-6 hidden">
            <div class="mb-4 flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm">
                <span class="truncate text-slate-300" id="fileName"></span>
                <span class="text-slate-400" id="pageCount"></span>
            </div>
            <div class="mb-4 inline-flex flex-wrap gap-1 rounded-2xl border border-white/10 bg-white/5 p-1">
                <button data-mode="extract" onclick="setMode('extract')" class="rounded-xl px-4 py-2 text-sm font-semibold transition">Extract range</button>
                <button data-mode="remove" onclick="setMode('remove')" class="rounded-xl px-4 py-2 text-sm font-semibold transition">Remove pages</button>
                <button data-mode="each" onclick="setMode('each')" class="rounded-xl px-4 py-2 text-sm font-semibold transition">Split every page</button>
            </div>
            <div id="rangeWrap">
                <label class="mb-2 block text-sm font-semibold text-slate-200">Pages <span class="font-normal text-slate-400">(e.g. 1-3, 5, 8-10)</span></label>
                <input id="range" value="1-3" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 font-mono text-white focus:border-orange-400/60 focus:outline-none">
            </div>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button onclick="run()" id="runBtn" class="flex-1 rounded-2xl bg-gradient-to-r from-red-500 to-orange-500 px-6 py-3 font-semibold text-white shadow-lg shadow-orange-500/25 transition hover:scale-[1.01] active:scale-95">Process &amp; download</button>
                <button onclick="reset()" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-slate-200 transition hover:bg-white/10">Choose another</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let bytes = null, name = 'document', total = 0, mode = 'extract';

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-orange-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-orange-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-orange-400', 'bg-white/10'); if (e.dataTransfer.files.length) loadFile(e.dataTransfer.files[0]); });

    function handleFile(e) { if (e.target.files.length) loadFile(e.target.files[0]); }
    async function loadFile(file) {
        if (file.type !== 'application/pdf') return showNotification('Please select a PDF.', 'error');
        name = file.name.replace(/\.pdf$/i, '');
        bytes = await file.arrayBuffer();
        try {
            const { PDFDocument } = PDFLib;
            const doc = await PDFDocument.load(bytes, { ignoreEncryption: true });
            total = doc.getPageCount();
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('pageCount').textContent = `${total} pages`;
            document.getElementById('panel').classList.remove('hidden');
            setMode('extract');
        } catch (e) { showNotification('Could not read this PDF.', 'error'); }
    }

    function setMode(m) {
        mode = m;
        document.querySelectorAll('[data-mode]').forEach((b) => { const a = b.dataset.mode === m; b.className = `rounded-xl px-4 py-2 text-sm font-semibold transition ${a ? 'bg-gradient-to-r from-red-500 to-orange-500 text-white shadow' : 'text-slate-400'}`; });
        document.getElementById('rangeWrap').style.display = m === 'each' ? 'none' : 'block';
    }

    function parseRange(str, max) {
        const set = new Set();
        str.split(',').forEach((part) => {
            part = part.trim(); if (!part) return;
            if (part.includes('-')) { let [a, b] = part.split('-').map((n) => parseInt(n)); a = a || 1; b = b || max; for (let i = a; i <= b; i++) if (i >= 1 && i <= max) set.add(i - 1); }
            else { const n = parseInt(part); if (n >= 1 && n <= max) set.add(n - 1); }
        });
        return [...set].sort((a, b) => a - b);
    }

    function save(bytesOut, fname) {
        const blob = new Blob([bytesOut], { type: 'application/pdf' });
        const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = fname; a.click(); URL.revokeObjectURL(a.href);
    }

    async function run() {
        const btn = document.getElementById('runBtn'); btn.disabled = true;
        try {
            const { PDFDocument } = PDFLib;
            const src = await PDFDocument.load(bytes, { ignoreEncryption: true });
            if (mode === 'each') {
                for (let i = 0; i < total; i++) {
                    const out = await PDFDocument.create();
                    const [p] = await out.copyPages(src, [i]); out.addPage(p);
                    save(await out.save(), `${name}-page-${i + 1}.pdf`);
                    await new Promise((r) => setTimeout(r, 120));
                }
                showNotification(`Split into ${total} files!`, 'success');
            } else {
                let indices = parseRange(document.getElementById('range').value, total);
                if (mode === 'remove') { const rm = new Set(indices); indices = [...Array(total).keys()].filter((i) => !rm.has(i)); }
                if (!indices.length) { showNotification('No pages selected.', 'error'); btn.disabled = false; return; }
                const out = await PDFDocument.create();
                const pages = await out.copyPages(src, indices); pages.forEach((p) => out.addPage(p));
                save(await out.save(), `${name}-${mode === 'remove' ? 'trimmed' : 'extract'}.pdf`);
                showNotification('PDF downloaded!', 'success');
            }
        } catch (e) { console.error(e); showNotification('Processing failed.', 'error'); }
        finally { btn.disabled = false; }
    }
    function reset() { bytes = null; document.getElementById('panel').classList.add('hidden'); document.getElementById('fileInput').value = ''; }
</script>
@endpush
