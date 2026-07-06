<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | CV Mojoputri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #EAEAEA;
            color: #000000;
        }
        .sidebar {
            background-color: #EAEAEA;
            /* Border menggunakan warna utama 2DAA9E dengan transparansi rendah untuk aksen minimalis */
            border-right: 1px solid rgba(45, 170, 158, 0.2);
        }
        .glass-card {
            /* Menggunakan background putih padat agar kontras dan terbaca jelas di atas warna latar #EAEAEA */
            background-color: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(45, 170, 158, 0.15);
        }

        /* Skema warna tombol utama */
        .sage-btn {
            background-color: #2DAA9E;
            color: #000;
            transition: all 0.2s ease;
        }
        .sage-btn:hover {
            background-color: #66D2CE;
            color: #000000; /* Warna hitam pekat saat di-hover karena background-nya berubah terang */
            box-shadow: 0 4px 15px rgba(45, 170, 158, 0.25);
        }

        /* Tab navigasi aktif */
        .tab-btn.active {
            /* Menggunakan opacity rendah dari warna utama 2DAA9E sebagai latar belakang */
            background-color: rgba(45, 170, 158, 0.12);
            color: #2DAA9E;
            border-left: 3.5px solid #2DAA9E;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #EAEAEA; }
        ::-webkit-scrollbar-thumb { background: #EAEAEA; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #2DAA9E; }

        @media (max-width: 1023px) {
            .sidebar-mobile { transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar-mobile.open { transform: translateX(0); }
        }

        /* Glow card for analytics */
        .glow-card {
            position: relative;
        }
        .glow-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(to bottom right, rgba(45, 170, 158, 0.3), transparent);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
    </style>

</head>
<body class="flex h-screen overflow-hidden">

    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-40 hidden lg:hidden"></div>

    <div id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 sidebar sidebar-mobile flex flex-col justify-between shrink-0">
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-17 h-17 rounded-xl flex items-center justify-center shadow-lg">
                    <img src="{{ asset('assets/logo-cv-mojoputri.png') }}" alt="Logo CV Mojoputri" class="w-full h-full object-contain drop-shadow-lg">
                </div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-wider text-black">CV MOJOPUTRI</h1>
                    <span class="text-xs text-slate-600 font-semibold tracking-widest uppercase">Admin Portal</span>
                </div>
            </div>

            <div class="flex items-center space-x-3 p-3 bg-slate-100 rounded-xl mb-6 border border-slate-300/40">
                <div class="w-10 h-10 rounded-lg bg-[#2DAA9E]/10 flex items-center justify-center border border-[#2DAA9E]/20">
                    <i class="fas fa-user-shield text-[#2DAA9E] text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xs font-bold text-black">Administrator</h2>
                    <span class="text-xs text-emerald-400 font-semibold uppercase flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Online
                    </span>
                </div>
            </div>

            <nav class="space-y-1">
                <button onclick="switchTab('master-barang')" id="btn-master-barang" class="tab-btn active w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg text-slate-700 hover:text-black hover:bg-slate-100 text-xs font-semibold tracking-wide transition-all text-left">
                    <i class="fas fa-boxes text-base w-5"></i><span>Master Barang</span>
                </button>
                <button onclick="switchTab('barang-masuk')" id="btn-barang-masuk" class="tab-btn w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg text-slate-700 hover:text-black hover:bg-slate-100 text-xs font-semibold tracking-wide transition-all text-left">
                    <i class="fas fa-arrow-circle-down text-base w-5"></i><span>Barang Masuk (Stok)</span>
                </button>
                <button onclick="switchTab('barang-keluar')" id="btn-barang-keluar" class="tab-btn w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg text-slate-700 hover:text-black hover:bg-slate-100 text-xs font-semibold tracking-wide transition-all text-left">
                    <i class="fas fa-arrow-circle-up text-base w-5"></i><span>Barang Keluar (Sales)</span>
                </button>
                <button onclick="switchTab('verifikasi')" id="btn-verifikasi" class="tab-btn w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg text-slate-700 hover:text-black hover:bg-slate-100 text-xs font-semibold tracking-wide transition-all text-left">
                    <i class="fas fa-check-double text-base w-5"></i><span>Verifikasi Penjualan</span>
                    @php $pendingCount = $barangKeluar->where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-amber-500 text-slate-950 font-bold px-1.5 py-0.5 rounded text-[11px]">{{ $pendingCount }}</span>
                    @endif
                </button>
                <button onclick="switchTab('dashboard-analitik')" id="btn-dashboard-analitik" class="tab-btn w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg text-slate-700 hover:text-black hover:bg-slate-100 text-xs font-semibold tracking-wide transition-all text-left">
                    <i class="fas fa-chart-line text-base w-5"></i><span>Dashboard Analitik</span>
                </button>
            </nav>
        </div>
        <div class="p-6 border-t border-slate-200">
            <a href="{{ route('logout') }}" class="w-full flex items-center justify-center space-x-2.5 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-black border border-red-500/20 py-2.5 rounded-lg text-xs font-bold transition-all">
                <i class="fas fa-sign-out-alt"></i><span>Keluar Aplikasi</span>
            </a>
        </div>
    </div>

    <div class="flex-1 flex flex-col overflow-hidden relative w-full">
        <header class="h-auto min-h-16 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between px-4 sm:px-8 py-3 shrink-0 bg-white gap-3">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg bg-slate-200 text-slate-800 hover:text-black">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-black flex items-center gap-2">
                        <i class="fas fa-solar-panel text-[#659287]"></i> Portal Operasional Admin
                    </h1>
                    <p class="text-xs text-slate-600 font-semibold tracking-wide uppercase hidden sm:block">Kelola Data Master Penjualan & Inventaris CV Mojoputri</p>
                </div>
            </div>
            <span class="text-xs text-slate-700"><i class="far fa-calendar-alt mr-1.5"></i> Hari Ini: <span class="font-bold text-black">{{ today()->format('d M Y') }}</span></span>
        </header>

        <main class="flex-1 overflow-y-auto p-4 sm:p-8 relative">
            @if(session('success'))
                <div class="mb-6 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-4 rounded-xl text-xs flex items-center justify-between shadow-lg">
                    <div class="flex items-center gap-3"><i class="fas fa-check-circle text-lg"></i><div><span class="font-bold">Berhasil!</span> {{ session('success') }}</div></div>
                    <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200"><i class="fas fa-times"></i></button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-500/10 border border-red-500/30 text-red-400 p-4 rounded-xl text-xs flex items-center justify-between shadow-lg">
                    <div class="flex items-center gap-3"><i class="fas fa-exclamation-triangle text-lg"></i><div><span class="font-bold">Error!</span> {{ session('error') }}</div></div>
                    <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-200"><i class="fas fa-times"></i></button>
                </div>
            @endif

            <div id="tab-master-barang" class="tab-content space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold tracking-wide text-slate-700 uppercase">Master Data Produk</h2>
                        <p class="text-xs text-slate-600 mt-1">Daftar beras dan hasil produksi Mojoputri</p>
                    </div>
                    <button onclick="toggleModal('modal-add-barang')" class="sage-btn text-xs font-bold px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 shadow-lg w-full sm:w-auto">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </button>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[640px]">
                            <thead>
                                <tr class="bg-white border-b border-slate-300 text-slate-700 uppercase tracking-wider font-semibold">
                                    <th class="p-4 pl-6">ID</th>
                                    <th class="p-4">Nama Produk</th>
                                    <th class="p-4">Grade</th>
                                    <th class="p-4">Stok Tersedia</th>
                                    <th class="p-4">Harga / Kg</th>
                                    <th class="p-4 text-center pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($barang as $b)
                                <tr class="hover:bg-slate-50 transition-all">
                                    <td class="p-4 pl-6 font-bold text-[#000000]">#{{ $b->mb_id }}</td>
                                    <td class="p-4 font-semibold text-[#000000]">{{ $b->mb_desc }}</td>
                                    <td class="p-4 text-[#2DAA9E] font-bold text-xs uppercase">{{ $b->mb_grade }}</td>
                                    <td class="p-4 font-bold text-slate-800">
                                        @if($b->mb_stok < 50)
                                            <span class="text-red-400"><i class="fas fa-exclamation-circle mr-1"></i> {{ $b->mb_stok }} Kg</span>
                                        @else
                                            <span class="text-[#000000]"><i class="fas fa-cubes mr-1 text-[#0000]/70"></i> {{ $b->mb_stok }} Kg</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-800 font-medium">
                                        Rp {{ number_format($b->mb_hpp + $b->mb_profit, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-center pr-6">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            <button onclick='openDetailBarang(@json($b))' class="px-2.5 py-1.5 bg-[#2DAA9E]/10 hover:bg-[#2DAA9E] text-[#2DAA9E] hover:text-[#0f111a] rounded-lg text-xs font-bold transition-all">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </button>
                                            <button onclick='openEditBarang(@json($b))' class="w-7 h-7 bg-slate-200 hover:bg-[#2DAA9E] text-slate-700 hover:text-[#0f111a] rounded-lg flex items-center justify-center transition-all">
                                                <i class="far fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.barang.delete', $b->mb_id) }}" method="POST" onsubmit="return confirm('Hapus produk ini dan seluruh riwayat transaksi terkait?')">
                                                @csrf
                                                <button type="submit" class="w-7 h-7 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-black rounded-lg flex items-center justify-center transition-all">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="p-8 text-center text-slate-600 font-medium">Belum ada data barang tersedia.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tab-barang-masuk" class="tab-content hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold tracking-wide text-slate-700 uppercase">Penerimaan Barang Masuk</h2>
                        <p class="text-xs text-slate-600 mt-1">Catat penambahan stok hasil produksi/penyetokan</p>
                    </div>
                    <button onclick="toggleModal('modal-add-masuk')" class="sage-btn text-xs font-bold px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 shadow-lg w-full sm:w-auto">
                        <i class="fas fa-plus"></i> Catat Barang Masuk
                    </button>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[600px]">
                            <thead>
                                <tr class="bg-white border-b border-slate-300 text-slate-700 uppercase tracking-wider font-semibold">
                                    <th class="p-4 pl-6">ID Masuk</th>
                                    <th class="p-4">Nama Produk</th>
                                    <th class="p-4">Tanggal Masuk</th>
                                    <th class="p-4">Jumlah</th>
                                    <th class="p-4 text-center pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($barangMasuk as $bm)
                                <tr class="hover:bg-slate-50 transition-all">
                                    <td class="p-4 pl-6 font-bold text-[#2DAA9E]">#IN-{{ $bm->bm_id }}</td>
                                    <td class="p-4">
                                        <div class="font-semibold text-black">{{ $bm->mb_desc }}</div>
                                        @if($bm->mb_grade)
                                            <div class="text-xs text-[#2DAA9E] font-bold uppercase mt-0.5">{{ $bm->mb_grade }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-800 font-medium"><i class="far fa-calendar-alt text-slate-600 mr-1.5"></i>{{ \Carbon\Carbon::parse($bm->bm_date)->format('d F Y') }}</td>
                                    <td class="p-4 font-bold text-[#2DAA9E]">+{{ $bm->bm_qty }} Kg</td>
                                    <td class="p-4 text-center pr-6">
                                        <div class="flex items-center justify-center gap-3">
                                            <button onclick="openEditMasuk({{ $bm->bm_id }}, {{ $bm->bm_mb_id }}, {{ $bm->bm_qty }}, '{{ $bm->bm_date }}')" class="w-7 h-7 bg-slate-200 hover:bg-[#2DAA9E] text-slate-700 hover:text-[#0f111a] rounded-lg flex items-center justify-center transition-all">
                                                <i class="far fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.masuk.delete', $bm->bm_id) }}" method="POST" onsubmit="return confirm('Hapus transaksi masuk ini? Stok akan berkurang otomatis.')">
                                                @csrf
                                                <button type="submit" class="w-7 h-7 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-black rounded-lg flex items-center justify-center transition-all">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="p-8 text-center text-slate-600 font-medium">Belum ada pencatatan barang masuk.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tab-barang-keluar" class="tab-content hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold tracking-wide text-slate-700 uppercase">Pencatatan Penjualan (Barang Keluar)</h2>
                        <p class="text-xs text-slate-600 mt-1">Catat transaksi penjualan beras ke pelanggan</p>
                    </div>
                    <button onclick="toggleModal('modal-add-keluar')" class="sage-btn text-xs font-bold px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 shadow-lg w-full sm:w-auto">
                        <i class="fas fa-plus"></i> Catat Penjualan Baru
                    </button>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[720px]">
                            <thead>
                                <tr class="bg-white border-b border-slate-300 text-slate-700 uppercase tracking-wider font-semibold">
                                    <th class="p-4 pl-6">ID Keluar</th>
                                    <th class="p-4">Nama Produk</th>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4">Jumlah</th>
                                    <th class="p-4">Total Pendapatan</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-center pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($barangKeluar as $bk)
                                <tr class="hover:bg-slate-50 transition-all">
                                    <td class="p-4 pl-6 font-bold text-[#2DAA9E]">#OUT-{{ $bk->bk_id }}</td>
                                    <td class="p-4">
                                        <div class="font-semibold text-black">{{ $bk->mb_desc }}</div>
                                        @if($bk->mb_grade)
                                            <div class="text-xs text-[#2DAA9E] font-bold uppercase mt-0.5">{{ $bk->mb_grade }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-800 font-medium"><i class="far fa-calendar-alt text-slate-600 mr-1.5"></i>{{ \Carbon\Carbon::parse($bk->bk_date)->format('d F Y') }}</td>
                                    <td class="p-4 font-bold text-red-400">-{{ $bk->bk_qty }} Kg</td>
                                    <td class="p-4 font-bold text-black">Rp {{ number_format($bk->bk_total_harga, 0, ',', '.') }}</td>
                                    <td class="p-4">
                                        @if($bk->status === 'validated')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-bold text-[11px] uppercase border border-emerald-500/20"><span class="w-1 h-1 rounded-full bg-emerald-400"></span> Validated</span>
                                        @elseif($bk->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 font-bold text-[11px] uppercase border border-red-500/20"><span class="w-1 h-1 rounded-full bg-red-400"></span> Rejected</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 font-bold text-[11px] uppercase border border-amber-500/20"><span class="w-1 h-1 rounded-full bg-amber-400 animate-pulse"></span> Pending</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center pr-6">
                                        <div class="flex items-center justify-center gap-3">
                                            <button onclick="openEditKeluar({{ $bk->bk_id }}, {{ $bk->bk_mb_id }}, {{ $bk->bk_qty }}, '{{ $bk->bk_date }}')" class="w-7 h-7 bg-slate-200 hover:bg-[#2DAA9E] text-slate-700 hover:text-[#0f111a] rounded-lg flex items-center justify-center transition-all">
                                                <i class="far fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.keluar.delete', $bk->bk_id) }}" method="POST" onsubmit="return confirm('Hapus transaksi keluar ini? Stok akan dikembalikan otomatis.')">
                                                @csrf
                                                <button type="submit" class="w-7 h-7 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-black rounded-lg flex items-center justify-center transition-all">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="p-8 text-center text-slate-600 font-medium">Belum ada pencatatan penjualan/barang keluar.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="tab-verifikasi" class="tab-content hidden space-y-6">
                <div>
                    <h2 class="text-sm font-bold tracking-wide text-slate-700 uppercase">Validasi Transaksi Penjualan</h2>
                    <p class="text-xs text-slate-600 mt-1">Verifikasi atau tolak transaksi penjualan baru untuk sinkronisasi akuntansi & stok</p>
                </div>
                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[720px]">
                            <thead>
                                <tr class="bg-white border-b border-slate-300 text-slate-700 uppercase tracking-wider font-semibold">
                                    <th class="p-4 pl-6">ID Keluar</th>
                                    <th class="p-4">Nama Produk</th>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4">Kuantitas</th>
                                    <th class="p-4">Total Pendapatan</th>
                                    <th class="p-4">Status Sesi</th>
                                    <th class="p-4 text-center pr-6">Tindakan Validasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @php $pendingItems = $barangKeluar->where('status', 'pending'); @endphp
                                @forelse($pendingItems as $pk)
                                <tr class="hover:bg-slate-50 transition-all bg-amber-500/[0.02]">
                                    <td class="p-4 pl-6 font-bold text-amber-400">#OUT-{{ $pk->bk_id }}</td>
                                    <td class="p-4">
                                        <div class="font-semibold text-black">{{ $pk->mb_desc }}</div>
                                        @if($pk->mb_grade)<div class="text-xs text-[#2DAA9E] font-bold uppercase mt-0.5">{{ $pk->mb_grade }}</div>@endif
                                    </td>
                                    <td class="p-4 font-medium text-slate-800">{{ \Carbon\Carbon::parse($pk->bk_date)->format('d F Y') }}</td>
                                    <td class="p-4 font-bold text-red-400">{{ $pk->bk_qty }} Kg</td>
                                    <td class="p-4 font-bold text-black">Rp {{ number_format($pk->bk_total_harga, 0, ',', '.') }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 font-bold text-[11px] uppercase border border-amber-500/20 flex items-center w-max gap-1">
                                            <span class="w-1 h-1 rounded-full bg-amber-400 animate-ping"></span> Menunggu Validasi
                                        </span>
                                    </td>
                                    <td class="p-4 text-center pr-6">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            <form action="{{ route('admin.keluar.validate', ['id' => $pk->bk_id, 'status' => 'validated']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-3 py-1.5 rounded-lg flex items-center gap-1 transition-all uppercase text-xs"><i class="fas fa-check"></i> SETUJUI</button>
                                            </form>
                                            <form action="{{ route('admin.keluar.validate', ['id' => $pk->bk_id, 'status' => 'rejected']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-red-500/10 hover:bg-red-500 hover:text-black border border-red-500/30 text-red-400 font-bold px-3 py-1.5 rounded-lg flex items-center gap-1 transition-all uppercase text-xs"><i class="fas fa-times"></i> TOLAK</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-600 font-medium">
                                        <div class="flex flex-col items-center justify-center py-4">
                                            <i class="fas fa-check-circle text-emerald-400 text-3xl mb-2.5 opacity-60"></i>
                                            <span>Semua transaksi penjualan telah divalidasi! Tidak ada antrean.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ======================================== -->
            <!-- TAB: DASHBOARD ANALITIK (merged from Owner) -->
            <!-- ======================================== -->
            <div id="tab-dashboard-analitik" class="tab-content hidden space-y-6">

                <!-- Filter Panel -->
                <div class="glass-card rounded-2xl p-6 shadow-xl">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                        <div>
                            <h2 class="text-base font-bold text-black flex items-center gap-2">
                                <i class="fas fa-chart-line text-[#2DAA9E]"></i> Sales & Stock Intelligence Dashboard
                            </h2>
                            <p class="text-xs text-slate-600 font-semibold tracking-wide uppercase">Analisis Performa Bisnis, Inventori & Prediksi Peramalan Penjualan</p>
                        </div>
                        <a href="{{ route('admin.export', ['start_date' => $startDate, 'end_date' => $endDate, 'grade' => $gradeFilter, 'product' => $productFilter]) }}"
                           class="sage-btn text-xs font-bold px-4 py-2.5 rounded-lg flex items-center gap-2 shadow-lg w-max shrink-0">
                            <i class="fas fa-file-csv"></i> Unduh Laporan CSV
                        </a>
                    </div>
                    <form action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
                        <input type="hidden" name="tab" value="dashboard-analitik">
                        <input type="date" name="start_date" value="{{ $startDate }}" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-black focus:outline-none focus:border-[#2DAA9E] w-full sm:w-auto">
                        <span class="text-slate-600 hidden sm:inline">s/d</span>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-black focus:outline-none focus:border-[#2DAA9E] w-full sm:w-auto">
                        <select name="product" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-black focus:outline-none focus:border-[#2DAA9E] w-full sm:w-auto">
                            <option value="">Semua Produk</option>
                            @foreach($products as $p)
                                <option value="{{ $p->mb_id }}" {{ (string)$productFilter === (string)$p->mb_id ? 'selected' : '' }}>{{ $p->mb_desc }} — {{ $p->mb_grade }}</option>
                            @endforeach
                        </select>
                        <select name="grade" class="bg-white border border-slate-300 rounded-lg px-3 py-2 text-black focus:outline-none focus:border-[#2DAA9E] w-full sm:w-auto">
                            <option value="">Semua Grade</option>
                            @foreach($availableGrades as $g)
                                <option value="{{ $g }}" {{ $gradeFilter === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="submit" class="flex-1 sm:flex-none sage-btn font-bold px-4 py-2 rounded-lg transition-all flex items-center justify-center gap-1.5">
                                <i class="fas fa-filter"></i> Terapkan
                            </button>
                            <a href="{{ route('admin.dashboard') }}?tab=dashboard-analitik" class="flex-1 sm:flex-none bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold px-3 py-2 rounded-lg transition-all text-center">Reset</a>
                        </div>
                    </form>
                </div>

                <!-- KPI GRID CARDS -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 1: Modal (Total HPP) -->
                    <div class="glass-card rounded-2xl p-6 shadow-xl relative overflow-hidden flex items-center justify-between">
                        <div class="space-y-2">
                            <span class="text-xs text-slate-600 font-bold uppercase tracking-wider block">Modal (Total HPP)</span>
                            <h3 class="text-xl font-extrabold text-red-500">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h3>
                            <span class="text-[11px] font-semibold text-slate-600 flex items-center gap-1">
                                <i class="fas fa-box-open"></i> Harga Pokok Penjualan
                            </span>
                        </div>
                        <div class="w-12 h-12 bg-red-500/10 rounded-xl flex items-center justify-center border border-red-500/20 text-red-500">
                            <i class="fas fa-file-invoice-dollar text-xl"></i>
                        </div>
                    </div>

                    <!-- Card 2: Penjualan Kotor (Revenue) -->
                    <div class="glass-card rounded-2xl p-6 shadow-xl relative overflow-hidden flex items-center justify-between">
                        <div class="space-y-2">
                            <span class="text-xs text-slate-600 font-bold uppercase tracking-wider block">Penjualan Kotor (Revenue)</span>
                            <h3 class="text-xl font-extrabold text-black">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                            <span class="text-[11px] font-semibold text-emerald-400 flex items-center gap-1">
                                <i class="fas fa-check-double"></i> Hanya Transaksi Tervalidasi
                            </span>
                        </div>
                        <div class="w-12 h-12 bg-[#2DAA9E]/10 rounded-xl flex items-center justify-center border border-[#2DAA9E]/20 text-[#2DAA9E]">
                            <i class="fas fa-wallet text-xl"></i>
                        </div>
                    </div>

                    <!-- Card 3: Profit (Laba Kotor) -->
                    <div class="glass-card rounded-2xl p-6 shadow-xl relative overflow-hidden flex items-center justify-between">
                        <div class="space-y-2">
                            <span class="text-xs text-slate-600 font-bold uppercase tracking-wider block">Profit (Laba Kotor)</span>
                            <h3 class="text-xl font-extrabold text-emerald-500">Rp {{ number_format($grossMargin, 0, ',', '.') }}</h3>
                            <span class="text-[11px] font-semibold text-slate-600 flex items-center gap-1">
                                <i class="fas fa-chart-line"></i> Penjualan Kotor - Modal
                            </span>
                        </div>
                        <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-500/20 text-emerald-500">
                            <i class="fas fa-coins text-xl"></i>
                        </div>
                    </div>

                    <!-- Card 4: Inventori & Pergerakan Stok -->
                    <div class="glass-card rounded-2xl p-5 shadow-xl relative overflow-hidden flex items-center justify-between">
                        <div class="space-y-2 w-full">
                            <span class="text-xs text-slate-600 font-bold uppercase tracking-wider block">Inventori & Pergerakan Stok</span>
                            <div class="flex items-center justify-between w-full">
                                <div>
                                    <span class="text-[11px] text-emerald-500 block font-bold">MASUK</span>
                                    <span class="text-xs font-extrabold text-black">{{ number_format($totalStockIn, 0, ',', '.') }} Kg</span>
                                </div>
                                <div class="text-slate-300 font-light text-lg">|</div>
                                <div>
                                    <span class="text-[11px] text-red-500 block font-bold">KELUAR</span>
                                    <span class="text-xs font-extrabold text-black">{{ number_format($totalStockOut, 0, ',', '.') }} Kg</span>
                                </div>
                                <div class="text-slate-300 font-light text-lg">|</div>
                                <div>
                                    <span class="text-[11px] text-blue-500 block font-bold">NET SISA</span>
                                    <span class="text-xs font-extrabold text-blue-500">{{ number_format($netUnitsAvailable, 0, ',', '.') }} Kg</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CASH FLOW HEALTH ALERT WIDGET -->
                @php
                    $isDeficit = $totalSales < $totalInventoryCost;
                    $cashStatusColor = $isDeficit ? 'orange' : 'emerald';
                    $cashIcon = $isDeficit ? 'fa-triangle-exclamation' : 'fa-shield-check';
                    $cashTitle = $isDeficit ? 'Peringatan Arus Kas (Defisit Modal)' : 'Arus Kas Sehat (Surplus Modal)';
                    
                    $selisih = abs($totalInventoryCost - $totalSales);
                @endphp
                <div class="glass-card rounded-2xl p-5 shadow-lg border-l-4 {{ $isDeficit ? 'border-orange-500 bg-orange-50/50' : 'border-emerald-500 bg-emerald-50/50' }} flex flex-col md:flex-row gap-6 items-start md:items-center justify-between">
                    <div class="flex items-start gap-4 flex-1">
                        <div class="w-12 h-12 shrink-0 rounded-full flex items-center justify-center text-xl bg-white shadow-sm text-{{ $cashStatusColor }}-500">
                            <i class="fas {{ $cashIcon }} {{ $isDeficit ? 'animate-pulse' : '' }}"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wide mb-1">{{ $cashTitle }}</h3>
                            @if($isDeficit)
                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    Total uang masuk dari penjualan (<span class="font-bold text-black">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>) 
                                    belum menutupi total modal seluruh kulakan barang (<span class="font-bold text-black">Rp {{ number_format($totalInventoryCost, 0, ',', '.') }}</span>).
                                    Uang perusahaan saat ini masih tertahan sebesar <span class="font-bold text-orange-600">Rp {{ number_format($selisih, 0, ',', '.') }}</span> dalam bentuk sisa stok <span class="font-bold">{{ number_format($netUnitsAvailable, 0, ',', '.') }} Kg</span> di gudang.
                                </p>
                            @else
                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    Hebat! Total uang masuk (<span class="font-bold text-black">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>) 
                                    sudah berhasil menutupi dan melampaui total modal keseluruhan kulakan (<span class="font-bold text-black">Rp {{ number_format($totalInventoryCost, 0, ',', '.') }}</span>).
                                    Bisnis sudah dalam status untung bersih arus kas dengan surplus sebesar <span class="font-bold text-emerald-600">Rp {{ number_format($selisih, 0, ',', '.') }}</span>.
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="shrink-0 w-full md:w-64 bg-white p-3 rounded-xl shadow-sm border border-slate-100">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-xs font-bold text-slate-500 uppercase">Inventory Turnover</span>
                            <span class="text-sm font-extrabold text-{{ $inventoryTurnover < 50 ? 'orange' : 'emerald' }}-500">{{ $inventoryTurnover }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $inventoryTurnover < 50 ? 'bg-orange-400' : 'bg-emerald-400' }}" style="width: {{ min($inventoryTurnover, 100) }}%"></div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5 text-right">Tingkat perputaran stok barang</p>
                    </div>
                </div>

                <!-- FORECASTING & PREDICTIVE ANALYTICS SECTION -->
                <div class="glow-card glass-card rounded-3xl p-6 shadow-2xl border border-[#2DAA9E]/10">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-[#2DAA9E]/20 rounded-2xl flex items-center justify-center border border-[#2DAA9E]/40 text-[#2DAA9E] shrink-0">
                                <i class="fas fa-magic text-2xl"></i>
                            </div>
                            <div class="space-y-1">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#2DAA9E]/15 border border-[#2DAA9E]/20 text-xs font-bold text-[#2DAA9E] uppercase">
                                    <i class="fas fa-brain animate-pulse"></i> Prediksi Penjualan
                                </div>
                                <h2 class="text-base font-extrabold text-black">Prediksi Volume Penjualan Bulan Depan</h2>
                                <p class="text-xs text-slate-600 font-medium">Berdasarkan data penjualan historis tervalidasi</p>
                            </div>
                        </div>

                        <!-- Forecast Result -->
                        <div class="bg-slate-50 border border-[#2DAA9E]/20 px-8 py-5 rounded-2xl flex items-center gap-5 shrink-0 shadow-lg">
                            <div class="space-y-1 text-center sm:text-right">
                                <span class="text-xs text-slate-700 font-bold uppercase tracking-wider block">Estimasi Volume</span>
                                <h4 class="text-3xl font-extrabold text-black">{{ number_format($forecastedQty, 0, ',', '.') }} <span class="text-lg text-slate-700">Kg</span></h4>
                                <p class="text-xs font-semibold flex items-center justify-center sm:justify-end gap-1.5 {{ $slope >= 0.1 ? 'text-emerald-500' : ($slope < -0.1 ? 'text-red-400' : 'text-slate-700') }}">
                                    <i class="fas {{ $slope >= 0.1 ? 'fa-arrow-trend-up' : ($slope < -0.1 ? 'fa-arrow-trend-down' : 'fa-minus') }}"></i>
                                    Tren: {{ $trendDirection }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VISUALIZATIONS GRID -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    <!-- Line Chart: Sales Trends -->
                    <div class="glass-card rounded-2xl p-6 shadow-xl flex flex-col">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-200 pb-3">
                            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#2DAA9E]"></span> Sales Revenue Trend
                            </h3>
                            <span class="text-xs text-slate-600">Berdasarkan bulan</span>
                        </div>
                        <div class="relative flex-1 min-h-[280px]">
                            <canvas id="salesTrendChart"></canvas>
                        </div>
                    </div>

                    <!-- Bar Chart: Stock Movement -->
                    <div class="glass-card rounded-2xl p-6 shadow-xl flex flex-col">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-200 pb-3">
                            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#8b5cf6]"></span> Stock Movement Comparison
                            </h3>
                            <span class="text-xs text-slate-600">Volume kuantitas Kg</span>
                        </div>
                        <div class="relative flex-1 min-h-[280px]">
                            <canvas id="stockMovementChart"></canvas>
                        </div>
                    </div>

                    <!-- Pie Chart: Grade Distribution -->
                    <div class="glass-card rounded-2xl p-6 shadow-xl flex flex-col lg:col-span-2 xl:col-span-1">
                        <div class="flex items-center justify-between mb-4 border-b border-slate-200 pb-3">
                            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Distribusi Penjualan per Grade
                            </h3>
                            <span class="text-xs text-slate-600">Tervalidasi</span>
                        </div>
                        <div class="relative flex-1 min-h-[280px] flex items-center justify-center">
                            <canvas id="gradeDistributionChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- MONTHLY PROFIT/LOSS REPORT -->
                <div class="space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-coins text-[#2DAA9E]"></i> Laporan Laba Rugi Bulanan
                            </h3>
                            <p class="text-xs text-slate-600 mt-1">Perbandingan Pendapatan, Harga Pokok Penjualan (HPP), dan Laba Kotor</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Table -->
                        <div class="glass-card rounded-2xl overflow-hidden shadow-2xl lg:col-span-2">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs min-w-[640px]">
                                    <thead>
                                        <tr class="bg-white border-b border-slate-300 text-slate-700 uppercase tracking-wider font-semibold">
                                            <th class="p-4 pl-6">Bulan</th>
                                            <th class="p-4">Pendapatan (Revenue)</th>
                                            <th class="p-4">Total HPP</th>
                                            <th class="p-4 pr-6">Laba Kotor (Gross Profit)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200">
                                        @forelse($chartLabels as $index => $monthLabel)
                                        <tr class="hover:bg-slate-50 transition-all">
                                            <td class="p-4 pl-6 font-bold text-[#2DAA9E]">{{ $monthLabel }}</td>
                                            <td class="p-4 font-bold text-slate-800">Rp {{ number_format($chartSales[$index] ?? 0, 0, ',', '.') }}</td>
                                            <td class="p-4 font-bold text-red-500">Rp {{ number_format($chartHPP[$index] ?? 0, 0, ',', '.') }}</td>
                                            <td class="p-4 pr-6 font-extrabold text-emerald-500">Rp {{ number_format($chartProfit[$index] ?? 0, 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="p-8 text-center text-slate-600 font-medium">Tidak ada data laba rugi.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Bar Chart: Profit vs HPP -->
                        <div class="glass-card rounded-2xl p-6 shadow-xl flex flex-col">
                            <div class="flex items-center justify-between mb-4 border-b border-slate-200 pb-3">
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#f59e0b]"></span> Profit & Loss Comparison
                                </h3>
                            </div>
                            <div class="relative flex-1 min-h-[280px]">
                                <canvas id="profitLossChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- DETAIL TRANSACTIONS TABLE -->
                <div class="space-y-4">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-list-ul text-[#2DAA9E]"></i> Catatan Detail Riwayat Penjualan
                            </h3>
                            <p class="text-xs text-slate-600 mt-1">Daftar transaksi penjualan terdaftar dalam jangka penyaringan</p>
                        </div>
                    </div>

                    <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs min-w-[640px]">
                                <thead>
                                    <tr class="bg-white border-b border-slate-300 text-slate-700 uppercase tracking-wider font-semibold">
                                        <th class="p-4 pl-6">ID Keluar</th>
                                        <th class="p-4">Deskripsi Produk</th>
                                        <th class="p-4">Tanggal Penjualan</th>
                                        <th class="p-4">Kuantitas</th>
                                        <th class="p-4">Total Pendapatan (IDR)</th>
                                        <th class="p-4 pr-6">Status Validasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @forelse($analyticsTransactions as $t)
                                    <tr class="hover:bg-slate-50 transition-all">
                                        <td class="p-4 pl-6 font-bold text-[#2DAA9E]">#OUT-{{ $t->bk_id }}</td>
                                        <td class="p-4">
                                            <div class="font-semibold text-black">{{ $t->mb_desc }}</div>
                                            @if($t->mb_grade)
                                                <div class="text-xs text-[#2DAA9E] font-bold uppercase mt-0.5">{{ $t->mb_grade }}</div>
                                            @endif
                                        </td>
                                        <td class="p-4 text-slate-800 font-medium"><i class="far fa-calendar-alt text-slate-600 mr-1.5"></i>{{ \Carbon\Carbon::parse($t->bk_date)->format('d F Y') }}</td>
                                        <td class="p-4 font-bold text-slate-800">{{ $t->bk_qty }} Kg</td>
                                        <td class="p-4 font-bold text-black">Rp {{ number_format($t->bk_total_harga, 0, ',', '.') }}</td>
                                        <td class="p-4 pr-6">
                                            @if($t->status === 'validated')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-bold text-[11px] uppercase border border-emerald-500/20">
                                                    <span class="w-1 h-1 rounded-full bg-emerald-400"></span> Validated
                                                </span>
                                            @elseif($t->status === 'rejected')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 font-bold text-[11px] uppercase border border-red-500/20">
                                                    <span class="w-1 h-1 rounded-full bg-red-400"></span> Rejected
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 font-bold text-[11px] uppercase border border-amber-500/20">
                                                    <span class="w-1 h-1 rounded-full bg-amber-400 animate-pulse"></span> Pending
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-600 font-medium">Tidak ada transaksi penjualan terdaftar dalam jangka penyaringan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <div id="modal-detail-barang" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-white border border-slate-300 rounded-2xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-black uppercase"><i class="fas fa-info-circle text-[#2DAA9E] mr-2"></i> Detail Produk</h3>
                <button onclick="toggleModal('modal-detail-barang')" class="text-slate-700 hover:text-black"><i class="fas fa-times"></i></button>
            </div>
            <div id="detail-barang-content" class="space-y-4 text-xs"></div>
        </div>
    </div>

    <div id="modal-add-barang" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-lg bg-white border border-slate-300 rounded-2xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-black uppercase"><i class="fas fa-box text-[#2DAA9E] mr-2"></i> Tambah Produk</h3>
                <button onclick="toggleModal('modal-add-barang')" class="text-slate-700 hover:text-black"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.barang.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Nama Produk</label>
                    <input type="text" name="mb_desc" required placeholder="Contoh: Beras Premium Super" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <div id="dynamic-grades-container" class="space-y-3">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-300 space-y-3 grade-block">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-bold text-[#2DAA9E] uppercase text-xs tracking-wider">Grade</h4>
                            <button type="button" onclick="this.closest('.grade-block').remove()" class="text-red-400 hover:text-red-500 text-xs font-bold uppercase"><i class="fas fa-times"></i> Hapus</button>
                        </div>
                        <div>
                            <input type="text" name="grades[]" placeholder="Nama Grade (Misal: A)" required class="w-full bg-white border border-slate-300 rounded-lg p-2 text-black focus:outline-none focus:border-[#2DAA9E] mb-2">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Stok Awal (Kg)</label>
                                <input type="number" name="stock[]" min="0" value="0" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                            </div>
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">HPP / Kg (Rp)</label>
                                <input type="number" name="hpp[]" min="0" step="0.01" value="0" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                            </div>
                            <div>
                                <label class="block text-slate-600 font-semibold mb-1">Keuntungan / Kg (Rp)</label>
                                <input type="number" name="profit[]" min="0" step="0.01" value="0" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addGradeBlock()" class="w-full bg-slate-200 hover:bg-slate-700 text-slate-800 font-bold py-2 rounded-lg text-xs transition-all mb-4 border border-slate-400 border-dashed">
                    <i class="fas fa-plus mr-1"></i> Tambah Grade
                </button>
                <button type="submit" class="w-full sage-btn font-bold py-2.5 rounded-lg shadow-lg">Simpan Produk</button>
            </form>
        </div>
    </div>

    <div id="modal-edit-barang" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-lg bg-white border border-slate-300 rounded-2xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-black uppercase"><i class="fas fa-box text-[#2DAA9E] mr-2"></i> Edit Produk</h3>
                <button onclick="toggleModal('modal-edit-barang')" class="text-slate-700 hover:text-black"><i class="fas fa-times"></i></button>
            </div>
            <form id="form-edit-barang" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Nama Produk</label>
                    <input type="text" name="mb_desc" id="edit-mb-desc" required class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Grade</label>
                    <input type="text" name="mb_grade" id="edit-mb-grade" required class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">HPP / Kg (Rp)</label>
                        <input type="number" name="mb_hpp" id="edit-mb-hpp" min="0" step="0.01" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                    </div>
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Keuntungan / Kg (Rp)</label>
                        <input type="number" name="mb_profit" id="edit-mb-profit" min="0" step="0.01" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                    </div>
                </div>
                <button type="submit" class="w-full sage-btn font-bold py-2.5 rounded-lg shadow-lg">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <div id="modal-add-masuk" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-white border border-slate-300 rounded-2xl shadow-2xl p-6 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-black uppercase"><i class="fas fa-arrow-circle-down text-[#2DAA9E] mr-2"></i> Catat Barang Masuk</h3>
                <button onclick="toggleModal('modal-add-masuk')" class="text-slate-700 hover:text-black"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.masuk.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Produk</label>
                    <select name="bm_mb_id" id="add-masuk-product" required class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($barang as $b)
                            <option value="{{ $b->mb_id }}">{{ $b->mb_desc }} — {{ $b->mb_grade }} (Stok: {{ $b->mb_stok }} Kg)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Jumlah Barang (Kg)</label>
                    <input type="number" name="bm_qty" required min="1" placeholder="Masukkan jumlah kg" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Tanggal Masuk</label>
                    <input type="date" name="bm_date" value="{{ today()->format('Y-m-d') }}" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <button type="submit" class="w-full sage-btn font-bold py-2.5 rounded-lg shadow-lg">Simpan Transaksi Masuk</button>
            </form>
        </div>
    </div>

    <div id="modal-edit-masuk" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-white border border-slate-300 rounded-2xl shadow-2xl p-6 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-black uppercase"><i class="fas fa-edit text-[#659287] mr-2"></i> Edit Barang Masuk</h3>
                <button onclick="toggleModal('modal-edit-masuk')" class="text-slate-700 hover:text-black"><i class="fas fa-times"></i></button>
            </div>
            <form id="form-edit-masuk" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Produk</label>
                    <select name="bm_mb_id" id="edit-masuk-product" required class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                        @foreach($barang as $b)
                            <option value="{{ $b->mb_id }}">{{ $b->mb_desc }} — {{ $b->mb_grade }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Jumlah Barang (Kg)</label>
                    <input type="number" name="bm_qty" id="edit-bm-qty" required min="1" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Tanggal Masuk</label>
                    <input type="date" name="bm_date" id="edit-bm-date" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <button type="submit" class="w-full sage-btn font-bold py-2.5 rounded-lg shadow-lg">Simpan Transaksi</button>
            </form>
        </div>
    </div>

    <div id="modal-add-keluar" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-white border border-slate-300 rounded-2xl shadow-2xl p-6 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-black uppercase"><i class="fas fa-arrow-circle-up text-red-400 mr-2"></i> Catat Penjualan</h3>
                <button onclick="toggleModal('modal-add-keluar')" class="text-slate-700 hover:text-black"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.keluar.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Produk</label>
                    <select name="bk_mb_id" id="add-keluar-product" required class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($barang as $b)
                            <option value="{{ $b->mb_id }}">{{ $b->mb_desc }} — {{ $b->mb_grade }} (Stok: {{ $b->mb_stok }} Kg | Rp {{ number_format($b->mb_hpp + $b->mb_profit, 0, ',', '.') }}/Kg)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Jumlah Barang (Kg)</label>
                    <input type="number" name="bk_qty" required min="1" placeholder="Masukkan berat kg" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Tanggal Keluar</label>
                    <input type="date" name="bk_date" value="{{ today()->format('Y-m-d') }}" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <button type="submit" class="w-full sage-btn font-bold py-2.5 rounded-lg shadow-lg">Simpan Penjualan</button>
            </form>
        </div>
    </div>

    <div id="modal-edit-keluar" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-white border border-slate-300 rounded-2xl shadow-2xl p-6 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-black uppercase"><i class="fas fa-edit text-[#659287] mr-2"></i> Edit Penjualan</h3>
                <button onclick="toggleModal('modal-edit-keluar')" class="text-slate-700 hover:text-black"><i class="fas fa-times"></i></button>
            </div>
            <form id="form-edit-keluar" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Produk</label>
                    <select name="bk_mb_id" id="edit-keluar-product" required class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                        @foreach($barang as $b)
                            <option value="{{ $b->mb_id }}">{{ $b->mb_desc }} — {{ $b->mb_grade }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Jumlah Barang (Kg)</label>
                    <input type="number" name="bk_qty" id="edit-bk-qty" required min="1" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <div>
                    <label class="block text-slate-700 font-bold mb-2">Tanggal Keluar</label>
                    <input type="date" name="bk_date" id="edit-bk-date" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                </div>
                <button type="submit" class="w-full sage-btn font-bold py-2.5 rounded-lg shadow-lg">Simpan Penjualan</button>
            </form>
        </div>
    </div>

    <script>
        function formatRupiah(n) {
            return 'Rp ' + Math.round(n).toLocaleString('id-ID');
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebar-overlay').classList.toggle('hidden');
        }

        function switchTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            document.getElementById('btn-' + tabId).classList.add('active');
            localStorage.setItem('active_admin_tab', tabId);
            if (window.innerWidth < 1024) toggleSidebar();
        }

        function toggleModal(modalId) {
            document.getElementById(modalId).classList.toggle('hidden');
        }

        function openDetailBarang(b) {
            const item = typeof b === 'string' ? JSON.parse(b) : b;
            const html = `
                <div class="font-bold text-black text-sm mb-4">${item.mb_desc}</div>
                <div class="p-3 bg-white/50 rounded-lg border border-slate-300 flex justify-between items-center">
                    <span class="font-bold text-[#2DAA9E]">${item.mb_grade}</span>
                    <div class="text-right">
                        <div class="font-bold text-[#2DAA9E]">${item.mb_stok} Kg</div>
                        <div class="text-slate-600 text-xs">${formatRupiah(parseFloat(item.mb_hpp) + parseFloat(item.mb_profit))}/Kg</div>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 text-xs">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-300">
                        <span class="text-slate-600 uppercase font-bold block mb-1">HPP / Kg</span>
                        <span class="font-bold text-black">${formatRupiah(item.mb_hpp)}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-300">
                        <span class="text-slate-600 uppercase font-bold block mb-1">Profit / Kg</span>
                        <span class="font-bold text-black">${formatRupiah(item.mb_profit)}</span>
                    </div>
                </div>`;
            document.getElementById('detail-barang-content').innerHTML = html;
            toggleModal('modal-detail-barang');
        }

        function addGradeBlock() {
            const container = document.getElementById('dynamic-grades-container');
            const html = `
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-300 space-y-3 grade-block mt-3">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-bold text-[#2DAA9E] uppercase text-xs tracking-wider">Grade Baru</h4>
                    <button type="button" onclick="this.closest('.grade-block').remove()" class="text-red-400 hover:text-red-500 text-xs font-bold uppercase"><i class="fas fa-times"></i> Hapus</button>
                </div>
                <div>
                    <input type="text" name="grades[]" placeholder="Nama Grade" required class="w-full bg-white border border-slate-300 rounded-lg p-2 text-black focus:outline-none focus:border-[#2DAA9E] mb-2">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Stok Awal (Kg)</label>
                        <input type="number" name="stock[]" min="0" value="0" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                    </div>
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">HPP / Kg (Rp)</label>
                        <input type="number" name="hpp[]" min="0" step="0.01" value="0" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                    </div>
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1">Keuntungan / Kg (Rp)</label>
                        <input type="number" name="profit[]" min="0" step="0.01" value="0" class="w-full bg-white border border-slate-300 rounded-lg p-2.5 text-black focus:outline-none focus:border-[#2DAA9E]">
                    </div>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        function openEditBarang(b) {
            const item = typeof b === 'string' ? JSON.parse(b) : b;
            document.getElementById('form-edit-barang').action = '/admin/barang/update/' + item.mb_id;
            document.getElementById('edit-mb-desc').value = item.mb_desc;
            document.getElementById('edit-mb-grade').value = item.mb_grade;
            document.getElementById('edit-mb-hpp').value = item.mb_hpp;
            document.getElementById('edit-mb-profit').value = item.mb_profit;
            toggleModal('modal-edit-barang');
        }

        function openEditMasuk(id, mbId, qty, date) {
            document.getElementById('form-edit-masuk').action = '/admin/barang-masuk/update/' + id;
            document.getElementById('edit-masuk-product').value = mbId;
            document.getElementById('edit-bm-qty').value = qty;
            document.getElementById('edit-bm-date').value = date;
            toggleModal('modal-edit-masuk');
        }

        function openEditKeluar(id, mbId, qty, date) {
            document.getElementById('form-edit-keluar').action = '/admin/barang-keluar/update/' + id;
            document.getElementById('edit-keluar-product').value = mbId;
            document.getElementById('edit-bk-qty').value = qty;
            document.getElementById('edit-bk-date').value = date;
            toggleModal('modal-edit-keluar');
        }

        // ==========================================
        // CHART.JS ANALYTICS RENDER (merged from Owner)
        // ==========================================
        const accentPurple = '#5b50e6';
        const textMuted = '#475569';
        const gridColor = 'rgba(0, 0, 0, 0.05)';

        const chartLabels = @json($chartLabels);
        const salesData = @json($chartSales);
        const stockInData = @json($chartStockIn);
        const stockOutData = @json($chartStockOut);
        const hppData = @json($chartHPP);
        const profitData = @json($chartProfit);
        const gradeLabels = @json($gradeDistribution->pluck('mb_grade'));
        const gradeQty = @json($gradeDistribution->pluck('total_qty'));

        Chart.defaults.color = textMuted;
        Chart.defaults.font.family = 'Outfit';
        Chart.defaults.font.size = 11;

        function initAnalyticsCharts() {
            const salesCanvas = document.getElementById('salesTrendChart');
            const stockCanvas = document.getElementById('stockMovementChart');
            const gradeCanvas = document.getElementById('gradeDistributionChart');

            if (!salesCanvas || !stockCanvas || !gradeCanvas) return;

            // 1. Sales Revenue Trend (Line Chart)
            const ctxSales = salesCanvas.getContext('2d');
            const salesGradient = ctxSales.createLinearGradient(0, 0, 0, 300);
            salesGradient.addColorStop(0, 'rgba(45, 170, 158, 0.45)');
            salesGradient.addColorStop(1, 'rgba(45, 170, 158, 0)');

            new Chart(ctxSales, {
                type: 'line',
                data: {
                    labels: chartLabels,
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
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: gridColor }, ticks: { padding: 8 } },
                        y: { grid: { color: gridColor }, ticks: { padding: 8, callback: function(value) { return 'Rp ' + value.toLocaleString('id-ID'); } } }
                    }
                }
            });

            // 2. Stock In vs Stock Out (Bar Chart)
            const ctxStock = stockCanvas.getContext('2d');
            new Chart(ctxStock, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        { label: 'Stok Masuk (IN)', data: stockInData, backgroundColor: 'rgba(16, 185, 129, 0.85)', borderRadius: 6, maxBarThickness: 15 },
                        { label: 'Stok Keluar (OUT)', data: stockOutData, backgroundColor: 'rgba(239, 68, 68, 0.85)', borderRadius: 6, maxBarThickness: 15 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', labels: { color: '#000000', boxWidth: 10, boxHeight: 10, padding: 12 } } },
                    scales: {
                        x: { grid: { color: gridColor }, ticks: { padding: 8 } },
                        y: { grid: { color: gridColor }, ticks: { padding: 8, callback: function(value) { return value + ' Kg'; } } }
                    }
                }
            });

            // 3. Grade Distribution (Doughnut)
            const ctxGrade = gradeCanvas.getContext('2d');
            new Chart(ctxGrade, {
                type: 'doughnut',
                data: {
                    labels: gradeLabels.map(g => 'Grade ' + g),
                    datasets: [{
                        data: gradeQty,
                        backgroundColor: ['rgba(45, 170, 158, 0.85)', 'rgba(102, 210, 206, 0.85)', 'rgba(16, 185, 129, 0.85)', 'rgba(91, 80, 230, 0.85)', 'rgba(139, 92, 246, 0.85)'],
                        borderColor: '#ffffff',
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { color: '#000000', boxWidth: 10, padding: 12 } } }
                }
            });

            // 4. Profit and Loss (Stacked Bar Chart)
            const ctxProfit = document.getElementById('profitLossChart');
            if (ctxProfit) {
                new Chart(ctxProfit.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: chartLabels,
                        datasets: [
                            { label: 'Laba Kotor (Profit)', data: profitData, backgroundColor: 'rgba(16, 185, 129, 0.85)', borderRadius: 4 },
                            { label: 'Total HPP', data: hppData, backgroundColor: 'rgba(239, 68, 68, 0.85)', borderRadius: 4 }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'top', labels: { color: '#000000', boxWidth: 10, boxHeight: 10, padding: 12 } } },
                        scales: {
                            x: { grid: { color: gridColor }, ticks: { padding: 8 }, stacked: true },
                            y: { grid: { color: gridColor }, ticks: { padding: 8, callback: function(value) { return 'Rp ' + (value/1000000).toFixed(1) + 'M'; } }, stacked: true }
                        }
                    }
                });
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            // Check for tab query param
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            const savedTab = tabParam || localStorage.getItem('active_admin_tab');
            if (savedTab && document.getElementById('tab-' + savedTab)) {
                switchTab(savedTab);
            }
            // Initialize charts
            initAnalyticsCharts();
        });
    </script>
</body>
</html>
