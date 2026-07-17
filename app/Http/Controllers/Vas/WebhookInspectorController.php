<?php

namespace App\Http\Controllers\Vas;

use App\Http\Controllers\Controller;

class WebhookInspectorController extends Controller
{
    public function index()
    {
        return view('quickies.vas.webhook-inspector');
    }
}
