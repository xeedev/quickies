<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(private StripeService $stripe)
    {
    }

    public function handle(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');
        $payload = $request->getContent();

        if ($secret) {
            $valid = $this->stripe->verifyWebhook($payload, $request->header('Stripe-Signature'), $secret);
            if (! $valid) {
                return response('Invalid signature', 400);
            }
        }

        $event = json_decode($payload, true);
        $type = $event['type'] ?? '';
        $object = $event['data']['object'] ?? [];

        try {
            switch ($type) {
                case 'checkout.session.completed':
                    $this->onCheckoutCompleted($object);
                    break;

                case 'customer.subscription.created':
                case 'customer.subscription.updated':
                case 'customer.subscription.deleted':
                    $this->onSubscriptionChange($object);
                    break;
            }
        } catch (\Throwable $e) {
            report($e);

            return response('Webhook handler error', 500);
        }

        return response('ok', 200);
    }

    private function resolveUser(array $object): ?User
    {
        $userId = $object['metadata']['user_id'] ?? $object['client_reference_id'] ?? null;
        if ($userId && $user = User::find($userId)) {
            return $user;
        }

        $customer = $object['customer'] ?? null;
        if ($customer) {
            return User::where('stripe_customer_id', $customer)->first();
        }

        return null;
    }

    private function onCheckoutCompleted(array $session): void
    {
        $user = $this->resolveUser($session);
        if (! $user) {
            return;
        }

        if (! empty($session['customer'])) {
            $user->forceFill(['stripe_customer_id' => $session['customer']])->save();
        }

        if (! empty($session['subscription'])) {
            $subscription = $this->stripe->getSubscription($session['subscription']);
            $this->stripe->syncSubscription($user, $subscription);
        }
    }

    private function onSubscriptionChange(array $subscription): void
    {
        $user = $this->resolveUser($subscription);
        if (! $user) {
            return;
        }

        $this->stripe->syncSubscription($user, $subscription);
    }
}
