@extends('layouts.app')

@section('title', 'Manage users')

@section('content')
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">Users</h1>
            <p class="mt-1 text-sm text-slate-400">Grant access, promote admins or remove accounts.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="rounded-2xl border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/10">← Dashboard</a>
    </div>

    <form method="GET" action="{{ route('admin.users') }}" class="mb-5">
        <div class="relative max-w-md">
            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input name="q" value="{{ $q }}" placeholder="Search name or email…" class="w-full rounded-2xl border border-white/10 bg-white/5 py-3 pl-12 pr-4 text-sm text-white placeholder-slate-500 focus:border-indigo-400/60 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
        </div>
    </form>

    <div class="overflow-x-auto rounded-3xl border border-white/10 bg-white/5 backdrop-blur-xl">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-white/10 text-left text-xs uppercase tracking-wide text-slate-400">
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Plan</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Joined</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                    <tr class="border-b border-white/5">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-white">{{ $u->name }}</div>
                            <div class="text-xs text-slate-500">{{ $u->email }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-0.5 text-xs font-semibold {{ $u->hasActiveSubscription() ? 'text-emerald-300' : 'text-slate-400' }}">{{ $u->planLabel() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $u->isAdmin() ? 'bg-fuchsia-500/20 text-fuchsia-200' : 'bg-white/5 text-slate-400' }}">{{ ucfirst($u->role) }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-400">{{ $u->created_at?->format('M j, Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap justify-end gap-1.5">
                                @if ($u->hasActiveSubscription())
                                    <form method="POST" action="{{ route('admin.users.update', $u) }}"><input type="hidden" name="action" value="revoke">@csrf<button class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-xs font-semibold text-amber-200 transition hover:bg-white/10">Revoke</button></form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.update', $u) }}"><input type="hidden" name="action" value="grant_comp">@csrf<button class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-xs font-semibold text-emerald-200 transition hover:bg-white/10">Grant Pro</button></form>
                                @endif
                                @if ($u->isAdmin())
                                    <form method="POST" action="{{ route('admin.users.update', $u) }}"><input type="hidden" name="action" value="make_user">@csrf<button class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-xs font-semibold text-slate-300 transition hover:bg-white/10">Demote</button></form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.update', $u) }}"><input type="hidden" name="action" value="make_admin">@csrf<button class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-xs font-semibold text-fuchsia-200 transition hover:bg-white/10">Make admin</button></form>
                                @endif
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete this user?')">@csrf @method('DELETE')<button class="rounded-lg border border-white/10 bg-white/5 px-2.5 py-1 text-xs font-semibold text-rose-300 transition hover:bg-rose-500/20">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-10 text-center text-slate-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
