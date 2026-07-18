<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpgradeController extends Controller
{
    public function show(Request $request)
    {
        return view('upgrade', [
            'reason' => $request->query('reason', 'pro'),
            'tool' => $request->query('tool'),
            'plans' => config('plans.plans'),
            'symbol' => config('plans.currency_symbol'),
            'trialDays' => config('plans.trial_days'),
        ]);
    }
}
