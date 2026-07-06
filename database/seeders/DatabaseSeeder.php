<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('barang_masuk')->truncate();
        DB::table('barang_keluar')->truncate();
        DB::table('ms_barang')->truncate();
        DB::table('ms_account')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('ms_account')->insert([
            ['ma_user' => 'admin', 'ma_pass' => 'password123', 'role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['ma_user' => 'owner', 'ma_pass' => 'password123', 'role' => 'owner', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $products = [
            ['desc' => 'Beras Pandan Wangi Premium', 'grades' => [
                'Grade A' => ['stock' => 50, 'hpp' => 12000, 'profit' => 3000],
                'Grade B' => ['stock' => 40, 'hpp' => 10000, 'profit' => 2500],
                'Grade C' => ['stock' => 30, 'hpp' => 8000, 'profit' => 2000],
            ]],
            ['desc' => 'Beras Premium Mojoputri', 'grades' => [
                'Grade A' => ['stock' => 60, 'hpp' => 13000, 'profit' => 3500],
                'Grade B' => ['stock' => 50, 'hpp' => 11000, 'profit' => 2800],
            ]],
        ];

        $barangIds = [];
        foreach ($products as $p) {
            foreach ($p['grades'] as $grade => $data) {
                $mb_id = DB::table('ms_barang')->insertGetId([
                    'mb_desc' => $p['desc'],
                    'mb_grade' => $grade,
                    'mb_stok' => $data['stock'],
                    'mb_hpp' => $data['hpp'],
                    'mb_profit' => $data['profit'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $barangIds[] = $mb_id;

                DB::table('barang_masuk')->insert([
                    'bm_mb_id' => $mb_id,
                    'bm_qty' => $data['stock'],
                    'bm_date' => today()->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $startDate = Carbon::now()->subMonths(5)->startOfMonth();
        $endDate = Carbon::now();

        for ($date = clone $startDate; $date->lte($endDate); $date->addDays(7)) {
            foreach ($barangIds as $mb_id) {
                $barang = DB::table('ms_barang')->where('mb_id', $mb_id)->first();
                if (!$barang) continue;

                $qtyMasuk = rand(10, 30);
                DB::table('barang_masuk')->insert([
                    'bm_mb_id' => $mb_id,
                    'bm_qty' => $qtyMasuk,
                    'bm_date' => $date->format('Y-m-d'),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $qtyKeluar = rand(5, 20);
                $totalHarga = ($barang->mb_hpp + $barang->mb_profit) * $qtyKeluar;
                $status = $date->gt(Carbon::now()->subDays(15)) && rand(0, 4) === 0 ? 'pending' : 'validated';

                DB::table('barang_keluar')->insert([
                    'bk_mb_id' => $mb_id,
                    'bk_qty' => $qtyKeluar,
                    'bk_total_harga' => $totalHarga,
                    'bk_date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }

        foreach ($barangIds as $mb_id) {
            $incoming = DB::table('barang_masuk')->where('bm_mb_id', $mb_id)->sum('bm_qty');
            $outgoing = DB::table('barang_keluar')
                ->where('bk_mb_id', $mb_id)
                ->where('status', '!=', 'rejected')
                ->sum('bk_qty');

            DB::table('ms_barang')
                ->where('mb_id', $mb_id)
                ->update(['mb_stok' => max(0, $incoming - $outgoing)]);
        }
    }
}
