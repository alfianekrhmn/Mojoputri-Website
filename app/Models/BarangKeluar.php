<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';
    protected $primaryKey = 'bk_id';

    protected $fillable = ['bk_mb_id', 'bk_qty', 'bk_date', 'bk_total_harga', 'status'];

    public function barang()
    {
        return $this->belongsTo(MsBarang::class, 'bk_mb_id', 'mb_id');
    }
}
