<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class AbTestController extends Controller
{
    public function index()
    {
        return view('quickies.vas.ab-test');
    }
}
