<?php

namespace App\Http\Controllers;

class MarkdownToPdfController extends Controller
{
    public function index()
    {
        return view('quickies.markdown-to-pdf');
    }
}
