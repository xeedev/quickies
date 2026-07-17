@extends('layouts.app')

@section('title', 'Mock JSON Generator')
@section('description', 'Generate realistic mock JSON data from a simple schema.')

@section('content')
    <x-tool-header title="Mock JSON Generator" subtitle="Describe fields and generate realistic fake JSON data."
        from="from-emerald-500" to="to-teal-500" icon="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-200">Schema</label>
                    <div class="flex items-center gap-2">
                        <label class="text-xs text-slate-400">Rows <input type="number" id="count" value="5" min="1" max="500" class="ml-1 w-16 rounded-lg border border-white/10 bg-white/5 px-2 py-1 text-white focus:outline-none"></label>
                        <button onclick="generate()" class="rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 px-4 py-1.5 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Generate</button>
                    </div>
                </div>
                <textarea id="schema" rows="14" spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 font-mono text-sm text-white transition focus:border-emerald-400/60 focus:outline-none focus:ring-2 focus:ring-emerald-500/30"></textarea>
                <p class="mt-2 text-xs text-slate-500">Types: <span class="font-mono">id, uuid, name, firstName, lastName, email, username, phone, city, country, company, jobTitle, sentence, paragraph, word, bool, int, float, price, date, datetime, color, url, avatar</span>. Use <span class="font-mono">int:1-100</span> for ranges.</p>
            </div>
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="text-sm font-semibold text-slate-200">Output</label>
                    <button onclick="copyToClipboard(document.getElementById('output').value, 'JSON copied')" class="rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <textarea id="output" rows="18" readonly spellcheck="false" class="w-full rounded-2xl border border-white/10 bg-slate-950/50 px-4 py-3 font-mono text-sm text-emerald-100 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const FIRST = ['Ada','Grace','Alan','Linus','Katherine','Margaret','Dennis','Ken','Barbara','Tim','Sofia','Mateo','Noah','Emma','Liam','Olivia','Aria','Kai'];
    const LAST = ['Lovelace','Hopper','Turing','Torvalds','Johnson','Hamilton','Ritchie','Thompson','Liskov','Berners-Lee','Nakamura','Silva','Khan','Patel','Costa'];
    const CITIES = ['London','Tokyo','Berlin','Austin','Toronto','Lisbon','Nairobi','Sydney','Oslo','Cairo'];
    const COUNTRIES = ['Portugal','Japan','Germany','Canada','Kenya','Norway','Brazil','India','Australia','Egypt'];
    const COMPANIES = ['Acme','Globex','Initech','Umbrella','Hooli','Stark Industries','Wayne Enterprises','Wonka'];
    const JOBS = ['Engineer','Designer','Product Manager','Analyst','Architect','DevOps Lead','Researcher'];
    const WORDS = 'lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt labore magna aliqua'.split(' ');
    const rand = (a) => a[Math.floor(Math.random() * a.length)];
    const int = (min, max) => Math.floor(Math.random() * (max - min + 1)) + min;

    function value(type) {
        let range = null;
        if (type.includes(':')) { const [t, r] = type.split(':'); type = t; range = r.split('-').map(Number); }
        switch (type) {
            case 'id': return int(1, 100000);
            case 'uuid': return crypto.randomUUID ? crypto.randomUUID() : '00000000-0000-4000-8000-000000000000';
            case 'firstName': return rand(FIRST);
            case 'lastName': return rand(LAST);
            case 'name': return `${rand(FIRST)} ${rand(LAST)}`;
            case 'username': return (rand(FIRST) + rand(LAST) + int(1, 99)).toLowerCase();
            case 'email': return `${rand(FIRST).toLowerCase()}.${rand(LAST).toLowerCase().replace(/[^a-z]/g,'')}@example.com`;
            case 'phone': return `+1 (${int(200,999)}) ${int(200,999)}-${String(int(0,9999)).padStart(4,'0')}`;
            case 'city': return rand(CITIES);
            case 'country': return rand(COUNTRIES);
            case 'company': return rand(COMPANIES);
            case 'jobTitle': return rand(JOBS);
            case 'word': return rand(WORDS);
            case 'sentence': { const n = int(5, 10); return (Array.from({length:n}, () => rand(WORDS)).join(' ').replace(/^./, (c) => c.toUpperCase())) + '.'; }
            case 'paragraph': { return Array.from({length: int(3,5)}, () => { const n = int(5,10); return Array.from({length:n}, () => rand(WORDS)).join(' ').replace(/^./,(c)=>c.toUpperCase()) + '.'; }).join(' '); }
            case 'bool': return Math.random() > 0.5;
            case 'int': return int(range ? range[0] : 0, range ? range[1] : 1000);
            case 'float': return +(Math.random() * (range ? range[1] : 100)).toFixed(2);
            case 'price': return +(int(range ? range[0] : 1, range ? range[1] : 500) + Math.random()).toFixed(2);
            case 'date': { const d = new Date(Date.now() - int(0, 3e10)); return d.toISOString().slice(0, 10); }
            case 'datetime': { const d = new Date(Date.now() - int(0, 3e10)); return d.toISOString(); }
            case 'color': return '#' + Math.floor(Math.random()*0xffffff).toString(16).padStart(6,'0');
            case 'url': return 'https://example.com/' + rand(WORDS);
            case 'avatar': return `https://i.pravatar.cc/150?u=${int(1,9999)}`;
            default: return null;
        }
    }

    function generate() {
        try {
            const schema = JSON.parse(document.getElementById('schema').value);
            const count = Math.min(500, Math.max(1, parseInt(document.getElementById('count').value) || 1));
            const rows = Array.from({ length: count }, () => {
                const obj = {};
                for (const [key, type] of Object.entries(schema)) obj[key] = value(String(type));
                return obj;
            });
            document.getElementById('output').value = JSON.stringify(rows, null, 2);
        } catch (e) {
            document.getElementById('output').value = '⚠ Schema must be valid JSON: ' + e.message;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('schema').value = '{\n  "id": "id",\n  "name": "name",\n  "email": "email",\n  "age": "int:18-65",\n  "salary": "price:40000-120000",\n  "active": "bool",\n  "joined": "date"\n}';
        generate();
    });
</script>
@endpush
