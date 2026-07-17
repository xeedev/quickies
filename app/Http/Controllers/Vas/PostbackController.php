<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class PostbackController extends Controller
{
    public function index()
    {
        return view('quickies.vas.postback');
    }
}
