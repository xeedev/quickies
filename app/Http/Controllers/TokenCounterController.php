<?php

namespace App\Http\Controllers;

class TokenCounterController extends Controller
{
    public function index()
    {
        return view('quickies.token-counter');
    }
}
