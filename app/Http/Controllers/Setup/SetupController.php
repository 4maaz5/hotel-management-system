<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;

class SetupController extends Controller
{
    public function index()
    {
        return view('layouts.setup');
    }
}
