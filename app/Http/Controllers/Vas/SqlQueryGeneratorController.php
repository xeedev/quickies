<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class SqlQueryGeneratorController extends Controller
{
    public function index()
    {
        return view('quickies.vas.sql-query-generator');
    }
}
