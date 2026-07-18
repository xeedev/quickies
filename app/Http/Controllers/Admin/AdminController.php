<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ToolUsage;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $activeStatuses = ['active', 'trialing', 'comp'];

        $revenue = User::whereIn('subscription_status', ['active', 'trialing'])
            ->get()
            ->sum(fn ($u) => (float) config("plans.plans.{$u->plan}.price", 0));

        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'activeSubs' => User::whereIn('subscription_status', $activeStatuses)->count(),
            'usageToday' => ToolUsage::where('used_on', now()->toDateString())->count(),
            'usage7d' => ToolUsage::where('used_on', '>=', now()->subDays(7)->toDateString())->count(),
            'mrr' => $revenue,
            'symbol' => config('plans.currency_symbol'),
            'recentUsers' => User::latest()->take(6)->get(),
            'topTools' => ToolUsage::selectRaw('tool_slug, count(*) as hits')
                ->where('used_on', '>=', now()->subDays(30)->toDateString())
                ->groupBy('tool_slug')->orderByDesc('hits')->take(8)->get(),
        ]);
    }

    public function users(Request $request)
    {
        $q = $request->query('q');

        $users = User::query()
            ->when($q, fn ($query) => $query->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', compact('users', 'q'));
    }

    public function updateUser(Request $request, User $user)
    {
        $action = $request->input('action');

        switch ($action) {
            case 'grant_comp':
                $user->forceFill([
                    'subscription_status' => 'comp',
                    'plan' => 'comp',
                    'current_period_ends_at' => now()->addYear(),
                ])->save();
                $message = 'Granted complimentary Pro access.';
                break;

            case 'revoke':
                $user->forceFill([
                    'subscription_status' => null,
                    'plan' => null,
                    'current_period_ends_at' => null,
                ])->save();
                $message = 'Access revoked.';
                break;

            case 'make_admin':
                $user->forceFill(['role' => 'admin'])->save();
                $message = 'User promoted to admin.';
                break;

            case 'make_user':
                $user->forceFill(['role' => 'user'])->save();
                $message = 'Admin rights removed.';
                break;

            default:
                $message = 'No action taken.';
        }

        return back()->with('status', $message);
    }

    public function destroyUser(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('status', 'User deleted.');
    }
}
