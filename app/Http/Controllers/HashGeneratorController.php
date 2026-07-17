<?php

namespace App\Http\Controllers;

class HashGeneratorController extends Controller
{
    public function index()
    {
        return view('quickies.hash-generator');
    }
}
