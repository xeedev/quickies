<?php

namespace App\Http\Controllers;

class WordCounterController extends Controller
{
    public function index()
    {
        return view('quickies.word-counter');
    }
}
