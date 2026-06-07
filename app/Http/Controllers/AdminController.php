<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\MsBarang;

class AdminController extends Controller
{
    public function index()
    {
        // 1. Ambil transaksi pending
        $pendingTransactions = \App\Models\BarangKeluar::where('status', 'pending')->get();

        // 2. Hitung total stok (Sekarang menggunakan mb_stok)
        $totalStock = \App\Models\MsBarang::sum('mb_stok');

        // 3. Hitung stok menipis
        $lowStockCount = \App\Models\MsBarang::where('mb_stok', '<', 10)->count();

        return view('admin.dashboard', compact('pendingTransactions', 'totalStock', 'lowStockCount'));
    }
}
