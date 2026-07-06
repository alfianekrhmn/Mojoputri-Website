<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function showLogin()
    {
        $user = session('user');
        if ($user === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $username = $request->input('email');
        $password = $request->input('password');

        $account = DB::table('ms_account')
            ->where('ma_user', $username)
            ->where('ma_pass', $password)
            ->first();

        if ($account) {
            session(['user' => $account->role]);
            if ($account->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Logged in as Admin successfully.');
            }
        }

        return back()->with('error', 'Username or Password incorrect.');
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    // ==========================================
    // ADMIN PORTAL
    // ==========================================

    public function admin(Request $request)
    {
        if (session('user') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login as Admin to access this page.');
        }

        $barang = DB::table('ms_barang')->orderBy('mb_id', 'desc')->get();

        $barangMasuk = DB::table('barang_masuk')
            ->join('ms_barang', 'barang_masuk.bm_mb_id', '=', 'ms_barang.mb_id')
            ->select('barang_masuk.*', 'ms_barang.mb_desc', 'ms_barang.mb_grade')
            ->orderBy('bm_date', 'desc')
            ->get();

        $barangKeluar = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->select('barang_keluar.*', 'ms_barang.mb_desc', 'ms_barang.mb_grade')
            ->orderBy('bk_date', 'desc')
            ->get();

        // =============================================
        // ANALYTICS DATA (merged from Owner Portal)
        // =============================================
        $startDate = $request->input('start_date') ?: Carbon::now()->subMonths(5)->startOfMonth()->format('Y-m-d');
        $endDate = $request->input('end_date') ?: Carbon::now()->format('Y-m-d');
        $gradeFilter = $request->input('grade');
        $productFilter = $request->input('product');

        $salesQuery = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->whereBetween('bk_date', [$startDate, $endDate]);

        $incomingQuery = DB::table('barang_masuk')
            ->join('ms_barang', 'barang_masuk.bm_mb_id', '=', 'ms_barang.mb_id')
            ->whereBetween('bm_date', [$startDate, $endDate]);

        if ($gradeFilter) {
            $salesQuery->where('ms_barang.mb_grade', $gradeFilter);
            $incomingQuery->where('ms_barang.mb_grade', $gradeFilter);
        }

        if ($productFilter) {
            $salesQuery->where('ms_barang.mb_id', $productFilter);
            $incomingQuery->where('ms_barang.mb_id', $productFilter);
        }

        $totalSales = (clone $salesQuery)
            ->where('barang_keluar.status', 'validated')
            ->sum('barang_keluar.bk_total_harga');

        $grossMarginData = (clone $salesQuery)
            ->where('barang_keluar.status', 'validated')
            ->select(
                DB::raw('SUM(ms_barang.mb_profit * barang_keluar.bk_qty) as total_profit'),
                DB::raw('SUM(ms_barang.mb_hpp * barang_keluar.bk_qty) as total_hpp')
            )
            ->first();
        $grossMargin = $grossMarginData->total_profit ?? 0;
        $totalHpp = $grossMarginData->total_hpp ?? 0;

        $totalStockIn = (clone $incomingQuery)->sum('barang_masuk.bm_qty');
        $totalStockOut = (clone $salesQuery)->sum('barang_keluar.bk_qty');
        $netUnitsAvailable = $totalStockIn - $totalStockOut;

        $totalInventoryCost = (clone $incomingQuery)
            ->select(DB::raw('SUM(ms_barang.mb_hpp * barang_masuk.bm_qty) as total_inv_cost'))
            ->first()->total_inv_cost ?? 0;
            
        $inventoryTurnover = $totalStockIn > 0 ? round(($totalStockOut / $totalStockIn) * 100, 1) : 0;

        $monthlySalesQuery = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->select(
                DB::raw("DATE_FORMAT(bk_date, '%Y-%m') as month"),
                DB::raw("SUM(bk_total_harga) as total_sales"),
                DB::raw("SUM(ms_barang.mb_hpp * barang_keluar.bk_qty) as total_hpp"),
                DB::raw("SUM(ms_barang.mb_profit * barang_keluar.bk_qty) as total_profit")
            )
            ->where('barang_keluar.status', 'validated')
            ->whereBetween('bk_date', [$startDate, $endDate]);

        if ($gradeFilter) $monthlySalesQuery->where('ms_barang.mb_grade', $gradeFilter);
        if ($productFilter) $monthlySalesQuery->where('ms_barang.mb_id', $productFilter);

        $salesTrend = $monthlySalesQuery->groupBy('month')->orderBy('month', 'asc')->get();

        $stockInMonthlyQuery = DB::table('barang_masuk')
            ->join('ms_barang', 'barang_masuk.bm_mb_id', '=', 'ms_barang.mb_id')
            ->select(DB::raw("DATE_FORMAT(bm_date, '%Y-%m') as month"), DB::raw("SUM(bm_qty) as total_in"))
            ->whereBetween('bm_date', [$startDate, $endDate]);
        if ($gradeFilter) $stockInMonthlyQuery->where('ms_barang.mb_grade', $gradeFilter);
        if ($productFilter) $stockInMonthlyQuery->where('ms_barang.mb_id', $productFilter);
        $stockInMonthlyData = $stockInMonthlyQuery->groupBy('month')->orderBy('month', 'asc')->get()->pluck('total_in', 'month')->toArray();

        $stockOutMonthlyQuery = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->select(DB::raw("DATE_FORMAT(bk_date, '%Y-%m') as month"), DB::raw("SUM(bk_qty) as total_out"))
            ->where('status', '!=', 'rejected')
            ->whereBetween('bk_date', [$startDate, $endDate]);
        if ($gradeFilter) $stockOutMonthlyQuery->where('ms_barang.mb_grade', $gradeFilter);
        if ($productFilter) $stockOutMonthlyQuery->where('ms_barang.mb_id', $productFilter);
        $stockOutMonthlyData = $stockOutMonthlyQuery->groupBy('month')->orderBy('month', 'asc')->get()->pluck('total_out', 'month')->toArray();

        $allMonths = array_unique(array_merge(array_keys($stockInMonthlyData), array_keys($stockOutMonthlyData)));
        sort($allMonths);

        $chartLabels = [];
        $chartSales = [];
        $chartStockIn = [];
        $chartStockOut = [];
        $chartHPP = [];
        $chartProfit = [];

        foreach ($allMonths as $month) {
            $chartLabels[] = Carbon::parse($month . '-01')->format('M Y');
            $chartStockIn[] = $stockInMonthlyData[$month] ?? 0;
            $chartStockOut[] = $stockOutMonthlyData[$month] ?? 0;

            $salesVal = 0;
            $hppVal = 0;
            $profitVal = 0;
            foreach ($salesTrend as $s) {
                if ($s->month === $month) {
                    $salesVal = $s->total_sales;
                    $hppVal = $s->total_hpp;
                    $profitVal = $s->total_profit;
                    break;
                }
            }
            $chartSales[] = $salesVal;
            $chartHPP[] = $hppVal;
            $chartProfit[] = $profitVal;
        }

        // Forecasting
        $forecastingQuery = DB::table('barang_keluar')
            ->select(DB::raw("DATE_FORMAT(bk_date, '%Y-%m') as month"), DB::raw("SUM(bk_qty) as total_qty"))
            ->where('status', 'validated')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $n = count($forecastingQuery);
        $slope = 0;
        $forecastedQty = 0;
        $trendDirection = 'Stable';

        if ($n >= 2) {
            $sumX = $sumY = $sumXY = $sumX2 = 0;
            foreach ($forecastingQuery as $index => $data) {
                $x = $index + 1;
                $y = $data->total_qty;
                $sumX += $x;
                $sumY += $y;
                $sumXY += ($x * $y);
                $sumX2 += ($x * $x);
            }
            $denominator = ($n * $sumX2) - ($sumX * $sumX);
            if ($denominator != 0) {
                $slope = (($n * $sumXY) - ($sumX * $sumY)) / $denominator;
                $intercept = ($sumY - ($slope * $sumX)) / $n;
            } else {
                $slope = 0;
                $intercept = $sumY / $n;
            }
            $forecastedQty = max(0, round($slope * ($n + 1) + $intercept));
            if ($slope > 0.1) $trendDirection = 'Upward Growth';
            elseif ($slope < -0.1) $trendDirection = 'Downward Decline';
        } else {
            $avgQty = DB::table('barang_keluar')->where('status', 'validated')->avg('bk_qty') ?: 0;
            $forecastedQty = round($avgQty * 5);
        }

        $analyticsTransactions = (clone $salesQuery)
            ->select('barang_keluar.*', 'ms_barang.mb_desc', 'ms_barang.mb_grade')
            ->orderBy('bk_date', 'desc')
            ->get();

        $products = DB::table('ms_barang')->select('mb_id', 'mb_desc', 'mb_grade')->orderBy('mb_desc')->get();

        $availableGrades = DB::table('ms_barang')
            ->distinct()
            ->orderBy('mb_grade')
            ->pluck('mb_grade');

        $gradeDistributionQuery = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->select('ms_barang.mb_grade', DB::raw('SUM(bk_qty) as total_qty'))
            ->where('status', 'validated')
            ->whereBetween('bk_date', [$startDate, $endDate]);

        if ($productFilter) $gradeDistributionQuery->where('ms_barang.mb_id', $productFilter);
        if ($gradeFilter) $gradeDistributionQuery->where('ms_barang.mb_grade', $gradeFilter);

        $gradeDistribution = $gradeDistributionQuery->groupBy('ms_barang.mb_grade')->get();

        return view('admin', compact(
            'barang', 'barangMasuk', 'barangKeluar',
            'totalSales', 'grossMargin', 'totalHpp', 'totalStockIn', 'totalStockOut', 'netUnitsAvailable',
            'totalInventoryCost', 'inventoryTurnover',
            'chartLabels', 'chartSales', 'chartStockIn', 'chartStockOut', 'chartHPP', 'chartProfit',
            'salesTrend', 'slope', 'forecastedQty', 'trendDirection',
            'analyticsTransactions', 'startDate', 'endDate', 'gradeFilter', 'productFilter',
            'products', 'gradeDistribution', 'availableGrades'
        ));
    }

    public function getBarangList()
    {
        $barang = DB::table('ms_barang')
            ->select('mb_id', 'mb_desc', 'mb_grade', 'mb_stok', 'mb_hpp', 'mb_profit')
            ->orderBy('mb_desc')
            ->get();

        return response()->json($barang);
    }

    // --- CRUD: Master Barang ---

    public function storeBarang(Request $request)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $mb_desc = trim($request->input('mb_desc'));
        $grades = $request->input('grades', []);
        $stocks = $request->input('stock', []);
        $hpps = $request->input('hpp', []);
        $profits = $request->input('profit', []);

        $inserted = 0;

        foreach ($grades as $index => $grade) {
            $grade = trim($grade);
            if (empty($grade)) continue;

            $stock = (int) ($stocks[$index] ?? 0);
            $hpp = (float) ($hpps[$index] ?? 0);
            $profit = (float) ($profits[$index] ?? 0);

            $mb_id = DB::table('ms_barang')->insertGetId([
                'mb_desc' => $mb_desc,
                'mb_grade' => $grade,
                'mb_stok' => $stock,
                'mb_hpp' => $hpp,
                'mb_profit' => $profit,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($stock > 0) {
                DB::table('barang_masuk')->insert([
                    'bm_mb_id' => $mb_id,
                    'bm_qty' => $stock,
                    'bm_date' => today()->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $inserted++;
        }

        if ($inserted === 0) {
            return back()->with('error', 'Minimal satu grade harus diisi.');
        }

        return back()->with('success', 'Produk berhasil ditambahkan (' . $inserted . ' varian grade).');
    }

    public function updateBarang(Request $request, $id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        DB::table('ms_barang')
            ->where('mb_id', $id)
            ->update([
                'mb_desc' => trim($request->input('mb_desc')),
                'mb_grade' => trim($request->input('mb_grade')),
                'mb_hpp' => (float) $request->input('mb_hpp', 0),
                'mb_profit' => (float) $request->input('mb_profit', 0),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function deleteBarang($id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        DB::table('barang_masuk')->where('bm_mb_id', $id)->delete();
        DB::table('barang_keluar')->where('bk_mb_id', $id)->delete();
        DB::table('ms_barang')->where('mb_id', $id)->delete();

        return back()->with('success', 'Produk dan seluruh riwayat transaksi terkait berhasil dihapus.');
    }

    // --- CRUD: Barang Masuk ---

    public function storeBarangMasuk(Request $request)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $mb_id = (int) $request->input('bm_mb_id');
        $qty = (int) $request->input('bm_qty');

        if (!DB::table('ms_barang')->where('mb_id', $mb_id)->exists()) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        DB::table('barang_masuk')->insert([
            'bm_mb_id' => $mb_id,
            'bm_qty' => $qty,
            'bm_date' => $request->input('bm_date') ?: today()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->recalculateStock($mb_id);

        return back()->with('success', 'Barang masuk berhasil dicatat.');
    }

    public function updateBarangMasuk(Request $request, $id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $transaction = DB::table('barang_masuk')->where('bm_id', $id)->first();
        if (!$transaction) {
            return back()->with('error', 'Transaksi tidak ditemukan.');
        }

        $old_mb_id = $transaction->bm_mb_id;
        $new_mb_id = (int) $request->input('bm_mb_id');
        $qty = (int) $request->input('bm_qty');

        DB::table('barang_masuk')
            ->where('bm_id', $id)
            ->update([
                'bm_mb_id' => $new_mb_id,
                'bm_qty' => $qty,
                'bm_date' => $request->input('bm_date'),
                'updated_at' => now(),
            ]);

        $this->recalculateStock($new_mb_id);
        if ($old_mb_id !== $new_mb_id) {
            $this->recalculateStock($old_mb_id);
        }

        return back()->with('success', 'Transaksi barang masuk berhasil diperbarui.');
    }

    public function deleteBarangMasuk($id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $transaction = DB::table('barang_masuk')->where('bm_id', $id)->first();
        if ($transaction) {
            DB::table('barang_masuk')->where('bm_id', $id)->delete();
            $this->recalculateStock($transaction->bm_mb_id);
        }

        return back()->with('success', 'Transaksi barang masuk berhasil dihapus.');
    }

    // --- CRUD: Barang Keluar ---

    public function storeBarangKeluar(Request $request)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $mb_id = (int) $request->input('bk_mb_id');
        $qty = (int) $request->input('bk_qty');

        $barang = DB::table('ms_barang')->where('mb_id', $mb_id)->first();
        if (!$barang) {
            return back()->with('error', 'Produk tidak ditemukan.');
        }

        if ($barang->mb_stok < $qty) {
            return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $barang->mb_stok . ' Kg.');
        }

        $totalHarga = ($barang->mb_hpp + $barang->mb_profit) * $qty;

        DB::table('barang_keluar')->insert([
            'bk_mb_id' => $mb_id,
            'bk_qty' => $qty,
            'bk_total_harga' => $totalHarga,
            'bk_date' => $request->input('bk_date') ?: today()->format('Y-m-d'),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->recalculateStock($mb_id);

        return back()->with('success', 'Penjualan berhasil dicatat. Status: Menunggu Validasi.');
    }

    public function updateBarangKeluar(Request $request, $id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $transaction = DB::table('barang_keluar')->where('bk_id', $id)->first();
        if (!$transaction) {
            return back()->with('error', 'Transaksi tidak ditemukan.');
        }

        $old_mb_id = $transaction->bk_mb_id;
        $mb_id = (int) $request->input('bk_mb_id');
        $qty = (int) $request->input('bk_qty');

        $barang = DB::table('ms_barang')->where('mb_id', $mb_id)->first();
        $totalHarga = $barang ? ($barang->mb_hpp + $barang->mb_profit) * $qty : 0;

        DB::table('barang_keluar')
            ->where('bk_id', $id)
            ->update([
                'bk_mb_id' => $mb_id,
                'bk_qty' => $qty,
                'bk_total_harga' => $totalHarga,
                'bk_date' => $request->input('bk_date'),
                'updated_at' => now(),
            ]);

        $this->recalculateStock($mb_id);
        if ($old_mb_id !== $mb_id) {
            $this->recalculateStock($old_mb_id);
        }

        return back()->with('success', 'Transaksi penjualan berhasil diperbarui.');
    }

    public function deleteBarangKeluar($id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $transaction = DB::table('barang_keluar')->where('bk_id', $id)->first();
        if ($transaction) {
            DB::table('barang_keluar')->where('bk_id', $id)->delete();
            $this->recalculateStock($transaction->bk_mb_id);
        }

        return back()->with('success', 'Transaksi penjualan berhasil dihapus.');
    }

    public function validateTransaction($id, $status)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        if (!in_array($status, ['pending', 'validated', 'rejected'])) {
            return back()->with('error', 'Status tidak valid.');
        }

        DB::table('barang_keluar')
            ->where('bk_id', $id)
            ->update(['status' => $status, 'updated_at' => now()]);

        $transaction = DB::table('barang_keluar')->where('bk_id', $id)->first();
        if ($transaction) {
            $this->recalculateStock($transaction->bk_mb_id);
        }

        return back()->with('success', 'Status transaksi diperbarui menjadi ' . ucfirst($status) . '.');
    }

    private function recalculateStock($mb_id)
    {
        $incoming = DB::table('barang_masuk')->where('bm_mb_id', $mb_id)->sum('bm_qty');
        $outgoing = DB::table('barang_keluar')
            ->where('bk_mb_id', $mb_id)
            ->where('status', '!=', 'rejected')
            ->sum('bk_qty');

        DB::table('ms_barang')
            ->where('mb_id', $mb_id)
            ->update(['mb_stok' => max(0, $incoming - $outgoing), 'updated_at' => now()]);
    }

    public function exportCsv(Request $request)
    {
        if (session('user') !== 'admin') {
            return redirect()->route('login')->with('error', 'Unauthorized.');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $gradeFilter = $request->input('grade');
        $productFilter = $request->input('product');

        $query = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->select(
                'barang_keluar.bk_id', 'ms_barang.mb_desc', 'ms_barang.mb_grade',
                'barang_keluar.bk_qty', 'barang_keluar.bk_date',
                'ms_barang.mb_hpp', 'ms_barang.mb_profit',
                'barang_keluar.bk_total_harga', 'barang_keluar.status'
            )
            ->orderBy('bk_date', 'desc');

        if ($startDate && $endDate) $query->whereBetween('bk_date', [$startDate, $endDate]);
        if ($gradeFilter) $query->where('ms_barang.mb_grade', $gradeFilter);
        if ($productFilter) $query->where('ms_barang.mb_id', $productFilter);

        $data = $query->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=Sales_Report_CV_Mojoputri.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Transaction ID', 'Product Description', 'Grade', 'Quantity Sold (Kg)', 'Sales Date', 'HPP/Kg', 'Profit/Kg', 'Total Value (IDR)', 'Status'];

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($data as $row) {
                fputcsv($file, [
                    $row->bk_id,
                    $row->mb_desc,
                    $row->mb_grade,
                    $row->bk_qty,
                    $row->bk_date,
                    $row->mb_hpp,
                    $row->mb_profit,
                    $row->bk_total_harga,
                    ucfirst($row->status),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
