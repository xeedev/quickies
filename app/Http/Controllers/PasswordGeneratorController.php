<?php

namespace App\Http\Controllers;

class PasswordGeneratorController extends Controller
{
    public function index()
    {
        return view('quickies.password-generator');
    }
}
