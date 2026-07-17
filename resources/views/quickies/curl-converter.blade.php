@extends('layouts.app')

@section('title', 'cURL Converter')
@section('description', 'Convert cURL commands into fetch and axios code.')

@section('content')
    <x-tool-header title="cURL Converter" subtitle="Paste a cURL command and get ready-to-use fetch or axios code."
        from="from-sky-500" to="to-cyan-500" icon="M4 7l4-4m0 0l4 4M8 3v13m8 5l-4-4m0 0l-4 4m4-4V8" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <label class="mb-2 block text-sm font-semibold text-slate-200">cURL command</label>
        <textarea id="input" rows="6" spellcheck="false" oninput="convert()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-cyan-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/30" placeholder="curl -X POST https://api.example.com/users -H 'Content-Type: application/json' -d '{&quot;name&quot;:&quot;Ada&quot;}'"></textarea>

        <div class="mt-4 inline-flex rounded-2xl border border-white/10 bg-white/5 p-1">
            <button id="tabFetch" onclick="setTab('fetch')" class="rounded-xl px-5 py-2 text-sm font-semibold transition">fetch</button>
            <button id="tabAxios" onclick="setTab('axios')" class="rounded-xl px-5 py-2 text-sm font-semibold transition">axios</button>
            <button id="tabNode" onclick="setTab('node')" class="rounded-xl px-5 py-2 text-sm font-semibold transition">node https</button>
        </div>
        <div class="mt-3">
            <div class="mb-2 flex items-center justify-between">
                <span id="err" class="text-sm font-semibold text-rose-300"></span>
                <button onclick="copyToClipboard(document.getElementById('output').textContent, 'Code copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
            </div>
            <pre id="output" class="overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/50 p-4 font-mono text-sm text-cyan-100"></pre>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let tab = 'fetch';
    let parsed = null;

    function tokenize(cmd) {
        const tokens = []; let cur = ''; let quote = null;
        for (let i = 0; i < cmd.length; i++) {
            const c = cmd[i];
            if (quote) { if (c === quote) quote = null; else cur += c; }
            else if (c === '"' || c === "'") quote = c;
            else if (/\s/.test(c)) { if (cur) { tokens.push(cur); cur = ''; } }
            else if (c === '\\' && cmd[i + 1] === '\n') i++;
            else cur += c;
        }
        if (cur) tokens.push(cur);
        return tokens;
    }

    function parseCurl(cmd) {
        cmd = cmd.trim().replace(/\\\n/g, ' ');
        const tokens = tokenize(cmd);
        if (!tokens.length || tokens[0] !== 'curl') throw new Error('Command must start with "curl".');
        const res = { method: null, url: null, headers: {}, body: null };
        for (let i = 1; i < tokens.length; i++) {
            const t = tokens[i];
            if (t === '-X' || t === '--request') res.method = tokens[++i];
            else if (t === '-H' || t === '--header') { const h = tokens[++i]; const idx = h.indexOf(':'); if (idx > -1) res.headers[h.slice(0, idx).trim()] = h.slice(idx + 1).trim(); }
            else if (t === '-d' || t === '--data' || t === '--data-raw' || t === '--data-binary') res.body = tokens[++i];
            else if (t === '-u' || t === '--user') res.headers['Authorization'] = 'Basic ' + btoa(tokens[++i]);
            else if (t === '--compressed' || t === '-L' || t === '--location' || t === '-s' || t === '--silent' || t === '-k' || t === '--insecure') { /* ignore */ }
            else if (!t.startsWith('-') && !res.url) res.url = t;
        }
        if (!res.url) throw new Error('No URL found in command.');
        if (!res.method) res.method = res.body ? 'POST' : 'GET';
        return res;
    }

    function toFetch(p) {
        const opts = { method: p.method };
        if (Object.keys(p.headers).length) opts.headers = p.headers;
        if (p.body) opts.body = p.body;
        const optsStr = JSON.stringify(opts, null, 2).replace(/"([^"]+)":/g, '$1:');
        return `fetch(${JSON.stringify(p.url)}, ${optsStr})\n  .then(res => res.json())\n  .then(data => console.log(data))\n  .catch(err => console.error(err));`;
    }
    function toAxios(p) {
        const cfg = { method: p.method.toLowerCase(), url: p.url };
        if (Object.keys(p.headers).length) cfg.headers = p.headers;
        if (p.body) { try { cfg.data = JSON.parse(p.body); } catch (e) { cfg.data = p.body; } }
        const cfgStr = JSON.stringify(cfg, null, 2).replace(/"([^"]+)":/g, '$1:');
        return `import axios from 'axios';\n\naxios(${cfgStr})\n  .then(({ data }) => console.log(data))\n  .catch(err => console.error(err));`;
    }
    function toNode(p) {
        return `const https = require('https');\nconst url = new URL(${JSON.stringify(p.url)});\nconst req = https.request(url, {\n  method: ${JSON.stringify(p.method)},\n  headers: ${JSON.stringify(p.headers, null, 2).replace(/\n/g, '\n  ')}\n}, res => {\n  let data = '';\n  res.on('data', c => data += c);\n  res.on('end', () => console.log(data));\n});\n${p.body ? `req.write(${JSON.stringify(p.body)});\n` : ''}req.end();`;
    }

    function setTab(t) {
        tab = t;
        ['fetch', 'axios', 'node'].forEach((n) => {
            document.getElementById('tab' + n.charAt(0).toUpperCase() + n.slice(1)).className = `rounded-xl px-5 py-2 text-sm font-semibold transition ${n === t ? 'bg-gradient-to-r from-sky-500 to-cyan-500 text-white shadow' : 'text-slate-400'}`;
        });
        render();
    }

    function convert() {
        try { parsed = parseCurl(document.getElementById('input').value); document.getElementById('err').textContent = ''; }
        catch (e) { parsed = null; document.getElementById('err').textContent = e.message; document.getElementById('output').textContent = ''; return; }
        render();
    }
    function render() {
        if (!parsed) return;
        document.getElementById('output').textContent = tab === 'fetch' ? toFetch(parsed) : tab === 'axios' ? toAxios(parsed) : toNode(parsed);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('input').value = `curl -X POST https://api.example.com/users -H 'Content-Type: application/json' -H 'Authorization: Bearer token123' -d '{"name":"Ada","role":"admin"}'`;
        setTab('fetch');
        convert();
    });
</script>
@endpush
