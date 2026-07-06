<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsBarang extends Model
{
    protected $table = 'ms_barang';
    protected $primaryKey = 'mb_id';

    protected $fillable = ['mb_desc', 'mb_grade', 'mb_stok', 'mb_hpp', 'mb_profit'];

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'bm_mb_id', 'mb_id');
    }

    public function barangKeluar()
    {
        return $this->hasMany(BarangKeluar::class, 'bk_mb_id', 'mb_id');
    }
}
