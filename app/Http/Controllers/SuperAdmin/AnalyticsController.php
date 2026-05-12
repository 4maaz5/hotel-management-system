<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('super_admin.analytics.index');
    }
}
