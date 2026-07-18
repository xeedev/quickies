<?php

namespace App\Http\Middleware;

use App\Models\ToolUsage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates access to a tool page.
 *
 *  - Admins and active subscribers: unlimited access to everything.
 *  - Everyone else:
 *      * "Pro" tools require a subscription  -> redirected to /pricing.
 *      * "Free" tools may be used once per day per identity. Identity is the
 *        user id when logged in, otherwise the IP address, so the limit does
 *        not reset by switching browsers within the same day.
 */
class GateTool
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Unlimited access.
        if ($user && $user->hasActiveSubscription()) {
            return $next($request);
        }

        $slug = '/'.ltrim($request->path(), '/');
        $freeTools = config('plans.free_tools', []);
        $trialAll = (bool) config('plans.trial_all_tools', false);

        // Every tool is tryable for free when trial_all_tools is on; otherwise
        // only the allow-listed tools are.
        $isTryable = $trialAll || in_array($slug, $freeTools, true);

        // Not tryable at all -> must subscribe.
        if (! $isTryable) {
            return redirect()->route('upgrade', ['reason' => 'pro', 'tool' => $slug]);
        }

        // Enforce the per-day trial limit.
        $identity = $user ? ['user_id' => $user->id] : ['ip' => $request->ip()];
        $today = now()->toDateString();
        $limit = (int) config('plans.daily_limit_per_tool', 1);

        $usedCount = ToolUsage::forIdentity($identity)
            ->where('tool_slug', $slug)
            ->where('used_on', $today)
            ->count();

        if ($usedCount >= $limit) {
            return redirect()->route('upgrade', ['reason' => 'limit', 'tool' => $slug]);
        }

        ToolUsage::record($identity, $slug, $request);

        return $next($request);
    }
}
