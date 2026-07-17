@extends('layouts.app')

@section('title', 'PDF Merge')
@section('description', 'Combine several PDF files into one document.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
@endpush

@section('content')
    <x-tool-header title="PDF Merge" subtitle="Combine multiple PDFs into a single file — reorder before merging."
        from="from-rose-500" to="to-red-500" icon="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div id="dropZone" class="cursor-pointer rounded-2xl border-2 border-dashed border-rose-400/40 bg-white/5 px-6 py-10 text-center transition hover:border-rose-400 hover:bg-white/10" onclick="document.getElementById('fileInput').click()">
            <input type="file" id="fileInput" accept="application/pdf" multiple class="hidden" onchange="handleFiles(event)">
            <svg class="mx-auto mb-3 h-14 w-14 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <h3 class="text-lg font-bold text-white">Drop PDF files here</h3>
            <p class="mt-1 text-sm text-slate-400">or click to browse — add two or more</p>
        </div>

        <div id="panel" class="mt-6 hidden">
            <h3 class="mb-3 text-sm font-bold uppercase tracking-wide text-slate-300"><span id="count">0</span> files · drag to reorder</h3>
            <div id="list" class="space-y-2"></div>
            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button onclick="merge()" id="mergeBtn" class="flex-1 rounded-2xl bg-gradient-to-r from-rose-500 to-red-500 px-6 py-3 font-semibold text-white shadow-lg shadow-rose-500/25 transition hover:scale-[1.01] active:scale-95">Merge &amp; download</button>
                <button onclick="reset()" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-slate-200 transition hover:bg-white/10">Clear</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let files = [];
    let dragged = null;

    const dropZone = document.getElementById('dropZone');
    ['dragover', 'dragenter'].forEach((ev) => dropZone.addEventListener(ev, (e) => { e.preventDefault(); dropZone.classList.add('border-rose-400', 'bg-white/10'); }));
    ['dragleave', 'dragend'].forEach((ev) => dropZone.addEventListener(ev, () => dropZone.classList.remove('border-rose-400', 'bg-white/10')));
    dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.classList.remove('border-rose-400', 'bg-white/10'); addFiles(Array.from(e.dataTransfer.files)); });

    function handleFiles(e) { addFiles(Array.from(e.target.files)); e.target.value = ''; }
    function addFiles(list) {
        const pdfs = list.filter((f) => f.type === 'application/pdf');
        if (!pdfs.length) return showNotification('Please add PDF files.', 'error');
        files = files.concat(pdfs);
        document.getElementById('panel').classList.remove('hidden');
        render();
    }

    function render() {
        document.getElementById('count').textContent = files.length;
        document.getElementById('list').innerHTML = files.map((f, i) => `
            <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-3" draggable="true" data-i="${i}">
                <svg class="h-5 w-5 flex-shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                <span class="min-w-0 flex-1 truncate text-sm text-white">${i + 1}. ${f.name.replace(/</g,'&lt;')}</span>
                <span class="flex-shrink-0 text-xs text-slate-500">${(f.size/1024).toFixed(0)} KB</span>
                <button onclick="remove(${i})" class="flex-shrink-0 rounded p-1 text-rose-300 transition hover:bg-rose-500/20">✕</button>
            </div>`).join('');
        // drag reorder
        document.querySelectorAll('#list [draggable]').forEach((el) => {
            el.addEventListener('dragstart', () => { dragged = +el.dataset.i; el.classList.add('opacity-40'); });
            el.addEventListener('dragend', () => el.classList.remove('opacity-40'));
            el.addEventListener('dragover', (e) => e.preventDefault());
            el.addEventListener('drop', (e) => { e.preventDefault(); const to = +el.dataset.i; const [m] = files.splice(dragged, 1); files.splice(to, 0, m); render(); });
        });
    }
    function remove(i) { files.splice(i, 1); render(); if (!files.length) reset(); }
    function reset() { files = []; document.getElementById('panel').classList.add('hidden'); document.getElementById('list').innerHTML = ''; }

    async function merge() {
        if (files.length < 2) return showNotification('Add at least two PDFs.', 'error');
        const btn = document.getElementById('mergeBtn'); btn.disabled = true;
        showNotification('Merging…', 'info');
        try {
            const { PDFDocument } = PDFLib;
            const out = await PDFDocument.create();
            for (const file of files) {
                const bytes = await file.arrayBuffer();
                const src = await PDFDocument.load(bytes, { ignoreEncryption: true });
                const pages = await out.copyPages(src, src.getPageIndices());
                pages.forEach((p) => out.addPage(p));
            }
            const blob = new Blob([await out.save()], { type: 'application/pdf' });
            const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'merged.pdf'; a.click(); URL.revokeObjectURL(a.href);
            showNotification('Merged PDF downloaded!', 'success');
        } catch (e) { console.error(e); showNotification('Merge failed — a file may be encrypted or corrupt.', 'error'); }
        finally { btn.disabled = false; }
    }
</script>
@endpush
