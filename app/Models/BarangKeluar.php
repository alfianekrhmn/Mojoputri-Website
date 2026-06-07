<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    // Pastikan mengarah ke tabel barang_keluar
    protected $table = 'barang_keluar';

    // Sesuaikan primary key jika bukan 'id'
    protected $primaryKey = 'bk_id';

    protected $fillable = [
        'bk_mb_id', 'bk_qty', 'bk_date', 'bk_total_harga', 'status'
    ];
}
