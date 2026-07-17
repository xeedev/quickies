<?php

namespace App\Http\Controllers;

class ExifViewerController extends Controller
{
    public function index()
    {
        return view('quickies.exif-viewer');
    }
}
