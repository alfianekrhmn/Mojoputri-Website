<?php

namespace App\Models;

// Gunakan Authenticatable, bukan Model biasa agar bisa login
use Illuminate\Foundation\Auth\User as Authenticatable;

class MsAccount extends Authenticatable
{
    protected $table = 'ms_account'; // WAJIB: Agar tidak mencari ke tabel 'users'
    protected $primaryKey = 'ma_id';

    protected $fillable = [
        'ma_user',
        'ma_pass',
        'role',
    ];

    // Beritahu Laravel kolom password-nya adalah ma_pass (bukan password)
    public function getAuthPassword()
    {
        return $this->ma_pass;
    }
}
