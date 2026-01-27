<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PngToSvgController extends Controller
{
    public function index()
    {
        return view('quickies.png-to-svg');
    }
}
