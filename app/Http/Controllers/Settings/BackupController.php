<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;

class BackupController extends Controller
{
    public function index()
    {
        return view('Admin.Backend.Backup.index');
    }
}
