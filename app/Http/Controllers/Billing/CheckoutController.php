<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(private StripeService $stripe)
    {
    }

    public function checkout(Request $request, string $plan)
    {
        $config = config("plans.plans.{$plan}");

        if (! $config) {
            return redirect()->route('pricing')->with('error', 'Unknown plan.');
        }

        if (! $this->stripe->configured() || empty($config['stripe_price'])) {
            return redirect()->route('pricing')->with('error', 'Billing is not configured yet. Please add your Stripe keys and price IDs to .env.');
        }

        try {
            $url = $this->stripe->createCheckoutSession(
                $request->user(),
                $config['stripe_price'],
                route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
                route('pricing'),
                (int) config('plans.trial_days', 0),
            );

            return redirect()->away($url);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('pricing')->with('error', 'Could not start checkout. Please try again.');
        }
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');
        $user = $request->user();

        if ($sessionId && $this->stripe->configured()) {
            try {
                $session = $this->stripe->getSession($sessionId);
                if (! empty($session['subscription'])) {
                    $subscription = $this->stripe->getSubscription($session['subscription']);
                    $this->stripe->syncSubscription($user, $subscription);
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return view('billing.success');
    }

    public function portal(Request $request)
    {
        if (! $this->stripe->configured()) {
            return redirect()->route('dashboard')->with('error', 'Billing is not configured yet.');
        }

        $url = $this->stripe->billingPortalUrl($request->user(), route('dashboard'));

        if (! $url) {
            return redirect()->route('pricing')->with('error', 'No billing account found yet. Subscribe first.');
        }

        return redirect()->away($url);
    }
}
