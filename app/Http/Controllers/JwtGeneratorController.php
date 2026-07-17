<?php

namespace App\Http\Controllers;

class JwtGeneratorController extends Controller
{
    public function index()
    {
        return view('quickies.jwt-generator');
    }
}
