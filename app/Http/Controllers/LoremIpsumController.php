<?php

namespace App\Http\Controllers;

class LoremIpsumController extends Controller
{
    public function index()
    {
        return view('quickies.lorem-ipsum');
    }
}
