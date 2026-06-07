<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard | CV Mojoputri</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f111a;
            color: #ffffff;
        }
        .sidebar {
            background-color: #0c0d16;
            border-right: 1px solid rgba(255, 255, 255, 0.03);
        }
        .glass-card {
            background-color: #1a1d2e;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .purple-btn {
            background-color: #5b50e6;
            transition: all 0.2s ease;
        }
        .purple-btn:hover {
            background-color: #4c42c2;
            box-shadow: 0 0 15px rgba(91, 80, 230, 0.35);
        }
        .glow-card {
            position: relative;
        }
        .glow-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(to bottom right, rgba(91, 80, 230, 0.3), transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f111a;
        }
        ::-webkit-scrollbar-thumb {
            background: #1a1d2e;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #5b50e6;
        }
        @media (max-width: 1023px) {
            .sidebar-mobile { transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar-mobile.open { transform: translateX(0); }
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-40 hidden lg:hidden"></div>

    <!-- SIDEBAR -->
    <div id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 sidebar sidebar-mobile flex flex-col justify-between shrink-0">
        <div class="p-6">
            <!-- Brand -->
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#5b50e6] to-[#8b5cf6] flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-chart-pie text-white text-base"></i>
                </div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-wider text-white">CV MOJOPUTRI</h1>
                    <span class="text-[10px] text-slate-500 font-semibold tracking-widest uppercase">Executive BI</span>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="flex items-center space-x-3 p-3 bg-slate-900/60 rounded-xl mb-6 border border-slate-800/40">
                <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center border border-purple-500/20">
                    <i class="fas fa-user-tie text-[#8b5cf6] text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xs font-bold text-white">Direktur Utama</h2>
                    <span class="text-[10px] text-indigo-400 font-semibold uppercase flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span> Owner Portal
                    </span>
                </div>
            </div>

            <!-- Navigation Tabs (Only Owner Dashboard on this view) -->
            <nav class="space-y-1">
                <a href="#" class="w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg bg-indigo-500/15 text-[#8b5cf6] border-left border-l-3 border-[#5b50e6] text-xs font-semibold tracking-wide transition-all">
                    <i class="fas fa-cubes text-base w-5"></i>
                    <span>Dashboard Utama</span>
                </a>
            </nav>
        </div>

        <!-- Logout Area -->
        <div class="p-6 border-t border-slate-900">
            <a href="{{ route('logout') }}" class="w-full flex items-center justify-center space-x-2.5 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 py-2.5 rounded-lg text-xs font-bold transition-all">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar Aplikasi</span>
            </a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden relative w-full">
        
        <!-- TOPBAR & FILTER PANEL -->
        <header class="border-b border-slate-900 p-4 sm:p-6 shrink-0 bg-[#0f111a] flex flex-col gap-4">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg bg-slate-800 text-slate-300 hover:text-white shrink-0">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-chart-line text-[#8b5cf6]"></i> Sales & Stock Intelligence Dashboard
                    </h1>
                    <p class="text-[10px] text-slate-500 font-semibold tracking-wide uppercase hidden sm:block">Analisis Performa Bisnis, Inventori & Prediksi Peramalan Penjualan</p>
                </div>
            </div>
            
            <form action="{{ route('owner.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-[#1a1d2e] border border-slate-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-[#5b50e6] w-full sm:w-auto">
                <span class="text-slate-600 hidden sm:inline">s/d</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-[#1a1d2e] border border-slate-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-[#5b50e6] w-full sm:w-auto">
                <select name="product" class="bg-[#1a1d2e] border border-slate-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-[#5b50e6] w-full sm:w-auto">
                    <option value="">Semua Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->mb_id }}" {{ (string)$productFilter === (string)$p->mb_id ? 'selected' : '' }}>{{ $p->mb_desc }}</option>
                    @endforeach
                </select>
                <select name="grade" class="bg-[#1a1d2e] border border-slate-800 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-[#5b50e6] w-full sm:w-auto">
                    <option value="">Semua Grade</option>
                    <option value="A" {{ $gradeFilter === 'A' ? 'selected' : '' }}>Grade A</option>
                    <option value="B" {{ $gradeFilter === 'B' ? 'selected' : '' }}>Grade B</option>
                    <option value="C" {{ $gradeFilter === 'C' ? 'selected' : '' }}>Grade C</option>
                </select>
                <div class="flex gap-2 w-full sm:w-auto">
                    <button type="submit" class="flex-1 sm:flex-none bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded-lg transition-all flex items-center justify-center gap-1.5">
                        <i class="fas fa-filter"></i> Terapkan
                    </button>
                    <a href="{{ route('owner.dashboard') }}" class="flex-1 sm:flex-none bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold px-3 py-2 rounded-lg transition-all text-center">Reset</a>
                </div>
            </form>
        </header>

        <!-- VIEWPORT -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6">

            <!-- SUCCESS NOTIFICATION -->
            @if(session('success'))
                <div class="mb-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-xs flex items-center justify-between shadow-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle text-lg"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200"><i class="fas fa-times"></i></button>
                </div>
            @endif

            <!-- KPI GRID CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1: Total Sales Revenue -->
                <div class="glass-card rounded-2xl p-6 shadow-xl relative overflow-hidden flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Total Sales Revenue</span>
                        <h3 class="text-xl font-extrabold text-white">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                        <span class="text-[9px] font-semibold text-emerald-400 flex items-center gap-1">
                            <i class="fas fa-check-double"></i> Hanya Transaksi Tervalidasi
                        </span>
                    </div>
                    <div class="w-12 h-12 bg-indigo-500/10 rounded-xl flex items-center justify-center border border-indigo-500/20 text-[#5b50e6]">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                </div>

                <!-- Card 2: Estimated Gross Margin -->
                <div class="glass-card rounded-2xl p-6 shadow-xl relative overflow-hidden flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Gross Profit Margin</span>
                        <h3 class="text-xl font-extrabold text-emerald-400">Rp {{ number_format($grossMargin, 0, ',', '.') }}</h3>
                        <span class="text-[9px] font-semibold text-slate-500">Estimasi Bersih dari Pendapatan</span>
                    </div>
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20 text-emerald-400">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                </div>

                <!-- Card 3: Stock Movement -->
                <div class="glass-card rounded-2xl p-6 shadow-xl relative overflow-hidden flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Volume Transaksi Stok</span>
                        <div class="flex items-center gap-3">
                            <div>
                                <span class="text-[9px] text-emerald-400 block font-semibold">IN (Masuk)</span>
                                <span class="text-sm font-bold text-white">{{ number_format($totalStockIn, 0, ',', '.') }} Kg</span>
                            </div>
                            <div class="text-slate-700 font-light text-lg">|</div>
                            <div>
                                <span class="text-[9px] text-red-400 block font-semibold">OUT (Keluar)</span>
                                <span class="text-sm font-bold text-white">{{ number_format($totalStockOut, 0, ',', '.') }} Kg</span>
                            </div>
                        </div>
                    </div>
                    <div class="w-12 h-12 bg-[#8b5cf6]/10 rounded-xl flex items-center justify-center border border-[#8b5cf6]/20 text-[#8b5cf6]">
                        <i class="fas fa-dolly-flatbed text-xl"></i>
                    </div>
                </div>

                <!-- Card 4: Net Units Available -->
                <div class="glass-card rounded-2xl p-6 shadow-xl relative overflow-hidden flex items-center justify-between">
                    <div class="space-y-2">
                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider block">Net Available Stock</span>
                        <h3 class="text-xl font-extrabold text-blue-400">{{ number_format($netUnitsAvailable, 0, ',', '.') }} Kg</h3>
                        <span class="text-[9px] font-semibold text-slate-500">Margin Ketersediaan Bersih</span>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center border border-blue-500/20 text-blue-400">
                        <i class="fas fa-cubes text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- FORECASTING & PREDICTIVE ANALYTICS SECTION -->
            <div class="glow-card glass-card rounded-3xl p-6 shadow-2xl bg-gradient-to-br from-[#1a1d2e] to-[#121420] border border-indigo-500/10">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="space-y-3 max-w-xl">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-indigo-500/15 border border-indigo-500/20 text-xs font-bold text-indigo-400 uppercase">
                            <i class="fas fa-brain animate-pulse"></i> Forecasting & Predictive Analytics
                        </div>
                        <h2 class="text-base font-extrabold text-white">Regresi Linier Peramalan Tren Penjualan Bulan Depan</h2>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Menerapkan perhitungan matematika statistik regresi linear terintegrasi ($y = mx + b$) berdasarkan data kuantitas penjualan bulanan historis tervalidasi di database.
                        </p>
                        <!-- Explaining Math Formula -->
                        <div class="p-3.5 bg-slate-950/60 rounded-2xl border border-slate-900 flex flex-wrap items-center gap-4 text-xs font-mono text-slate-300">
                            <div>
                                <span class="text-slate-500 uppercase tracking-widest font-semibold block text-[9px] mb-0.5">Model Regresi</span>
                                <span>y = ({{ round($slope, 2) }} &times; X) + {{ round($intercept, 2) }}</span>
                            </div>
                            <div class="h-6 w-[1px] bg-slate-800"></div>
                            <div>
                                <span class="text-slate-500 uppercase tracking-widest font-semibold block text-[9px] mb-0.5">Slope (m)</span>
                                <span class="{{ $slope >= 0 ? 'text-emerald-400' : 'text-red-400' }}">{{ round($slope, 2) }}</span>
                            </div>
                            <div class="h-6 w-[1px] bg-slate-800"></div>
                            <div>
                                <span class="text-slate-500 uppercase tracking-widest font-semibold block text-[9px] mb-0.5">Arah Tren</span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase font-sans {{ $slope >= 0.1 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                                    {{ $trendDirection }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Forecast Result Counter -->
                    <div class="bg-indigo-950/20 border border-indigo-500/20 p-6 rounded-2xl flex flex-col md:flex-row items-center gap-6 shrink-0 lg:w-96 shadow-lg shadow-indigo-600/5">
                        <div class="w-14 h-14 bg-indigo-500/20 rounded-2xl flex items-center justify-center border border-indigo-500/40 text-indigo-300">
                            <i class="fas fa-magic text-2xl"></i>
                        </div>
                        <div class="space-y-1.5 text-center md:text-left">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Prediksi Volume Penjualan</span>
                            <h4 class="text-2xl font-extrabold text-white">{{ number_format($forecastedQty, 0, ',', '.') }} Kg</h4>
                            <p class="text-[10px] text-slate-500 font-medium">Bulan Depan (Beras Siap Jual)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VISUALIZATIONS GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Line Chart: Sales Trends -->
                <div class="glass-card rounded-2xl p-6 shadow-xl flex flex-col">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-900 pb-3">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#5b50e6]"></span> Sales revenue trend (Pendapatan)
                        </h3>
                        <span class="text-[10px] text-slate-500">Berdasarkan bulan</span>
                    </div>
                    <div class="relative flex-1 min-h-[280px]">
                        <canvas id="salesTrendChart"></canvas>
                    </div>
                </div>

                <!-- Bar Chart: Stock Movement Comparison -->
                <div class="glass-card rounded-2xl p-6 shadow-xl flex flex-col">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-900 pb-3">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#8b5cf6]"></span> Stock movement comparison (Masuk vs Keluar)
                        </h3>
                        <span class="text-[10px] text-slate-500">Volume kuantitas Kg</span>
                    </div>
                    <div class="relative flex-1 min-h-[280px]">
                        <canvas id="stockMovementChart"></canvas>
                    </div>
                </div>

                <!-- Pie Chart: Grade Distribution -->
                <div class="glass-card rounded-2xl p-6 shadow-xl flex flex-col lg:col-span-2 xl:col-span-1">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-900 pb-3">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Distribusi Penjualan per Grade
                        </h3>
                        <span class="text-[10px] text-slate-500">Tervalidasi</span>
                    </div>
                    <div class="relative flex-1 min-h-[280px] flex items-center justify-center">
                        <canvas id="gradeDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- DETAIL TRANSACTIONS TABLE -->
            <div class="space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-list-ul text-[#8b5cf6]"></i> Catatan Detail Riwayat Penjualan
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">Daftar transaksi penjualan terdaftar dalam jangka penyaringan</p>
                    </div>
                    
                    <!-- EXPORT BUTTON -->
                    <a href="{{ route('owner.export', ['start_date' => $startDate, 'end_date' => $endDate, 'grade' => $gradeFilter, 'product' => $productFilter]) }}" 
                       class="purple-btn text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-2 shadow-lg shadow-indigo-600/10 w-max self-end transition-all">
                        <i class="fas fa-file-csv"></i> Unduh Laporan CSV / Excel
                    </a>
                </div>

                <!-- Table Card -->
                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[640px]">
                            <thead>
                                <tr class="bg-slate-900/80 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                                    <th class="p-4 pl-6">ID Keluar</th>
                                    <th class="p-4">Deskripsi Produk</th>
                                    <th class="p-4">Tanggal Penjualan</th>
                                    <th class="p-4">Kuantitas</th>
                                    <th class="p-4">Total Pendapatan (IDR)</th>
                                    <th class="p-4 pr-6">Status Validasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @forelse($transactions as $t)
                                <tr class="hover:bg-slate-900/20 transition-all">
                                    <td class="p-4 pl-6 font-bold text-indigo-400">#OUT-{{ $t->bk_id }}</td>
                                    <td class="p-4">
                                        <div class="font-semibold text-white">{{ $t->mb_desc }}</div>
                                        @if($t->bk_grade)
                                            <div class="text-[10px] text-indigo-400 font-bold uppercase mt-0.5">Grade {{ $t->bk_grade }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-300 font-medium"><i class="far fa-calendar-alt text-slate-600 mr-1.5"></i>{{ \Carbon\Carbon::parse($t->bk_date)->format('d F Y') }}</td>
                                    <td class="p-4 font-bold text-slate-300">{{ $t->bk_qty }} Kg</td>
                                    <td class="p-4 font-bold text-white">Rp {{ number_format($t->bk_total_harga, 0, ',', '.') }}</td>
                                    <td class="p-4 pr-6">
                                        @if($t->status === 'validated')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-bold text-[9px] uppercase border border-emerald-500/20">
                                                <span class="w-1 h-1 rounded-full bg-emerald-400"></span> Validated
                                            </span>
                                        @elseif($t->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 font-bold text-[9px] uppercase border border-red-500/20">
                                                <span class="w-1 h-1 rounded-full bg-red-400"></span> Rejected
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 font-bold text-[9px] uppercase border border-amber-500/20">
                                                <span class="w-1 h-1 rounded-full bg-amber-400 animate-pulse"></span> Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-500 font-medium">Tidak ada transaksi penjualan terdaftar dalam jangka penyaringan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- =========================================================================
         CHART.JS NEON RENDER SCRIPT
         ========================================================================= -->
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }

        // Custom color assets
        const accentPurple = '#5b50e6';
        const accentViolet = '#8b5cf6';
        const textMuted = '#94a3b8';
        const gridColor = 'rgba(255, 255, 255, 0.02)';

        // Chart Data Injectors from Controller
        const labels = @json($chartLabels);
        const salesData = @json($chartSales);
        const stockInData = @json($chartStockIn);
        const stockOutData = @json($chartStockOut);
        const gradeLabels = @json($gradeDistribution->pluck('bk_grade'));
        const gradeQty = @json($gradeDistribution->pluck('total_qty'));

        // Chart.js default font settings
        Chart.defaults.color = textMuted;
        Chart.defaults.font.family = 'Outfit';
        Chart.defaults.font.size = 11;

        // 1. Render Sales Revenue Trend (Line Chart)
        const ctxSales = document.getElementById('salesTrendChart').getContext('2d');
        const salesGradient = ctxSales.createLinearGradient(0, 0, 0, 300);
        salesGradient.addColorStop(0, 'rgba(91, 80, 230, 0.45)');
        salesGradient.addColorStop(1, 'rgba(91, 80, 230, 0)');

        new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pendapatan Bersih (IDR)',
                    data: salesData,
                    borderColor: accentPurple,
                    borderWidth: 3.5,
                    backgroundColor: salesGradient,
                    fill: true,
                    tension: 0.38,
                    pointBackgroundColor: accentPurple,
                    pointBorderColor: '#fff',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: accentPurple,
                    pointHoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { padding: 8 }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            padding: 8,
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // 2. Render Stock In vs Stock Out Comparison (Bar Chart)
        const ctxStock = document.getElementById('stockMovementChart').getContext('2d');
        new Chart(ctxStock, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Stok Masuk (IN)',
                        data: stockInData,
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderRadius: 6,
                        maxBarThickness: 15
                    },
                    {
                        label: 'Stok Keluar (OUT)',
                        data: stockOutData,
                        backgroundColor: 'rgba(239, 68, 68, 0.85)',
                        borderRadius: 6,
                        maxBarThickness: 15
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#fff',
                            boxWidth: 10,
                            boxHeight: 10,
                            padding: 12
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { padding: 8 }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: {
                            padding: 8,
                            callback: function(value) {
                                return value + ' Kg';
                            }
                        }
                    }
                }
            }
        });

        // 3. Grade Distribution Pie Chart
        const ctxGrade = document.getElementById('gradeDistributionChart').getContext('2d');
        new Chart(ctxGrade, {
            type: 'doughnut',
            data: {
                labels: gradeLabels.map(g => 'Grade ' + g),
                datasets: [{
                    data: gradeQty,
                    backgroundColor: ['rgba(91, 80, 230, 0.85)', 'rgba(139, 92, 246, 0.85)', 'rgba(16, 185, 129, 0.85)'],
                    borderColor: '#1a1d2e',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#fff', boxWidth: 10, padding: 12 }
                    }
                }
            }
        });
    </script>
</body>
</html>
