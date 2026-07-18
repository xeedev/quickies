<?php

namespace App\Http\Controllers;

class PricingController extends Controller
{
    public function index()
    {
        return view('pricing', [
            'plans' => config('plans.plans'),
            'proFeatures' => config('plans.pro_features'),
            'freeFeatures' => config('plans.free_features'),
            'symbol' => config('plans.currency_symbol'),
            'trialDays' => config('plans.trial_days'),
        ]);
    }
}
