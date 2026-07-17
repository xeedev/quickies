<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class MsisdnController extends Controller
{
    public function index()
    {
        return view('quickies.vas.msisdn');
    }
}
