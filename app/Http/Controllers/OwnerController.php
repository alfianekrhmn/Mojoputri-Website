<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function index()
    {
        return "Selamat Datang di Portal Owner";
        // Nanti ganti dengan: return view('owner.dashboard');
    }
}
