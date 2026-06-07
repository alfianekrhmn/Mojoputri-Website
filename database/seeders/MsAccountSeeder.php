<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MsAccount; // BARIS INI WAJIB ADA
use Illuminate\Support\Facades\Hash;

class MsAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama agar tidak duplikat saat dijalankan ulang
        MsAccount::truncate();

        MsAccount::create([
            'ma_user' => 'admin@company.com',
            'ma_pass' => Hash::make('password123'),
            'role'    => 'admin'
        ]);

        MsAccount::create([
            'ma_user' => 'owner@company.com',
            'ma_pass' => Hash::make('password123'),
            'role'    => 'owner'
        ]);
    }
}
