<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class RoiCalculatorController extends Controller
{
    public function index()
    {
        return view('quickies.vas.roi-calculator');
    }
}
