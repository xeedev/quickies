<?php

namespace App\Http\Controllers;

class PasswordStrengthController extends Controller
{
    public function index()
    {
        return view('quickies.password-strength');
    }
}
