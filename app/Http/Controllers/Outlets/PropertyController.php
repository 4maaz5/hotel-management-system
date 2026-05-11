<?php

namespace App\Http\Controllers\Outlets;

use App\Http\Controllers\Controller;
use App\Models\OutletSetup;

class PropertyController extends Controller
{
    public function index()
    {
        $outlets = OutletSetup::orderBy('created_at', 'desc')->get();
        return view('admin.outlet_property.index', compact('outlets'));
    }
}
