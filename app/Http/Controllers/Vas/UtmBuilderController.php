<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class UtmBuilderController extends Controller
{
    public function index()
    {
        return view('quickies.vas.utm-builder');
    }
}
