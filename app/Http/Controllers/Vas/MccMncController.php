<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class MccMncController extends Controller
{
    public function index()
    {
        return view('quickies.vas.mcc-mnc');
    }
}
