<?php

namespace App\Http\Controllers;

class JsonFormatterController extends Controller
{
    public function index()
    {
        return view('quickies.json-formatter');
    }
}
