@extends('layouts.app')

@section('title', 'Token Counter')
@section('description', 'Estimate LLM token counts and cost for your prompts.')

@section('content')
    <x-tool-header title="Token Counter" subtitle="Estimate token usage and cost for popular LLMs."
        from="from-fuchsia-500" to="to-pink-500" icon="M7 8h10M7 12h6m-6 4h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z" />

    <div class="space-y-6">
        <div id="stats" class="grid grid-cols-2 gap-4 sm:grid-cols-4"></div>

        <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <label class="flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm text-slate-200">Model
                    <select id="model" onchange="analyze()" class="rounded-lg border border-white/10 bg-slate-900 px-2 py-1 text-sm text-white focus:outline-none">
                        <option value="4o" class="bg-slate-900" data-in="2.5" data-out="10">GPT-4o</option>
                        <option value="4omini" class="bg-slate-900" data-in="0.15" data-out="0.6">GPT-4o mini</option>
                        <option value="35" class="bg-slate-900" data-in="0.5" data-out="1.5">GPT-3.5 Turbo</option>
                        <option value="claude" class="bg-slate-900" data-in="3" data-out="15">Claude Sonnet</option>
                    </select>
                </label>
                <span class="text-xs text-slate-500">Prices are approximate USD per 1M tokens.</span>
            </div>
            <label class="mb-2 block text-sm font-semibold text-slate-200">Prompt</label>
            <textarea id="input" rows="12" oninput="analyze()" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-slate-500 transition focus:border-fuchsia-400/60 focus:outline-none focus:ring-2 focus:ring-fuchsia-500/30" placeholder="Paste your prompt to estimate tokens…"></textarea>
            <p class="mt-3 text-xs text-slate-500">Estimate uses a heuristic (~4 characters per token). Exact counts depend on the model's tokenizer.</p>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function estimateTokens(text) {
        if (!text) return 0;
        const chars = text.length;
        const words = (text.match(/\S+/g) || []).length;
        // Blend char-based (~4 chars/token) and word-based (~0.75 words/token) estimates.
        return Math.max(1, Math.round((chars / 4 + words / 0.75) / 2));
    }

    function card(label, value, accent = 'text-fuchsia-400') {
        return `<div class="rounded-2xl border border-white/10 bg-white/5 p-4 text-center backdrop-blur-xl">
            <div class="bg-gradient-to-r from-fuchsia-400 to-pink-400 bg-clip-text text-2xl font-bold text-transparent sm:text-3xl">${value}</div>
            <div class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-400">${label}</div></div>`;
    }

    function analyze() {
        const text = document.getElementById('input').value;
        const tokens = estimateTokens(text);
        const opt = document.getElementById('model').selectedOptions[0];
        const inPrice = parseFloat(opt.dataset.in);
        const cost = (tokens / 1e6) * inPrice;
        document.getElementById('stats').innerHTML =
            card('Tokens', tokens.toLocaleString()) +
            card('Characters', text.length.toLocaleString()) +
            card('Words', (text.match(/\S+/g) || []).length.toLocaleString()) +
            card('Input cost', '$' + cost.toFixed(cost < 0.01 ? 6 : 4));
    }
    document.addEventListener('DOMContentLoaded', analyze);
</script>
@endpush
