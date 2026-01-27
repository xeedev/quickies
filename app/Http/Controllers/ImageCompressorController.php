<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageCompressorController extends Controller
{
    public function index()
    {
        return view('quickies.image-compressor');
    }
}
