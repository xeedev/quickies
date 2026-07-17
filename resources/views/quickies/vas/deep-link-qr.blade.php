@extends('layouts.app')

@section('title', 'Deep Link & QR Generator')
@section('description', 'Build deep links with UTM tags and matching QR codes.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
@endpush

@section('content')
    <x-tool-header title="Deep Link & QR Generator" subtitle="Build app deep links with UTM tracking and a matching QR code."
        from="from-blue-500" to="to-cyan-500" icon="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 4v-4m0 4h2m4 0v-4m0 0h-4m4 0h.01M14 14h.01" />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_320px]">
        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-200">Scheme / base</label>
                    <input id="scheme" oninput="build()" value="myapp://" placeholder="myapp:// or https://" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 font-mono text-sm text-white focus:border-cyan-400/60 focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-semibold text-slate-200">Path</label>
                    <input id="path" oninput="build()" value="product/123" placeholder="product/123" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 font-mono text-sm text-white focus:border-cyan-400/60 focus:outline-none">
                </div>
                <div class="sm:col-span-2">
                    <label class="mb-1 block text-sm font-semibold text-slate-200">Web fallback URL <span class="font-normal text-slate-400">(optional)</span></label>
                    <input id="fallback" oninput="build()" placeholder="https://example.com/product/123" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 font-mono text-sm text-white focus:border-cyan-400/60 focus:outline-none">
                </div>
                @foreach (['source' => 'push', 'medium' => 'app', 'campaign' => 'relaunch'] as $k => $ph)
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-slate-200">utm_{{ $k }}</label>
                        <input id="utm_{{ $k }}" oninput="build()" placeholder="{{ $ph }}" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-cyan-400/60 focus:outline-none">
                    </div>
                @endforeach
            </div>

            <div class="mt-5 space-y-3">
                <div>
                    <div class="mb-1 flex items-center justify-between"><span class="text-sm font-semibold text-slate-200">Deep link</span><button onclick="copyToClipboard(document.getElementById('deep').textContent, 'Deep link copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button></div>
                    <div id="deep" class="break-all rounded-xl border border-white/10 bg-slate-950/50 p-3 font-mono text-sm text-cyan-200"></div>
                </div>
                <div id="fallbackWrap" class="hidden">
                    <div class="mb-1 flex items-center justify-between"><span class="text-sm font-semibold text-slate-200">Web fallback</span><button onclick="copyToClipboard(document.getElementById('web').textContent, 'URL copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button></div>
                    <div id="web" class="break-all rounded-xl border border-white/10 bg-slate-950/50 p-3 font-mono text-sm text-blue-200"></div>
                </div>
            </div>
        </div>

        <div class="flex flex-col rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl">
            <div class="flex flex-1 items-center justify-center">
                <div id="qr" class="inline-block rounded-2xl bg-white p-3 shadow-2xl"></div>
            </div>
            <div class="mt-4 flex gap-2">
                <label class="flex flex-1 items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-2 py-1.5 text-xs text-slate-300">QR encodes
                    <select id="qrTarget" onchange="renderQR()" class="flex-1 rounded bg-slate-900 px-1 py-1 text-white focus:outline-none"><option value="deep" class="bg-slate-900">Deep link</option><option value="web" class="bg-slate-900">Web fallback</option></select>
                </label>
                <button onclick="downloadQR()" class="rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:scale-105">PNG</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const val = (id) => document.getElementById(id).value.trim();
    function utmQs() {
        return ['source', 'medium', 'campaign'].map((k) => { const v = val('utm_' + k); return v ? `utm_${k}=${encodeURIComponent(v)}` : null; }).filter(Boolean).join('&');
    }
    function build() {
        const scheme = val('scheme'), path = val('path'), fallback = val('fallback'), qs = utmQs();
        let deep = scheme + path.replace(/^\//, '');
        if (qs) deep += (deep.includes('?') ? '&' : '?') + qs;
        document.getElementById('deep').textContent = deep;
        const fw = document.getElementById('fallbackWrap');
        if (fallback) { let web = fallback; if (qs) web += (web.includes('?') ? '&' : '?') + qs; document.getElementById('web').textContent = web; fw.classList.remove('hidden'); }
        else fw.classList.add('hidden');
        renderQR();
    }
    function renderQR() {
        const target = document.getElementById('qrTarget').value;
        const text = document.getElementById(target === 'web' ? 'web' : 'deep').textContent || document.getElementById('deep').textContent;
        const c = document.getElementById('qr'); c.innerHTML = '';
        if (text) new QRCode(c, { text, width: 200, height: 200, colorDark: '#0f172a', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.M });
    }
    function downloadQR() {
        const canvas = document.querySelector('#qr canvas');
        if (!canvas) return showNotification('Nothing to download.', 'error');
        const a = document.createElement('a'); a.href = canvas.toDataURL('image/png'); a.download = 'deeplink-qr.png'; a.click();
        showNotification('QR downloaded!', 'success');
    }
    document.addEventListener('DOMContentLoaded', build);
</script>
@endpush
