<?php

namespace App\Http\Controllers;

class DockerComposeController extends Controller
{
    public function index()
    {
        return view('quickies.docker-compose');
    }
}
