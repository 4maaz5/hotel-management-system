<?php

namespace App\Http\Controllers\Documents;

use App\Http\Controllers\Controller;

class VerifyDocumentController extends Controller
{
    public function index()
    {
        return view('Admin.Backend.DocumentVerification.index');
    }
}
