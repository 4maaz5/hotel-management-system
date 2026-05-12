<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    public function index()
    {
        return view('super_admin.activity.index');
    }
}
