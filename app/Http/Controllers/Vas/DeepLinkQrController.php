<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class DeepLinkQrController extends Controller
{
    public function index()
    {
        return view('quickies.vas.deep-link-qr');
    }
}
