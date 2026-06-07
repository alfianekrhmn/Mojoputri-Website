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
        DB::table('product_grades')->truncate();
        DB::table('ms_barang')->truncate();
        DB::table('ms_account')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('ms_account')->insert([
            ['ma_user' => 'admin', 'ma_pass' => 'password123', 'role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['ma_user' => 'owner', 'ma_pass' => 'password123', 'role' => 'owner', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $products = [
            ['desc' => 'Beras Pandan Wangi Premium', 'grades' => ['A' => ['stock' => 50, 'hpp' => 12000, 'profit' => 3000], 'B' => ['stock' => 40, 'hpp' => 10000, 'profit' => 2500], 'C' => ['stock' => 30, 'hpp' => 8000, 'profit' => 2000]]],
            ['desc' => 'Beras Premium Mojoputri', 'grades' => ['A' => ['stock' => 60, 'hpp' => 13000, 'profit' => 3500], 'B' => ['stock' => 50, 'hpp' => 11000, 'profit' => 2800], 'C' => ['stock' => 40, 'hpp' => 9000, 'profit' => 2200]]],
            ['desc' => 'Beras Sentra Ramos Premium', 'grades' => ['A' => ['stock' => 45, 'hpp' => 11500, 'profit' => 2800], 'B' => ['stock' => 55, 'hpp' => 9500, 'profit' => 2300], 'C' => ['stock' => 35, 'hpp' => 7500, 'profit' => 1800]]],
        ];

        $barangIds = [];
        foreach ($products as $p) {
            $totalStock = array_sum(array_column($p['grades'], 'stock'));
            $mb_id = DB::table('ms_barang')->insertGetId([
                'mb_desc' => $p['desc'],
                'mb_grade' => '-',
                'mb_stok' => $totalStock,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $barangIds[] = $mb_id;

            foreach ($p['grades'] as $grade => $data) {
                DB::table('product_grades')->insert([
                    'pg_mb_id' => $mb_id,
                    'pg_grade' => $grade,
                    'pg_stock' => $data['stock'],
                    'pg_hpp' => $data['hpp'],
                    'pg_profit' => $data['profit'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('barang_masuk')->insert([
                    'bm_mb_id' => $mb_id,
                    'bm_grade' => $grade,
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
                $gradeRow = DB::table('product_grades')->where('pg_mb_id', $mb_id)->inRandomOrder()->first();
                if (!$gradeRow) continue;

                $qtyMasuk = rand(10, 30);
                DB::table('barang_masuk')->insert([
                    'bm_mb_id' => $mb_id,
                    'bm_grade' => $gradeRow->pg_grade,
                    'bm_qty' => $qtyMasuk,
                    'bm_date' => $date->format('Y-m-d'),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $qtyKeluar = rand(5, 20);
                $totalHarga = ($gradeRow->pg_hpp + $gradeRow->pg_profit) * $qtyKeluar;
                $status = $date->gt(Carbon::now()->subDays(15)) && rand(0, 4) === 0 ? 'pending' : 'validated';

                DB::table('barang_keluar')->insert([
                    'bk_mb_id' => $mb_id,
                    'bk_grade' => $gradeRow->pg_grade,
                    'bk_qty' => $qtyKeluar,
                    'bk_hpp' => $gradeRow->pg_hpp,
                    'bk_profit' => $gradeRow->pg_profit,
                    'bk_date' => $date->format('Y-m-d'),
                    'bk_total_harga' => $totalHarga,
                    'status' => $status,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }
        }
    }
}
