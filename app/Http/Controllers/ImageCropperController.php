<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ImageCropperController extends Controller
{
    public function index()
    {
        return view('quickies.image-cropper');
    }
}
