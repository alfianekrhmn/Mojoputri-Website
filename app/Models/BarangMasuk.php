<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';
    protected $primaryKey = 'bm_id';

    protected $fillable = ['bm_mb_id', 'bm_qty', 'bm_date'];

    public function barang()
    {
        return $this->belongsTo(MsBarang::class, 'bm_mb_id', 'mb_id');
    }
}
