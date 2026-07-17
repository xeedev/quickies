<?php

namespace App\Http\Controllers;

class HttpStatusController extends Controller
{
    public function index()
    {
        return view('quickies.http-status');
    }
}
