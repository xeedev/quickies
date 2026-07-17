@extends('layouts.app')

@section('title', 'Docker Compose Validator')
@section('description', 'Validate docker-compose YAML structure and services.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/js-yaml@4.1.0/dist/js-yaml.min.js"></script>
@endpush

@section('content')
    <x-tool-header title="Docker Compose Validator" subtitle="Check docker-compose YAML syntax and structure."
        from="from-blue-500" to="to-sky-500" icon="M5 13h4v4H5v-4zm5 0h4v4h-4v-4zm5 0h4v4h-4v-4zM10 8h4v4h-4V8zm5 0h4v4h-4V8zM3 21c4 0 6-2 6-2" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-4 flex items-center gap-3">
            <button onclick="validate()" class="rounded-xl bg-gradient-to-r from-blue-500 to-sky-500 px-4 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Validate</button>
        </div>
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">docker-compose.yml</label>
                <textarea id="input" rows="18" spellcheck="false" oninput="validate()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-sky-400/60 focus:outline-none focus:ring-2 focus:ring-sky-500/30"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-slate-200">Report</label>
                <div id="report" class="min-h-[200px] space-y-2 rounded-2xl border border-white/10 bg-slate-950/50 p-4 text-sm"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const KNOWN_SERVICE_KEYS = ['image','build','command','entrypoint','environment','env_file','ports','volumes','networks','depends_on','restart','container_name','labels','healthcheck','deploy','expose','links','extra_hosts','working_dir','user','logging','profiles','platform','cap_add','cap_drop','devices','dns','tmpfs','ulimits','stdin_open','tty','hostname','domainname','mem_limit','cpus'];

    function item(kind, msg) {
        const c = { error: 'text-rose-200 bg-rose-500/10 border-rose-400/20', warn: 'text-amber-200 bg-amber-500/10 border-amber-400/20', ok: 'text-emerald-200 bg-emerald-500/10 border-emerald-400/20', info: 'text-slate-300 bg-white/5 border-white/10' }[kind];
        const icon = { error: '✕', warn: '!', ok: '✓', info: 'i' }[kind];
        return `<div class="flex items-start gap-2 rounded-xl border px-3 py-2 ${c}"><span class="font-bold">${icon}</span><span>${msg}</span></div>`;
    }

    function validate() {
        const report = document.getElementById('report');
        const text = document.getElementById('input').value;
        if (!text.trim()) { report.innerHTML = item('info', 'Paste a compose file to validate.'); return; }
        let doc;
        try { doc = jsyaml.load(text); }
        catch (e) { report.innerHTML = item('error', 'YAML syntax error: ' + e.message.replace(/&/g, '&amp;').replace(/</g, '&lt;')); return; }

        const out = [];
        if (doc === null || typeof doc !== 'object' || Array.isArray(doc)) { report.innerHTML = item('error', 'Top level must be a mapping (object).'); return; }

        out.push(item('ok', 'Valid YAML syntax.'));
        if (doc.version) out.push(item('info', `Compose version: <span class="font-mono">${doc.version}</span> (optional in the modern spec).`));

        if (!doc.services || typeof doc.services !== 'object') {
            out.push(item('error', 'Missing required top-level <span class="font-mono">services</span> mapping.'));
            report.innerHTML = out.join(''); return;
        }

        const names = Object.keys(doc.services);
        out.push(item('ok', `Found ${names.length} service${names.length === 1 ? '' : 's'}: ${names.map((n) => `<span class="font-mono">${n}</span>`).join(', ')}`));

        names.forEach((name) => {
            const svc = doc.services[name];
            if (!svc || typeof svc !== 'object') { out.push(item('error', `Service <span class="font-mono">${name}</span> must be a mapping.`)); return; }
            if (!svc.image && !svc.build) out.push(item('error', `Service <span class="font-mono">${name}</span> needs an <span class="font-mono">image</span> or <span class="font-mono">build</span>.`));
            Object.keys(svc).forEach((k) => { if (!KNOWN_SERVICE_KEYS.includes(k)) out.push(item('warn', `Service <span class="font-mono">${name}</span>: unknown key <span class="font-mono">${k}</span>.`)); });
            if (svc.depends_on) {
                const deps = Array.isArray(svc.depends_on) ? svc.depends_on : Object.keys(svc.depends_on);
                deps.forEach((d) => { if (!names.includes(d)) out.push(item('error', `Service <span class="font-mono">${name}</span> depends on <span class="font-mono">${d}</span> which is not defined.`)); });
            }
            if (svc.ports && Array.isArray(svc.ports)) {
                svc.ports.forEach((p) => { if (typeof p === 'string' && !/^\d+(:\d+)?(\/(tcp|udp))?$|^\d+\.\d+/.test(p) && !/:/.test(p)) out.push(item('warn', `Service <span class="font-mono">${name}</span>: port "${p}" looks unusual.`)); });
            }
        });

        const errors = out.filter((o) => o.includes('rose')).length;
        out.unshift(item(errors ? 'error' : 'ok', errors ? `${errors} issue${errors === 1 ? '' : 's'} found.` : 'Compose file looks valid. 🎉'));
        report.innerHTML = out.join('');
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('input').value = `services:
  web:
    image: nginx:alpine
    ports:
      - "8080:80"
    depends_on:
      - api
  api:
    build: ./api
    environment:
      - NODE_ENV=production
    restart: unless-stopped`;
        validate();
    });
</script>
@endpush
