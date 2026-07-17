<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class SmsCheckerController extends Controller
{
    public function index()
    {
        return view('quickies.vas.sms-checker');
    }
}
