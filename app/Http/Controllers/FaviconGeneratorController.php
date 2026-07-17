<?php

namespace App\Http\Controllers;

class FaviconGeneratorController extends Controller
{
    public function index()
    {
        return view('quickies.favicon-generator');
    }
}
