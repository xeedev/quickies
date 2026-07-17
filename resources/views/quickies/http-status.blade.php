@extends('layouts.app')

@section('title', 'HTTP Status Codes')
@section('description', 'Look up and learn every HTTP status code.')

@section('content')
    <x-tool-header title="HTTP Status Codes" subtitle="Search and reference every HTTP status code and its meaning."
        from="from-cyan-500" to="to-teal-500" icon="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />

    <div class="rounded-3xl border border-white/10 bg-white/5 p-5 backdrop-blur-xl sm:p-8">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input id="search" oninput="render()" placeholder="Search by code or name… (e.g. 404, teapot)" class="w-full rounded-2xl border border-white/10 bg-white/5 py-3 pl-12 pr-4 text-sm text-white placeholder-slate-500 transition focus:border-cyan-400/60 focus:outline-none focus:ring-2 focus:ring-cyan-500/30">
            </div>
            <div class="flex flex-wrap gap-1.5">
                @foreach (['all' => 'All', '1' => '1xx', '2' => '2xx', '3' => '3xx', '4' => '4xx', '5' => '5xx'] as $val => $label)
                    <button data-class="{{ $val }}" onclick="setClass('{{ $val }}')" class="class-btn rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-sm font-semibold text-slate-300 transition hover:bg-white/10">{{ $label }}</button>
                @endforeach
            </div>
        </div>
        <div id="list" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3"></div>
    </div>
@endsection

@push('scripts')
<script>
    const CODES = [
        [100,'Continue','The server has received the request headers and the client should proceed to send the body.'],
        [101,'Switching Protocols','The server is switching protocols as requested by the client.'],
        [103,'Early Hints','Used to return some response headers before the final HTTP message.'],
        [200,'OK','The request succeeded.'],
        [201,'Created','The request succeeded and a new resource was created.'],
        [202,'Accepted','The request has been accepted for processing but is not complete.'],
        [204,'No Content','The server successfully processed the request and is not returning content.'],
        [206,'Partial Content','The server is delivering only part of the resource due to a range header.'],
        [301,'Moved Permanently','The resource has been permanently moved to a new URL.'],
        [302,'Found','The resource resides temporarily under a different URL.'],
        [303,'See Other','The response can be found under another URL using GET.'],
        [304,'Not Modified','The resource has not been modified since the last request.'],
        [307,'Temporary Redirect','The request should be repeated with another URL, keeping the method.'],
        [308,'Permanent Redirect','The resource is now permanently located at another URL, keeping the method.'],
        [400,'Bad Request','The server cannot process the request due to a client error.'],
        [401,'Unauthorized','Authentication is required and has failed or not been provided.'],
        [402,'Payment Required','Reserved for future use; sometimes used by APIs for billing.'],
        [403,'Forbidden','The server understood the request but refuses to authorize it.'],
        [404,'Not Found','The requested resource could not be found.'],
        [405,'Method Not Allowed','The request method is not supported for the resource.'],
        [406,'Not Acceptable','The resource cannot produce a response matching the Accept headers.'],
        [408,'Request Timeout','The server timed out waiting for the request.'],
        [409,'Conflict','The request conflicts with the current state of the server.'],
        [410,'Gone','The resource is no longer available and will not return.'],
        [418,"I'm a teapot",'The server refuses to brew coffee because it is a teapot (RFC 2324).'],
        [422,'Unprocessable Entity','The request was well-formed but had semantic errors.'],
        [429,'Too Many Requests','The user has sent too many requests in a given amount of time.'],
        [451,'Unavailable For Legal Reasons','Access is denied for legal reasons.'],
        [500,'Internal Server Error','A generic error occurred on the server.'],
        [501,'Not Implemented','The server does not support the functionality required.'],
        [502,'Bad Gateway','The server received an invalid response from an upstream server.'],
        [503,'Service Unavailable','The server is not ready to handle the request (overloaded or down).'],
        [504,'Gateway Timeout','The upstream server failed to respond in time.'],
        [505,'HTTP Version Not Supported','The HTTP version used in the request is not supported.'],
    ];
    let cls = 'all';

    function color(code) {
        const c = Math.floor(code / 100);
        return { 1: 'text-sky-300 border-sky-400/30', 2: 'text-emerald-300 border-emerald-400/30', 3: 'text-amber-300 border-amber-400/30', 4: 'text-orange-300 border-orange-400/30', 5: 'text-rose-300 border-rose-400/30' }[c];
    }

    function setClass(c) {
        cls = c;
        document.querySelectorAll('.class-btn').forEach((b) => {
            const active = b.dataset.class === c;
            b.classList.toggle('bg-cyan-500/20', active);
            b.classList.toggle('text-cyan-200', active);
            b.classList.toggle('border-cyan-400/40', active);
        });
        render();
    }

    function render() {
        const q = document.getElementById('search').value.trim().toLowerCase();
        const list = CODES.filter(([code, name]) => {
            if (cls !== 'all' && Math.floor(code / 100) !== +cls) return false;
            return !q || String(code).includes(q) || name.toLowerCase().includes(q);
        });
        document.getElementById('list').innerHTML = list.map(([code, name, desc]) => `
            <div class="rounded-2xl border ${color(code)} bg-white/5 p-4">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-2xl font-bold ${color(code).split(' ')[0]}">${code}</span>
                    <button onclick="copyToClipboard('${code} ${name.replace(/'/g, "")}', 'Copied')" class="rounded-md border border-white/10 bg-white/5 px-2 py-0.5 text-[11px] font-semibold text-slate-300 transition hover:bg-white/10">Copy</button>
                </div>
                <div class="mt-1 font-semibold text-white">${name}</div>
                <p class="mt-1 text-xs leading-relaxed text-slate-400">${desc}</p>
            </div>`).join('') || '<p class="col-span-full py-8 text-center text-slate-500">No matching status codes.</p>';
    }
    document.addEventListener('DOMContentLoaded', () => { setClass('all'); });
</script>
@endpush
