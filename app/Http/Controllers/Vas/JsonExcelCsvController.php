<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class JsonExcelCsvController extends Controller
{
    public function index()
    {
        return view('quickies.vas.json-excel-csv');
    }
}
