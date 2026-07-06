<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $barangId = DB::table('ms_barang')->insertGetId([
            'mb_desc' => 'Beras Dummy Premium ' . rand(1, 100),
            'mb_grade' => 'Grade A',
            'mb_stok' => 0,
            'mb_hpp' => 10000,
            'mb_profit' => 2000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $months = 6;
        for ($i = $months; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            $daysInMonth = $date->daysInMonth;

            for ($j = 0; $j < rand(3, 8); $j++) {
                $randomDate = $date->copy()->addDays(rand(0, $daysInMonth - 1));
                $qtyIn = rand(200, 600);

                DB::table('barang_masuk')->insert([
                    'bm_mb_id' => $barangId,
                    'bm_qty' => $qtyIn,
                    'bm_date' => $randomDate->format('Y-m-d'),
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);

                $qtyOut = rand(50, 150) + (($months - $i) * 30);
                $hpp = 10000;
                $profit = 2000;
                $totalHarga = ($hpp + $profit) * $qtyOut;

                DB::table('barang_keluar')->insert([
                    'bk_mb_id' => $barangId,
                    'bk_qty' => $qtyOut,
                    'bk_total_harga' => $totalHarga,
                    'bk_date' => $randomDate->format('Y-m-d'),
                    'status' => 'validated',
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);
            }
        }

        $incoming = DB::table('barang_masuk')->where('bm_mb_id', $barangId)->sum('bm_qty');
        $outgoing = DB::table('barang_keluar')
            ->where('bk_mb_id', $barangId)
            ->where('status', '!=', 'rejected')
            ->sum('bk_qty');

        DB::table('ms_barang')
            ->where('mb_id', $barangId)
            ->update(['mb_stok' => max(0, $incoming - $outgoing)]);
    }
}
