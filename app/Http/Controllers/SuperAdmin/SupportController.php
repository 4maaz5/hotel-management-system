<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class SupportController extends Controller
{
    public function index()
    {
        return view('super_admin.support.index');
    }
}
