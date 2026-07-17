<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class LtvCalculatorController extends Controller
{
    public function index()
    {
        return view('quickies.vas.ltv-calculator');
    }
}
