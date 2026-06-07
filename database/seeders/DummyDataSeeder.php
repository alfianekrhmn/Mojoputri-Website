<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // We'll create a new master product and attach dummy data to it.
        $barangId = DB::table('ms_barang')->insertGetId([
            'mb_desc' => 'Beras Dummy Premium ' . rand(1, 100),
            'mb_grade' => '-',
            'mb_stok' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $grades = ['Grade A', 'Grade B'];
        
        foreach ($grades as $grade) {
            DB::table('product_grades')->insert([
                'pg_mb_id' => $barangId,
                'pg_grade' => $grade,
                'pg_stock' => 0,
                'pg_hpp' => 10000,
                'pg_profit' => 2000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Generate data for the past 6 months to create a prediction trend
        $months = 6;
        for ($i = $months; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            $daysInMonth = $date->daysInMonth;
            
            // Randomly insert a few incoming and outgoing records per month
            for ($j = 0; $j < rand(3, 8); $j++) {
                $randomDate = $date->copy()->addDays(rand(0, $daysInMonth - 1));
                $grade = $grades[array_rand($grades)];
                $qtyIn = rand(200, 600);

                DB::table('barang_masuk')->insert([
                    'bm_mb_id' => $barangId,
                    'bm_grade' => $grade,
                    'bm_qty' => $qtyIn,
                    'bm_date' => $randomDate->format('Y-m-d'),
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);

                // Generate some sales that are growing slightly over time to show trend in prediction
                $qtyOut = rand(50, 150) + (($months - $i) * 30); // slight upward trend

                $hpp = 10000;
                $profit = 2000;
                $totalHarga = ($hpp + $profit) * $qtyOut;

                DB::table('barang_keluar')->insert([
                    'bk_mb_id' => $barangId,
                    'bk_grade' => $grade,
                    'bk_qty' => $qtyOut,
                    'bk_hpp' => $hpp,
                    'bk_profit' => $profit,
                    'bk_date' => $randomDate->format('Y-m-d'),
                    'bk_total_harga' => $totalHarga,
                    'status' => 'validated',
                    'created_at' => $randomDate,
                    'updated_at' => $randomDate,
                ]);
            }
        }
        
        // Recalculate stock accurately at the end
        foreach ($grades as $grade) {
            $incoming = DB::table('barang_masuk')->where('bm_mb_id', $barangId)->where('bm_grade', $grade)->sum('bm_qty');
            $outgoing = DB::table('barang_keluar')->where('bk_mb_id', $barangId)->where('bk_grade', $grade)->where('status', 'validated')->sum('bk_qty');
            
            $net = max(0, $incoming - $outgoing);
            DB::table('product_grades')
                ->where('pg_mb_id', $barangId)
                ->where('pg_grade', $grade)
                ->update(['pg_stock' => $net]);
        }
        
        $totalStock = DB::table('product_grades')->where('pg_mb_id', $barangId)->sum('pg_stock');
        DB::table('ms_barang')->where('mb_id', $barangId)->update(['mb_stok' => $totalStock]);
    }
}
