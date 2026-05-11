<?php

namespace App\Http\Controllers\Outlets;

use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function index()
    {
        return view('admin.outlet_order.index');
    }
}
