<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // ==========================================
    // AUTHENTICATION LOGIC
    // ==========================================

    public function showLogin()
    {
        // Redirect if already logged in
        $user = session('user');
        if ($user === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user === 'owner') {
            return redirect()->route('owner.dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $username = $request->input('email'); // can accept email or simple username
        $password = $request->input('password');

        // Direct DB search (no hashing as requested for ultra fast deployment)
        $account = DB::table('ms_account')
            ->where('ma_user', $username)
            ->where('ma_pass', $password)
            ->first();

        if ($account) {
            session(['user' => $account->role]);
            if ($account->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Logged in as Admin successfully.');
            } elseif ($account->role === 'owner') {
                return redirect()->route('owner.dashboard')->with('success', 'Logged in as Owner successfully.');
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
    // ADMIN PORTAL & CRUD OPERATIONS
    // ==========================================

    public function admin()
    {
        if (session('user') !== 'admin') {
            return redirect()->route('login')->with('error', 'Please login as Admin to access this page.');
        }

        // Fetch products with their grades
        $barang = DB::table('ms_barang')
            ->orderBy('mb_id', 'desc')
            ->get();

        // Attach grades to each product (auto-backfill legacy products)
        foreach ($barang as $b) {
            $grades = DB::table('product_grades')
                ->where('pg_mb_id', $b->mb_id)
                ->get();

            if ($grades->count() === 0) {
                foreach (['A', 'B', 'C'] as $grade) {
                    DB::table('product_grades')->insert([
                        'pg_mb_id' => $b->mb_id,
                        'pg_grade' => $grade,
                        'pg_stock' => 0,
                        'pg_hpp' => 0,
                        'pg_profit' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $grades = DB::table('product_grades')
                    ->where('pg_mb_id', $b->mb_id)
                    ->get();
            }

            $b->grades = $grades;
            $gradeTotal = $b->grades->sum('pg_stock');
            if ($gradeTotal > 0 || $b->grades->count() > 0) {
                $b->mb_stok = $gradeTotal;
            }
        }

        // Fetch stock in with grade info
        $barangMasuk = DB::table('barang_masuk')
            ->join('ms_barang', 'barang_masuk.bm_mb_id', '=', 'ms_barang.mb_id')
            ->select('barang_masuk.*', 'ms_barang.mb_desc')
            ->orderBy('bm_date', 'desc')
            ->get();

        // Fetch stock out (sales) with grade info
        $barangKeluar = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->select('barang_keluar.*', 'ms_barang.mb_desc')
            ->orderBy('bk_date', 'desc')
            ->get();

        $productsData = $barang->map(function ($b) {
            return [
                'id' => $b->mb_id,
                'desc' => $b->mb_desc,
                'grades' => collect($b->grades)->map(function ($g) {
                    return [
                        'grade' => $g->pg_grade,
                        'stock' => $g->pg_stock,
                        'hpp' => (float) $g->pg_hpp,
                        'profit' => (float) $g->pg_profit,
                    ];
                })->values(),
            ];
        })->values();

        return view('admin', compact('barang', 'barangMasuk', 'barangKeluar', 'productsData'));
    }

    // --- AJAX: Get grades for a product ---

    public function getGradesByProduct($id)
    {
        $grades = DB::table('product_grades')
            ->where('pg_mb_id', $id)
            ->get();

        return response()->json($grades);
    }

    public function getBarangList()
    {
        $barang = DB::table('ms_barang')
            ->select('mb_id', 'mb_desc')
            ->orderBy('mb_desc')
            ->get()
            ->unique('mb_desc');

        return response()->json($barang->values());
    }

    // --- CRUD: Master Barang ---

    public function storeBarang(Request $request)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $mb_desc = trim($request->input('mb_desc'));

        // Create main product entry
        $mb_id = DB::table('ms_barang')->insertGetId([
            'mb_desc' => $mb_desc,
            'mb_grade' => '-', // Grade is now managed in product_grades
            'mb_stok' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $totalStock = 0;

        $grades = $request->input('grades', []);
        $stocks = $request->input('stock', []);
        $hpps = $request->input('hpp', []);
        $profits = $request->input('profit', []);

        // Insert custom grades with initial stock and pricing
        foreach ($grades as $index => $grade) {
            $grade = trim($grade);
            if (empty($grade)) continue;

            $stock = (int) ($stocks[$index] ?? 0);
            $hpp = (float) ($hpps[$index] ?? 0);
            $profit = (float) ($profits[$index] ?? 0);

            DB::table('product_grades')->insert([
                'pg_mb_id' => $mb_id,
                'pg_grade' => $grade,
                'pg_stock' => $stock,
                'pg_hpp' => $hpp,
                'pg_profit' => $profit,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // If initial stock > 0, create a barang_masuk entry as initial stock record
            if ($stock > 0) {
                DB::table('barang_masuk')->insert([
                    'bm_mb_id' => $mb_id,
                    'bm_grade' => $grade,
                    'bm_berat' => null,
                    'bm_jumlah' => null,
                    'bm_qty' => $stock,
                    'bm_date' => today()->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            $totalStock += $stock;
        }

        // Update total stock
        DB::table('ms_barang')->where('mb_id', $mb_id)->update([
            'mb_stok' => $totalStock,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan dengan stok awal per grade.');
    }

    public function updateBarang(Request $request, $id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        DB::table('ms_barang')
            ->where('mb_id', $id)
            ->update([
                'mb_desc' => $request->input('mb_desc'),
                'updated_at' => now()
            ]);

        // Note: For update, we assume grades are mostly static in this simple implementation,
        // or we can allow updating hpp and profit if they are passed.
        // If the UI passes them as arrays, let's update them based on the grade name.
        $grades = $request->input('grades', []);
        $hpps = $request->input('hpp', []);
        $profits = $request->input('profit', []);

        foreach ($grades as $index => $grade) {
            $grade = trim($grade);
            if (empty($grade)) continue;

            $hpp = isset($hpps[$index]) ? (float) $hpps[$index] : null;
            $profit = isset($profits[$index]) ? (float) $profits[$index] : null;

            if ($hpp !== null || $profit !== null) {
                $updateData = ['updated_at' => now()];
                if ($hpp !== null) $updateData['pg_hpp'] = $hpp;
                if ($profit !== null) $updateData['pg_profit'] = $profit;

                // Check if grade exists, if not, create it
                $exists = DB::table('product_grades')
                    ->where('pg_mb_id', $id)
                    ->where('pg_grade', $grade)
                    ->exists();

                if ($exists) {
                    DB::table('product_grades')
                        ->where('pg_mb_id', $id)
                        ->where('pg_grade', $grade)
                        ->update($updateData);
                } else {
                    $updateData['pg_mb_id'] = $id;
                    $updateData['pg_grade'] = $grade;
                    $updateData['pg_stock'] = 0;
                    $updateData['created_at'] = now();
                    DB::table('product_grades')->insert($updateData);
                }
            }
        }

        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function deleteBarang($id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        // Delete dependencies first
        DB::table('barang_masuk')->where('bm_mb_id', $id)->delete();
        DB::table('barang_keluar')->where('bk_mb_id', $id)->delete();
        DB::table('product_grades')->where('pg_mb_id', $id)->delete();
        DB::table('ms_barang')->where('mb_id', $id)->delete();

        return back()->with('success', 'Produk dan seluruh riwayat transaksi terkait berhasil dihapus.');
    }

    // --- CRUD: Barang Masuk ---

    public function storeBarangMasuk(Request $request)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $mb_id = (int) $request->input('bm_mb_id');
        $grade = $request->input('bm_grade');
        $qty = (int) $request->input('bm_qty');

        // Verify product and grade exist
        $productGrade = DB::table('product_grades')
            ->where('pg_mb_id', $mb_id)
            ->where('pg_grade', $grade)
            ->first();

        if (!$productGrade) {
            return back()->with('error', 'Grade tidak ditemukan untuk produk ini.');
        }

        DB::table('barang_masuk')->insert([
            'bm_mb_id' => $mb_id,
            'bm_grade' => $grade,
            'bm_berat' => null,
            'bm_jumlah' => null,
            'bm_qty' => $qty,
            'bm_date' => $request->input('bm_date') ?: today()->format('Y-m-d'),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->recalculateGradeStock($mb_id, $grade);

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
        $old_grade = $transaction->bm_grade;

        $new_mb_id = (int) $request->input('bm_mb_id');
        $new_grade = $request->input('bm_grade');
        $qty = (int) $request->input('bm_qty');

        DB::table('barang_masuk')
            ->where('bm_id', $id)
            ->update([
                'bm_mb_id' => $new_mb_id,
                'bm_grade' => $new_grade,
                'bm_qty' => $qty,
                'bm_date' => $request->input('bm_date'),
                'updated_at' => now()
            ]);

        // Recalculate old and new grade stocks
        $this->recalculateGradeStock($new_mb_id, $new_grade);
        if ($old_mb_id !== $new_mb_id || $old_grade !== $new_grade) {
            if ($old_grade) {
                $this->recalculateGradeStock($old_mb_id, $old_grade);
            }
        }

        return back()->with('success', 'Transaksi barang masuk berhasil diperbarui.');
    }

    public function deleteBarangMasuk($id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $transaction = DB::table('barang_masuk')->where('bm_id', $id)->first();
        if ($transaction) {
            DB::table('barang_masuk')->where('bm_id', $id)->delete();
            if ($transaction->bm_grade) {
                $this->recalculateGradeStock($transaction->bm_mb_id, $transaction->bm_grade);
            } else {
                $this->recalculateStock($transaction->bm_mb_id);
            }
        }

        return back()->with('success', 'Transaksi barang masuk berhasil dihapus.');
    }

    // --- CRUD: Barang Keluar ---

    public function storeBarangKeluar(Request $request)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $mb_id = (int) $request->input('bk_mb_id');
        $grade = $request->input('bk_grade');
        $qty = (int) $request->input('bk_qty');

        // Get grade pricing
        $productGrade = DB::table('product_grades')
            ->where('pg_mb_id', $mb_id)
            ->where('pg_grade', $grade)
            ->first();

        if (!$productGrade) {
            return back()->with('error', 'Grade tidak ditemukan untuk produk ini.');
        }

        // Check stock availability
        if ($productGrade->pg_stock < $qty) {
            return back()->with('error', 'Stok tidak mencukupi. Stok Grade ' . $grade . ' tersedia: ' . $productGrade->pg_stock . ' Kg.');
        }

        $hpp = $productGrade->pg_hpp;
        $profit = $productGrade->pg_profit;
        $totalHarga = ($hpp + $profit) * $qty;

        DB::table('barang_keluar')->insert([
            'bk_mb_id' => $mb_id,
            'bk_grade' => $grade,
            'bk_qty' => $qty,
            'bk_hpp' => $hpp,
            'bk_profit' => $profit,
            'bk_date' => $request->input('bk_date') ?: today()->format('Y-m-d'),
            'bk_total_harga' => $totalHarga,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $this->recalculateGradeStock($mb_id, $grade);

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
        $old_grade = $transaction->bk_grade;

        $mb_id = (int) $request->input('bk_mb_id');
        $grade = $request->input('bk_grade');
        $qty = (int) $request->input('bk_qty');

        // Get grade pricing
        $productGrade = DB::table('product_grades')
            ->where('pg_mb_id', $mb_id)
            ->where('pg_grade', $grade)
            ->first();

        $hpp = $productGrade ? $productGrade->pg_hpp : 0;
        $profit = $productGrade ? $productGrade->pg_profit : 0;
        $totalHarga = ($hpp + $profit) * $qty;

        DB::table('barang_keluar')
            ->where('bk_id', $id)
            ->update([
                'bk_mb_id' => $mb_id,
                'bk_grade' => $grade,
                'bk_qty' => $qty,
                'bk_hpp' => $hpp,
                'bk_profit' => $profit,
                'bk_date' => $request->input('bk_date'),
                'bk_total_harga' => $totalHarga,
                'updated_at' => now()
            ]);

        $this->recalculateGradeStock($mb_id, $grade);
        if ($old_mb_id !== $mb_id || $old_grade !== $grade) {
            if ($old_grade) {
                $this->recalculateGradeStock($old_mb_id, $old_grade);
            }
        }

        return back()->with('success', 'Transaksi penjualan berhasil diperbarui.');
    }

    public function deleteBarangKeluar($id)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        $transaction = DB::table('barang_keluar')->where('bk_id', $id)->first();
        if ($transaction) {
            DB::table('barang_keluar')->where('bk_id', $id)->delete();
            if ($transaction->bk_grade) {
                $this->recalculateGradeStock($transaction->bk_mb_id, $transaction->bk_grade);
            } else {
                $this->recalculateStock($transaction->bk_mb_id);
            }
        }

        return back()->with('success', 'Transaksi penjualan berhasil dihapus.');
    }

    // --- Validation flag ---

    public function validateTransaction($id, $status)
    {
        if (session('user') !== 'admin') return redirect()->route('login');

        if (!in_array($status, ['pending', 'validated', 'rejected'])) {
            return back()->with('error', 'Status tidak valid.');
        }

        DB::table('barang_keluar')
            ->where('bk_id', $id)
            ->update([
                'status' => $status,
                'updated_at' => now()
            ]);

        // Recalculate stock since rejected transactions return stock to available inventory
        $transaction = DB::table('barang_keluar')->where('bk_id', $id)->first();
        if ($transaction) {
            if ($transaction->bk_grade) {
                $this->recalculateGradeStock($transaction->bk_mb_id, $transaction->bk_grade);
            } else {
                $this->recalculateStock($transaction->bk_mb_id);
            }
        }

        return back()->with('success', 'Status transaksi diperbarui menjadi ' . ucfirst($status) . '.');
    }

    // --- Stock Recalculation ---

    /**
     * Recalculates stock for a specific grade of a product.
     * Stock = sum(barang_masuk for this grade) - sum(barang_keluar for this grade, non-rejected)
     */
    private function recalculateGradeStock($mb_id, $grade)
    {
        $incoming = DB::table('barang_masuk')
            ->where('bm_mb_id', $mb_id)
            ->where('bm_grade', $grade)
            ->sum('bm_qty');

        $outgoing = DB::table('barang_keluar')
            ->where('bk_mb_id', $mb_id)
            ->where('bk_grade', $grade)
            ->where('status', '!=', 'rejected')
            ->sum('bk_qty');

        $netStock = max(0, $incoming - $outgoing);

        DB::table('product_grades')
            ->where('pg_mb_id', $mb_id)
            ->where('pg_grade', $grade)
            ->update(['pg_stock' => $netStock, 'updated_at' => now()]);

        // Also update total in ms_barang
        $this->recalculateTotalStock($mb_id);
    }

    /**
     * Updates the total stock in ms_barang from sum of all product_grades.
     */
    private function recalculateTotalStock($mb_id)
    {
        $totalStock = DB::table('product_grades')
            ->where('pg_mb_id', $mb_id)
            ->sum('pg_stock');

        DB::table('ms_barang')
            ->where('mb_id', $mb_id)
            ->update(['mb_stok' => $totalStock, 'updated_at' => now()]);
    }

    /**
     * Legacy recalculate for products without grade system (backward compat).
     */
    private function recalculateStock($mb_id)
    {
        $incoming = DB::table('barang_masuk')->where('bm_mb_id', $mb_id)->sum('bm_qty');
        $outgoing = DB::table('barang_keluar')
            ->where('bk_mb_id', $mb_id)
            ->where('status', '!=', 'rejected')
            ->sum('bk_qty');

        $netStock = max(0, $incoming - $outgoing);

        DB::table('ms_barang')
            ->where('mb_id', $mb_id)
            ->update(['mb_stok' => $netStock]);
    }

    // ==========================================
    // OWNER PORTAL & BI REPORTING
    // ==========================================

    public function owner(Request $request)
    {
        if (session('user') !== 'owner') {
            return redirect()->route('login')->with('error', 'Please login as Owner to access this page.');
        }

        // Apply filters
        $startDate = $request->input('start_date') ?: Carbon::now()->subMonths(5)->startOfMonth()->format('Y-m-d');
        $endDate = $request->input('end_date') ?: Carbon::now()->format('Y-m-d');
        $gradeFilter = $request->input('grade');
        $productFilter = $request->input('product');

        // Base queries
        $salesQuery = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->whereBetween('bk_date', [$startDate, $endDate]);

        $incomingQuery = DB::table('barang_masuk')
            ->join('ms_barang', 'barang_masuk.bm_mb_id', '=', 'ms_barang.mb_id')
            ->whereBetween('bm_date', [$startDate, $endDate]);

        if ($gradeFilter) {
            $salesQuery->where('barang_keluar.bk_grade', $gradeFilter);
            $incomingQuery->where('barang_masuk.bm_grade', $gradeFilter);
        }

        if ($productFilter) {
            $salesQuery->where('ms_barang.mb_id', $productFilter);
            $incomingQuery->where('ms_barang.mb_id', $productFilter);
        }

        // --- KPI Cards ---

        // Total Sales Revenue (only validated transactions)
        $totalSales = (clone $salesQuery)
            ->where('barang_keluar.status', 'validated')
            ->sum('barang_keluar.bk_total_harga');

        // Gross Margin from actual hpp and profit data
        $grossMarginData = (clone $salesQuery)
            ->where('barang_keluar.status', 'validated')
            ->select(
                DB::raw('SUM(barang_keluar.bk_profit * barang_keluar.bk_qty) as total_profit')
            )
            ->first();
        $grossMargin = $grossMarginData->total_profit ?? ($totalSales * 0.25);

        // Stock Movement
        $totalStockIn = (clone $incomingQuery)->sum('barang_masuk.bm_qty');
        $totalStockOut = (clone $salesQuery)->sum('barang_keluar.bk_qty');

        // Net Units Available
        $netUnitsAvailable = $totalStockIn - $totalStockOut;

        // --- Visualization: Chart.js data ---

        // 1. Sales Trend (validated) grouped by Month
        $monthlySalesQuery = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->select(
                DB::raw("DATE_FORMAT(bk_date, '%Y-%m') as month"),
                DB::raw("SUM(bk_total_harga) as total_sales"),
                DB::raw("SUM(bk_qty) as total_qty")
            )
            ->where('barang_keluar.status', 'validated')
            ->whereBetween('bk_date', [$startDate, $endDate]);

        if ($gradeFilter) {
            $monthlySalesQuery->where('barang_keluar.bk_grade', $gradeFilter);
        }
        if ($productFilter) {
            $monthlySalesQuery->where('ms_barang.mb_id', $productFilter);
        }

        $salesTrend = $monthlySalesQuery
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        // 2. Stock Movement Monthly Comparison (In vs Out)
        $stockInMonthlyQuery = DB::table('barang_masuk')
            ->join('ms_barang', 'barang_masuk.bm_mb_id', '=', 'ms_barang.mb_id')
            ->select(
                DB::raw("DATE_FORMAT(bm_date, '%Y-%m') as month"),
                DB::raw("SUM(bm_qty) as total_in")
            )
            ->whereBetween('bm_date', [$startDate, $endDate]);
        if ($gradeFilter) $stockInMonthlyQuery->where('barang_masuk.bm_grade', $gradeFilter);
        if ($productFilter) $stockInMonthlyQuery->where('ms_barang.mb_id', $productFilter);
        $stockInMonthlyData = $stockInMonthlyQuery->groupBy('month')->orderBy('month', 'asc')->get()->pluck('total_in', 'month')->toArray();

        $stockOutMonthlyQuery = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->select(
                DB::raw("DATE_FORMAT(bk_date, '%Y-%m') as month"),
                DB::raw("SUM(bk_qty) as total_out")
            )
            ->where('status', '!=', 'rejected')
            ->whereBetween('bk_date', [$startDate, $endDate]);
        if ($gradeFilter) $stockOutMonthlyQuery->where('barang_keluar.bk_grade', $gradeFilter);
        if ($productFilter) $stockOutMonthlyQuery->where('ms_barang.mb_id', $productFilter);
        $stockOutMonthlyData = $stockOutMonthlyQuery->groupBy('month')->orderBy('month', 'asc')->get()->pluck('total_out', 'month')->toArray();

        // Harmonize monthly labels
        $allMonths = array_unique(array_merge(array_keys($stockInMonthlyData), array_keys($stockOutMonthlyData)));
        sort($allMonths);

        $chartLabels = [];
        $chartSales = [];
        $chartStockIn = [];
        $chartStockOut = [];

        foreach ($allMonths as $month) {
            $chartLabels[] = Carbon::parse($month . '-01')->format('M Y');
            $chartStockIn[] = $stockInMonthlyData[$month] ?? 0;
            $chartStockOut[] = $stockOutMonthlyData[$month] ?? 0;

            // Sales matching this month
            $salesVal = 0;
            foreach ($salesTrend as $s) {
                if ($s->month === $month) {
                    $salesVal = $s->total_sales;
                    break;
                }
            }
            $chartSales[] = $salesVal;
        }

        // --- Linear Regression Forecasting & Predictive Analytics ---

        $forecastingQuery = DB::table('barang_keluar')
            ->select(
                DB::raw("DATE_FORMAT(bk_date, '%Y-%m') as month"),
                DB::raw("SUM(bk_qty) as total_qty")
            )
            ->where('status', 'validated')
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $n = count($forecastingQuery);
        $slope = 0;
        $intercept = 0;
        $forecastedQty = 0;
        $trendDirection = 'Stable';

        if ($n >= 2) {
            $sumX = 0;
            $sumY = 0;
            $sumXY = 0;
            $sumX2 = 0;

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

        // --- Detail Transactions Table ---
        $transactions = (clone $salesQuery)
            ->select('barang_keluar.*', 'ms_barang.mb_desc')
            ->orderBy('bk_date', 'desc')
            ->get();

        // --- Product list for filter ---
        $products = DB::table('ms_barang')->select('mb_id', 'mb_desc')->orderBy('mb_desc')->get();

        // --- Grade distribution for pie chart ---
        $gradeDistributionQuery = DB::table('barang_keluar')
            ->select('bk_grade', DB::raw('SUM(bk_qty) as total_qty'))
            ->where('status', 'validated')
            ->whereBetween('bk_date', [$startDate, $endDate])
            ->whereNotNull('bk_grade');

        if ($productFilter) {
            $gradeDistributionQuery->where('bk_mb_id', $productFilter);
        }
        if ($gradeFilter) {
            $gradeDistributionQuery->where('bk_grade', $gradeFilter);
        }

        $gradeDistribution = $gradeDistributionQuery
            ->groupBy('bk_grade')
            ->get();

        return view('owner', compact(
            'totalSales',
            'grossMargin',
            'totalStockIn',
            'totalStockOut',
            'netUnitsAvailable',
            'chartLabels',
            'chartSales',
            'chartStockIn',
            'chartStockOut',
            'slope',
            'intercept',
            'forecastedQty',
            'trendDirection',
            'transactions',
            'startDate',
            'endDate',
            'gradeFilter',
            'productFilter',
            'products',
            'gradeDistribution'
        ));
    }

    // CSV Exporter for Sales Data
    public function exportCsv(Request $request)
    {
        if (session('user') !== 'owner') {
            return redirect()->route('login')->with('error', 'Unauthorized.');
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $gradeFilter = $request->input('grade');
        $productFilter = $request->input('product');

        $query = DB::table('barang_keluar')
            ->join('ms_barang', 'barang_keluar.bk_mb_id', '=', 'ms_barang.mb_id')
            ->select('barang_keluar.bk_id', 'ms_barang.mb_desc', 'barang_keluar.bk_grade', 'barang_keluar.bk_qty', 'barang_keluar.bk_date', 'barang_keluar.bk_hpp', 'barang_keluar.bk_profit', 'barang_keluar.bk_total_harga', 'barang_keluar.status')
            ->orderBy('bk_date', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('bk_date', [$startDate, $endDate]);
        }
        if ($gradeFilter) {
            $query->where('barang_keluar.bk_grade', $gradeFilter);
        }
        if ($productFilter) {
            $query->where('ms_barang.mb_id', $productFilter);
        }

        $data = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=Sales_Report_CV_Mojoputri.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Transaction ID', 'Product Description', 'Grade', 'Quantity Sold (Kg)', 'Sales Date', 'HPP/Kg', 'Profit/Kg', 'Total Value (IDR)', 'Status'];

        $callback = function() use($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $row) {
                $grade = $row->bk_grade ?: '-';
                fputcsv($file, [
                    $row->bk_id,
                    $row->mb_desc,
                    $grade,
                    $row->bk_qty,
                    $row->bk_date,
                    $row->bk_hpp ?? 0,
                    $row->bk_profit ?? 0,
                    $row->bk_total_harga,
                    ucfirst($row->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
