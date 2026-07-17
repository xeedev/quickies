<?php

namespace App\Http\Controllers;

class PdfMergeController extends Controller
{
    public function index()
    {
        return view('quickies.pdf-merge');
    }
}
