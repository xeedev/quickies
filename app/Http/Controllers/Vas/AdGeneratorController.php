<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class AdGeneratorController extends Controller
{
    public function index()
    {
        return view('quickies.vas.ad-generator');
    }
}
