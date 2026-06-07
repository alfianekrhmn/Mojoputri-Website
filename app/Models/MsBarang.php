<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsBarang extends Model
{
    protected $table = 'ms_barang';
    protected $primaryKey = 'mb_id'; // Sesuai dengan SQL: mb_id

    // Jika ingin memudahkan pemanggilan, kamu bisa buat accessor
    public function getStokAttribute()
    {
        return $this->attributes['mb_stok'];
    }
}
