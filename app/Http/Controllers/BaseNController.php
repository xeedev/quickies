<?php

namespace App\Http\Controllers;

class BaseNController extends Controller
{
    public function index()
    {
        return view('quickies.base-n');
    }
}
