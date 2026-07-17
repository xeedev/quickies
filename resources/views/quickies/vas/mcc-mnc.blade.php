@extends('layouts.app')

@section('title', 'MCC / MNC Lookup')
@section('description', 'Look up mobile carriers by MCC/MNC and country.')

@section('content')
    <x-tool-header title="MCC / MNC Lookup" subtitle="Find mobile country and network codes for carriers worldwide."
        from="from-teal-500" to="to-cyan-500" icon="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="relative mb-5">
            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input id="search" oninput="render()" placeholder="Search by country, operator, MCC or MNC…" class="w-full rounded-2xl border border-white/10 bg-white/5 py-3 pl-12 pr-4 text-sm text-white placeholder-slate-500 focus:border-teal-400/60 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
        </div>
        <div id="list" class="overflow-x-auto"></div>
        <p class="mt-4 text-xs text-slate-500">A curated set of common operators. MCC identifies the country; MNC identifies the network.</p>
    </div>
@endsection

@push('scripts')
<script>
    const DATA = [
        ['310','260','United States','T-Mobile'],['310','410','United States','AT&T'],['311','480','United States','Verizon'],['310','120','United States','Sprint'],
        ['234','10','United Kingdom','O2'],['234','15','United Kingdom','Vodafone'],['234','20','United Kingdom','3'],['234','30','United Kingdom','EE'],
        ['262','1','Germany','Telekom'],['262','2','Germany','Vodafone'],['262','3','Germany','O2'],
        ['208','1','France','Orange'],['208','10','France','SFR'],['208','20','France','Bouygues'],['208','15','France','Free Mobile'],
        ['404','1','India','Vodafone Idea'],['404','45','India','Airtel'],['405','840','India','Jio'],['404','10','India','Airtel'],
        ['505','1','Australia','Telstra'],['505','2','Australia','Optus'],['505','3','Australia','Vodafone'],
        ['724','2','Brazil','TIM'],['724','5','Brazil','Claro'],['724','6','Brazil','Vivo'],['724','10','Brazil','Vivo'],
        ['655','1','South Africa','Vodacom'],['655','10','South Africa','MTN'],['655','7','South Africa','Cell C'],
        ['602','1','Egypt','Orange'],['602','2','Egypt','Vodafone'],['602','3','Egypt','Etisalat'],
        ['621','20','Nigeria','Airtel'],['621','30','Nigeria','MTN'],['621','50','Nigeria','Glo'],
        ['420','1','Saudi Arabia','STC'],['420','3','Saudi Arabia','Mobily'],['420','4','Saudi Arabia','Zain'],
        ['424','2','UAE','Etisalat'],['424','3','UAE','du'],
        ['510','10','Indonesia','Telkomsel'],['510','11','Indonesia','XL'],['510','1','Indonesia','Indosat'],
        ['515','2','Philippines','Globe'],['515','3','Philippines','Smart'],
        ['525','1','Singapore','Singtel'],['525','3','Singapore','StarHub'],['525','5','Singapore','M1'],
        ['454','0','Hong Kong','CSL'],['454','3','Hong Kong','3'],
        ['250','1','Russia','MTS'],['250','2','Russia','MegaFon'],['250','99','Russia','Beeline'],
        ['214','1','Spain','Vodafone'],['214','3','Spain','Orange'],['214','7','Spain','Movistar'],
        ['222','1','Italy','TIM'],['222','10','Italy','Vodafone'],['222','88','Italy','Wind Tre'],
        ['262','1','Mexico','Telcel'],['334','20','Mexico','Telcel'],['334','30','Mexico','Movistar'],['334','50','Mexico','AT&T'],
    ];

    function render() {
        const q = document.getElementById('search').value.trim().toLowerCase();
        const rows = DATA.filter((r) => !q || r.join(' ').toLowerCase().includes(q));
        document.getElementById('list').innerHTML = `<table class="w-full text-sm"><thead><tr class="text-left text-xs uppercase tracking-wide text-slate-400"><th class="px-3 py-2">MCC</th><th class="px-3 py-2">MNC</th><th class="px-3 py-2">Country</th><th class="px-3 py-2">Operator</th><th></th></tr></thead><tbody>${rows.map((r, i) => `<tr class="border-t border-white/10 ${i % 2 ? 'bg-white/[0.02]' : ''}"><td class="px-3 py-2 font-mono text-teal-200">${r[0]}</td><td class="px-3 py-2 font-mono text-teal-200">${r[1].padStart(2,'0')}</td><td class="px-3 py-2 text-slate-300">${r[2]}</td><td class="px-3 py-2 text-white">${r[3]}</td><td class="px-3 py-2 text-right"><button onclick="copyToClipboard('${r[0]}${r[1].padStart(2,'0')}', 'PLMN copied')" class="rounded-md border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold text-slate-300 transition hover:bg-white/10">${r[0]}${r[1].padStart(2,'0')}</button></td></tr>`).join('')}</tbody></table>`;
        if (!rows.length) document.getElementById('list').innerHTML = '<p class="py-8 text-center text-slate-500">No matches found.</p>';
    }
    document.addEventListener('DOMContentLoaded', render);
</script>
@endpush
