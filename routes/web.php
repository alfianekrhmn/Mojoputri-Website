<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Main Gateway redirection
Route::get('/', function () {
    $user = session('user');
    if ($user === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user === 'owner') {
        return redirect()->route('owner.dashboard');
    }
    return redirect()->route('login');
});

// Simple Session Authentication
Route::get('/login', [DashboardController::class, 'showLogin'])->name('login');
Route::post('/login', [DashboardController::class, 'login']);
Route::get('/logout', [DashboardController::class, 'logout'])->name('logout');
Route::post('/logout', [DashboardController::class, 'logout'])->name('logout.post');

// Admin Panel Routes
Route::prefix('admin')->group(function () {
    Route::get('/', [DashboardController::class, 'admin'])->name('admin.dashboard');
    
    // CRUD - Master Barang
    Route::post('/barang/store', [DashboardController::class, 'storeBarang'])->name('admin.barang.store');
    Route::post('/barang/update/{id}', [DashboardController::class, 'updateBarang'])->name('admin.barang.update');
    Route::post('/barang/delete/{id}', [DashboardController::class, 'deleteBarang'])->name('admin.barang.delete');
    
    // CRUD - Barang Masuk
    Route::post('/barang-masuk/store', [DashboardController::class, 'storeBarangMasuk'])->name('admin.masuk.store');
    Route::post('/barang-masuk/update/{id}', [DashboardController::class, 'updateBarangMasuk'])->name('admin.masuk.update');
    Route::post('/barang-masuk/delete/{id}', [DashboardController::class, 'deleteBarangMasuk'])->name('admin.masuk.delete');

    // CRUD - Barang Keluar
    Route::post('/barang-keluar/store', [DashboardController::class, 'storeBarangKeluar'])->name('admin.keluar.store');
    Route::post('/barang-keluar/update/{id}', [DashboardController::class, 'updateBarangKeluar'])->name('admin.keluar.update');
    Route::post('/barang-keluar/delete/{id}', [DashboardController::class, 'deleteBarangKeluar'])->name('admin.keluar.delete');

    // Validation Status
    Route::post('/barang-keluar/validate/{id}/{status}', [DashboardController::class, 'validateTransaction'])->name('admin.keluar.validate');

    // AJAX - Get grades by product
    Route::get('/barang/grades/{id}', [DashboardController::class, 'getGradesByProduct'])->name('admin.barang.grades');

    // AJAX - Get all unique product names (for barang masuk autocomplete)
    Route::get('/barang/list', [DashboardController::class, 'getBarangList'])->name('admin.barang.list');
});

// Owner Panel Routes
Route::prefix('owner')->group(function () {
    Route::get('/', [DashboardController::class, 'owner'])->name('owner.dashboard');
    Route::get('/export', [DashboardController::class, 'exportCsv'])->name('owner.export');
});
