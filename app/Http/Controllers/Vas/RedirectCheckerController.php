<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class RedirectCheckerController extends Controller
{
    public function index()
    {
        return view('quickies.vas.redirect-checker');
    }
}
