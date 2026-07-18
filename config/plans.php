<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Free tier limits
    |--------------------------------------------------------------------------
    |
    | With "trial_all_tools" enabled, every tool can be tried once per day by
    | anonymous and free users — a single free run per tool, per day. After that
    | run they are asked to upgrade. This lets people try everything before
    | paying. The limit is enforced server-side (keyed by user id when logged in,
    | otherwise by IP) so it can't be reset by switching browsers within a day.
    |
    | The "free_tools" list is only used when trial_all_tools is false, to
    | restrict which tools are tryable for free.
    |
    */

    'trial_all_tools' => true,

    'free_tools' => [
        '/word-counter',
        '/case-converter',
        '/json-formatter',
        '/qr-code',
        '/password-generator',
    ],

    // Uses allowed per tool, per identity, per day (before the paywall).
    'daily_limit_per_tool' => 1,

    /*
    |--------------------------------------------------------------------------
    | Subscription plans
    |--------------------------------------------------------------------------
    |
    | Price IDs come from your Stripe dashboard. Fill them in .env and you are
    | ready to charge — no other code changes required.
    |
    */

    'currency' => env('BILLING_CURRENCY', 'usd'),
    'currency_symbol' => env('BILLING_CURRENCY_SYMBOL', '$'),
    'trial_days' => (int) env('BILLING_TRIAL_DAYS', 7),

    'plans' => [

        'monthly' => [
            'name' => 'Pro Monthly',
            'price' => 9,
            'interval' => 'month',
            'stripe_price' => env('STRIPE_PRICE_MONTHLY'),
            'badge' => null,
        ],

        'yearly' => [
            'name' => 'Pro Yearly',
            'price' => 79,
            'interval' => 'year',
            'stripe_price' => env('STRIPE_PRICE_YEARLY'),
            'badge' => 'Save 27%',
        ],

    ],

    // Marketing bullet points shown on the pricing/prelander pages.
    'pro_features' => [
        'Unlimited use of all 80+ tools',
        'No daily limits, ever',
        'Batch processing & bigger files',
        'Priority processing speed',
        'New tools added every month',
        'Cancel anytime',
    ],

    'free_features' => [
        'Try all 80+ tools',
        '1 free run per tool, every day',
        'Runs 100% in your browser',
        'No credit card required',
    ],
];
