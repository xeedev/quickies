<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Minimal Stripe client built on the HTTP client so the app has zero extra
 * Composer dependencies. Configure STRIPE_* keys in .env and everything works.
 */
class StripeService
{
    private const BASE = 'https://api.stripe.com/v1';

    public function configured(): bool
    {
        return ! empty(config('services.stripe.secret'));
    }

    private function request()
    {
        return Http::withToken(config('services.stripe.secret'))
            ->asForm()
            ->baseUrl(self::BASE);
    }

    /**
     * Ensure the user has a Stripe customer, creating one if needed.
     */
    public function ensureCustomer(User $user): string
    {
        if ($user->stripe_customer_id) {
            return $user->stripe_customer_id;
        }

        $res = $this->request()->post('/customers', [
            'email' => $user->email,
            'name' => $user->name,
            'metadata[user_id]' => $user->id,
        ])->throw()->json();

        $user->forceFill(['stripe_customer_id' => $res['id']])->save();

        return $res['id'];
    }

    /**
     * Create a Checkout Session for a subscription and return its URL.
     */
    public function createCheckoutSession(User $user, string $priceId, string $successUrl, string $cancelUrl, int $trialDays = 0): string
    {
        $customer = $this->ensureCustomer($user);

        $payload = [
            'mode' => 'subscription',
            'customer' => $customer,
            'line_items[0][price]' => $priceId,
            'line_items[0][quantity]' => 1,
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'allow_promotion_codes' => 'true',
            'client_reference_id' => $user->id,
            'subscription_data[metadata][user_id]' => $user->id,
        ];

        if ($trialDays > 0) {
            $payload['subscription_data[trial_period_days]'] = $trialDays;
        }

        $res = $this->request()->post('/checkout/sessions', $payload)->throw()->json();

        return $res['url'];
    }

    public function billingPortalUrl(User $user, string $returnUrl): ?string
    {
        if (! $user->stripe_customer_id) {
            return null;
        }

        $res = $this->request()->post('/billing_portal/sessions', [
            'customer' => $user->stripe_customer_id,
            'return_url' => $returnUrl,
        ])->throw()->json();

        return $res['url'] ?? null;
    }

    public function getSession(string $id): array
    {
        return $this->request()->get("/checkout/sessions/{$id}")->throw()->json();
    }

    public function getSubscription(string $id): array
    {
        return $this->request()->get("/subscriptions/{$id}")->throw()->json();
    }

    /**
     * Sync a user's local subscription state from a Stripe subscription object.
     *
     * @param  array<string, mixed>  $subscription
     */
    public function syncSubscription(User $user, array $subscription): void
    {
        $priceId = $subscription['items']['data'][0]['price']['id'] ?? null;
        $plan = collect(config('plans.plans'))
            ->search(fn ($p) => ($p['stripe_price'] ?? null) === $priceId);

        $user->forceFill([
            'stripe_subscription_id' => $subscription['id'] ?? $user->stripe_subscription_id,
            'subscription_status' => $subscription['status'] ?? $user->subscription_status,
            'plan' => $plan ?: $user->plan,
            'current_period_ends_at' => isset($subscription['current_period_end'])
                ? now()->createFromTimestamp($subscription['current_period_end'])
                : $user->current_period_ends_at,
            'trial_ends_at' => isset($subscription['trial_end']) && $subscription['trial_end']
                ? now()->createFromTimestamp($subscription['trial_end'])
                : $user->trial_ends_at,
        ])->save();
    }

    /**
     * Verify a Stripe webhook signature (t=...,v1=...) using the signing secret.
     */
    public function verifyWebhook(string $payload, ?string $sigHeader, string $secret): bool
    {
        if (! $sigHeader) {
            return false;
        }

        $parts = collect(explode(',', $sigHeader))
            ->mapWithKeys(function ($part) {
                [$k, $v] = array_pad(explode('=', $part, 2), 2, null);
                return [$k => $v];
            });

        $timestamp = $parts->get('t');
        $signature = $parts->get('v1');
        if (! $timestamp || ! $signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        // Reject events older than 5 minutes to prevent replay.
        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        return hash_equals($expected, $signature);
    }
}
