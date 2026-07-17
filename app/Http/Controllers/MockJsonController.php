<?php

namespace App\Http\Controllers;

class MockJsonController extends Controller
{
    public function index()
    {
        return view('quickies.mock-json');
    }
}
