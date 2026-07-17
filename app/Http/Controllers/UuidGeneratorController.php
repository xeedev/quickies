<?php

namespace App\Http\Controllers;

class UuidGeneratorController extends Controller
{
    public function index()
    {
        return view('quickies.uuid-generator');
    }
}
