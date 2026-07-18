<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Free tier limits
    |--------------------------------------------------------------------------
    |
    | Anonymous and free users may use each of the tools listed below once per
    | day. Every other tool requires an active Pro subscription. The daily limit
    | is enforced server-side (keyed by user id when logged in, otherwise by IP)
    | so it can't be reset simply by switching browsers within the same day.
    |
    */

    'free_tools' => [
        '/word-counter',
        '/case-converter',
        '/json-formatter',
        '/qr-code',
        '/password-generator',
    ],

    // Uses allowed per free tool, per identity, per day.
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
        '5 starter tools',
        '1 use per tool, per day',
        'Runs 100% in your browser',
        'No credit card required',
    ],
];
