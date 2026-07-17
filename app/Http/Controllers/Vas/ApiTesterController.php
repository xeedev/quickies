<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class ApiTesterController extends Controller
{
    public function index()
    {
        return view('quickies.vas.api-tester');
    }
}
