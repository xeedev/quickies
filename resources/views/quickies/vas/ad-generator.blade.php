@extends('layouts.app')

@section('title', 'Ad / SMS / Push Generator')
@section('description', 'Generate ad copy, SMS and push notification variants.')

@section('content')
    <x-tool-header title="Ad / SMS / Push Generator" subtitle="Spin up on-brand ad, SMS and push notification copy in seconds."
        from="from-pink-500" to="to-rose-500" icon="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div><label class="mb-1 block text-sm font-semibold text-slate-200">Product / offer</label><input id="product" value="Premium Football Alerts" oninput="generate()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-pink-400/60 focus:outline-none"></div>
            <div><label class="mb-1 block text-sm font-semibold text-slate-200">Key benefit</label><input id="benefit" value="live scores and exclusive highlights" oninput="generate()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-pink-400/60 focus:outline-none"></div>
            <div><label class="mb-1 block text-sm font-semibold text-slate-200">Call to action</label><input id="cta" value="Subscribe now" oninput="generate()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-pink-400/60 focus:outline-none"></div>
            <div><label class="mb-1 block text-sm font-semibold text-slate-200">Offer / price</label><input id="offer" value="just $2.99/week" oninput="generate()" class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-white focus:border-pink-400/60 focus:outline-none"></div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-3">
            <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">Channel
                <select id="channel" onchange="generate()" class="rounded-lg border border-white/10 bg-slate-900 px-2 py-1 text-white focus:outline-none"><option value="sms" class="bg-slate-900">SMS</option><option value="push" class="bg-slate-900">Push</option><option value="ad" class="bg-slate-900">Ad copy</option></select>
            </label>
            <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">Tone
                <select id="tone" onchange="generate()" class="rounded-lg border border-white/10 bg-slate-900 px-2 py-1 text-white focus:outline-none"><option class="bg-slate-900">Exciting</option><option class="bg-slate-900">Urgent</option><option class="bg-slate-900">Friendly</option><option class="bg-slate-900">Professional</option></select>
            </label>
            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200"><input type="checkbox" id="emoji" checked onchange="generate()" class="h-4 w-4 accent-pink-500"> Emoji</label>
            <button onclick="generate()" class="ml-auto rounded-xl bg-gradient-to-r from-pink-500 to-rose-500 px-5 py-2 text-sm font-semibold text-white shadow transition hover:scale-[1.02] active:scale-95">Regenerate</button>
        </div>
        <div id="variants" class="mt-6 space-y-3"></div>
        <p class="mt-4 text-xs text-slate-500">Copy is generated locally from proven marketing templates — always review before sending.</p>
    </div>
@endsection

@push('scripts')
<script>
    const EMO = { Exciting: ['🔥','⚡','🎉','🚀'], Urgent: ['⏰','🚨','⚠️','⌛'], Friendly: ['😊','👋','💛','✨'], Professional: ['✅','📈','💼','🔔'] };
    const val = (id) => document.getElementById(id).value.trim();

    function templates(ch, tone, p, b, cta, offer, emo) {
        const e = emo ? EMO[tone] : ['','','',''];
        const pick = (i) => (e[i] ? e[i] + ' ' : '');
        if (ch === 'sms') return [
            `${pick(0)}${p}: get ${b} for ${offer}. ${cta}!`,
            `${pick(1)}Don't miss out! ${b.charAt(0).toUpperCase()+b.slice(1)} — ${offer}. ${cta} today.`,
            `New from ${p} ${pick(2)} ${b}. ${cta} for ${offer}. Reply STOP to opt out.`,
            `${pick(3)}${cta} to ${p} and enjoy ${b}. Only ${offer}!`,
        ];
        if (ch === 'push') return [
            { t: `${pick(0)}${p}`, b: `Unlock ${b} for ${offer}. ${cta}!` },
            { t: `${pick(1)}${cta} today`, b: `${b.charAt(0).toUpperCase()+b.slice(1)} awaits — ${offer}.` },
            { t: `${pick(2)}Limited offer`, b: `${p}: ${b}. Now ${offer}.` },
            { t: `${pick(3)}You're missing out`, b: `Join ${p} for ${b}. ${cta}.` },
        ];
        return [
            { h: `${pick(0)}${p} — ${b}`, d: `${cta} and unlock ${b} for ${offer}. Join thousands of happy fans.` },
            { h: `${cta} to ${p}`, d: `${pick(1)}Get ${b} for ${offer}. Cancel anytime.` },
            { h: `${pick(2)}${b.charAt(0).toUpperCase()+b.slice(1)}`, d: `${p} brings you ${b}. Start now for ${offer}.` },
        ];
    }

    function generate() {
        const ch = val('channel'), tone = val('tone'), emo = document.getElementById('emoji').checked;
        const p = val('product') || 'our service', b = val('benefit') || 'great features', cta = val('cta') || 'Sign up', offer = val('offer') || 'a low price';
        const items = templates(ch, tone, p, b, cta, offer, emo);
        const limit = ch === 'sms' ? 160 : ch === 'push' ? 150 : 90;
        document.getElementById('variants').innerHTML = items.map((it, i) => {
            let text, meta;
            if (ch === 'sms') { text = it; meta = `${it.length}/160 chars · ${it.length <= 160 ? '1' : Math.ceil(it.length/153)} segment(s)`; }
            else if (ch === 'push') { text = `${it.t}\n${it.b}`; meta = `Title ${it.t.length}/50 · Body ${it.b.length}/150`; }
            else { text = `${it.h}\n${it.d}`; meta = `Headline ${it.h.length}/30 · Desc ${it.d.length}/90`; }
            const over = (ch === 'sms' && it.length > 160) || (ch === 'push' && (it.t.length > 50 || it.b.length > 150)) || (ch === 'ad' && (it.h.length > 30 || it.d.length > 90));
            return `<div class="rounded-2xl border ${over ? 'border-amber-400/30' : 'border-white/10'} bg-white/5 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 whitespace-pre-wrap text-sm text-white">${text.replace(/</g,'&lt;')}</div>
                    <button data-copy="${encodeURIComponent(text)}" onclick="copyToClipboard(decodeURIComponent(this.dataset.copy), 'Copy ${i+1} copied')" class="flex-shrink-0 rounded-lg border border-white/10 bg-white/5 px-3 py-1 text-xs font-semibold text-slate-200 transition hover:bg-white/10">Copy</button>
                </div>
                <div class="mt-2 text-[11px] ${over ? 'text-amber-300' : 'text-slate-500'}">${meta}${over ? ' · over recommended length' : ''}</div>
            </div>`;
        }).join('');
    }
    document.addEventListener('DOMContentLoaded', generate);
</script>
@endpush
