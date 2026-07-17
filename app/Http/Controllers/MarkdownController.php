<?php

namespace App\Http\Controllers;

class MarkdownController extends Controller
{
    public function index()
    {
        return view('quickies.markdown');
    }
}
