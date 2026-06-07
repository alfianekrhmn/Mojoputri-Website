<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | CV Mojoputri</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #0f111a; color: #ffffff; }
        .sidebar { background-color: #0c0d16; border-right: 1px solid rgba(255,255,255,0.03); }
        .glass-card { background-color: #1a1d2e; border: 1px solid rgba(255,255,255,0.05); }
        .purple-btn { background-color: #5b50e6; transition: all 0.2s ease; }
        .purple-btn:hover { background-color: #4c42c2; box-shadow: 0 0 15px rgba(91,80,230,0.35); }
        .tab-btn.active { background-color: rgba(91,80,230,0.15); color: #8b5cf6; border-left: 3px solid #5b50e6; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f111a; }
        ::-webkit-scrollbar-thumb { background: #1a1d2e; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #5b50e6; }
        @media (max-width: 1023px) {
            .sidebar-mobile { transform: translateX(-100%); transition: transform 0.3s ease; }
            .sidebar-mobile.open { transform: translateX(0); }
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Mobile overlay -->
    <div id="sidebar-overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-black/60 z-40 hidden lg:hidden"></div>

    <!-- SIDEBAR -->
    <div id="sidebar" class="fixed lg:static inset-y-0 left-0 z-50 w-64 sidebar sidebar-mobile flex flex-col justify-between shrink-0">
        <div class="p-6">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#5b50e6] to-[#8b5cf6] flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="fas fa-chart-pie text-white text-base"></i>
                </div>
                <div>
                    <h1 class="text-sm font-extrabold tracking-wider text-white">CV MOJOPUTRI</h1>
                    <span class="text-[10px] text-slate-500 font-semibold tracking-widest uppercase">Admin Portal</span>
                </div>
            </div>

            <div class="flex items-center space-x-3 p-3 bg-slate-900/60 rounded-xl mb-6 border border-slate-800/40">
                <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20">
                    <i class="fas fa-user-shield text-[#5b50e6] text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xs font-bold text-white">Administrator</h2>
                    <span class="text-[10px] text-emerald-400 font-semibold uppercase flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Online
                    </span>
                </div>
            </div>

            <nav class="space-y-1">
                <button onclick="switchTab('master-barang')" id="btn-master-barang" class="tab-btn active w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/30 text-xs font-semibold tracking-wide transition-all text-left">
                    <i class="fas fa-boxes text-base w-5"></i><span>Master Barang</span>
                </button>
                <button onclick="switchTab('barang-masuk')" id="btn-barang-masuk" class="tab-btn w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/30 text-xs font-semibold tracking-wide transition-all text-left">
                    <i class="fas fa-arrow-circle-down text-base w-5"></i><span>Barang Masuk (Stok)</span>
                </button>
                <button onclick="switchTab('barang-keluar')" id="btn-barang-keluar" class="tab-btn w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/30 text-xs font-semibold tracking-wide transition-all text-left">
                    <i class="fas fa-arrow-circle-up text-base w-5"></i><span>Barang Keluar (Sales)</span>
                </button>
                <button onclick="switchTab('verifikasi')" id="btn-verifikasi" class="tab-btn w-full flex items-center space-x-3.5 px-4 py-3 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900/30 text-xs font-semibold tracking-wide transition-all text-left">
                    <i class="fas fa-check-double text-base w-5"></i><span>Verifikasi Penjualan</span>
                    @php $pendingCount = $barangKeluar->where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto bg-amber-500 text-slate-950 font-bold px-1.5 py-0.5 rounded text-[9px]">{{ $pendingCount }}</span>
                    @endif
                </button>
            </nav>
        </div>
        <div class="p-6 border-t border-slate-900">
            <a href="{{ route('logout') }}" class="w-full flex items-center justify-center space-x-2.5 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 py-2.5 rounded-lg text-xs font-bold transition-all">
                <i class="fas fa-sign-out-alt"></i><span>Keluar Aplikasi</span>
            </a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col overflow-hidden relative w-full">
        <header class="h-auto min-h-16 border-b border-slate-900 flex flex-col sm:flex-row sm:items-center sm:justify-between px-4 sm:px-8 py-3 shrink-0 bg-[#0f111a] gap-3">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg bg-slate-800 text-slate-300 hover:text-white">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <i class="fas fa-solar-panel text-slate-500"></i> Portal Operasional Admin
                    </h1>
                    <p class="text-[10px] text-slate-500 font-semibold tracking-wide uppercase hidden sm:block">Kelola Data Master Penjualan & Inventaris CV Mojoputri</p>
                </div>
            </div>
            <span class="text-xs text-slate-400"><i class="far fa-calendar-alt mr-1.5"></i> Hari Ini: <span class="font-bold text-white">{{ today()->format('d M Y') }}</span></span>
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

            <!-- TAB 1: MASTER BARANG -->
            <div id="tab-master-barang" class="tab-content space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold tracking-wide text-slate-400 uppercase">Master Data Produk</h2>
                        <p class="text-xs text-slate-500 mt-1">Daftar beras dan hasil produksi Mojoputri</p>
                    </div>
                    <button onclick="toggleModal('modal-add-barang')" class="purple-btn text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/10 w-full sm:w-auto">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </button>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[640px]">
                            <thead>
                                <tr class="bg-slate-900/80 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                                    <th class="p-4 pl-6">ID</th>
                                    <th class="p-4">Nama Produk</th>
                                    <th class="p-4">Stok Tersedia</th>
                                    <th class="p-4">Harga / Kg</th>
                                    <th class="p-4 text-center pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @forelse($barang as $b)
                                @php
                                    $prices = $b->grades->map(fn($g) => $g->pg_hpp + $g->pg_profit);
                                    $minPrice = $prices->min() ?? 0;
                                    $maxPrice = $prices->max() ?? 0;
                                @endphp
                                <tr class="hover:bg-slate-900/20 transition-all">
                                    <td class="p-4 pl-6 font-bold text-indigo-400">#{{ $b->mb_id }}</td>
                                    <td class="p-4 font-semibold text-white">{{ $b->mb_desc }}</td>
                                    <td class="p-4 font-bold text-slate-300">
                                        @if($b->mb_stok < 50)
                                            <span class="text-red-400"><i class="fas fa-exclamation-circle mr-1"></i> {{ $b->mb_stok }} Kg</span>
                                        @else
                                            <span class="text-emerald-400"><i class="fas fa-cubes mr-1 text-emerald-500/70"></i> {{ $b->mb_stok }} Kg</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-300 font-medium">
                                        @if($minPrice == $maxPrice)
                                            Rp {{ number_format($minPrice, 0, ',', '.') }}
                                        @else
                                            Rp {{ number_format($minPrice, 0, ',', '.') }} – {{ number_format($maxPrice, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="p-4 text-center pr-6">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            <button onclick='openDetailBarang(@json($b->mb_id), @json($b->mb_desc), @json($b->grades))' class="px-2.5 py-1.5 bg-indigo-500/10 hover:bg-indigo-500 text-indigo-400 hover:text-white rounded-lg text-[10px] font-bold transition-all">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </button>
                                            <button onclick='openEditBarang(@json($b->mb_id), @json($b->mb_desc), @json($b->grades))' class="w-7 h-7 bg-slate-800 hover:bg-[#5b50e6] text-slate-400 hover:text-white rounded-lg flex items-center justify-center transition-all">
                                                <i class="far fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.barang.delete', $b->mb_id) }}" method="POST" onsubmit="return confirm('Hapus produk ini dan seluruh riwayat transaksi terkait?')">
                                                @csrf
                                                <button type="submit" class="w-7 h-7 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white rounded-lg flex items-center justify-center transition-all">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="p-8 text-center text-slate-500 font-medium">Belum ada data barang tersedia.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 2: BARANG MASUK -->
            <div id="tab-barang-masuk" class="tab-content hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold tracking-wide text-slate-400 uppercase">Penerimaan Barang Masuk</h2>
                        <p class="text-xs text-slate-500 mt-1">Catat penambahan stok hasil produksi/penyetokan</p>
                    </div>
                    <button onclick="toggleModal('modal-add-masuk')" class="purple-btn text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/10 w-full sm:w-auto">
                        <i class="fas fa-plus"></i> Catat Barang Masuk
                    </button>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[600px]">
                            <thead>
                                <tr class="bg-slate-900/80 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                                    <th class="p-4 pl-6">ID Masuk</th>
                                    <th class="p-4">Nama Produk</th>
                                    <th class="p-4">Tanggal Masuk</th>
                                    <th class="p-4">Jumlah</th>
                                    <th class="p-4 text-center pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @forelse($barangMasuk as $bm)
                                <tr class="hover:bg-slate-900/20 transition-all">
                                    <td class="p-4 pl-6 font-bold text-indigo-400">#IN-{{ $bm->bm_id }}</td>
                                    <td class="p-4">
                                        <div class="font-semibold text-white">{{ $bm->mb_desc }}</div>
                                        @if($bm->bm_grade)
                                            <div class="text-[10px] text-indigo-400 font-bold uppercase mt-0.5">Grade {{ $bm->bm_grade }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-300 font-medium"><i class="far fa-calendar-alt text-slate-600 mr-1.5"></i>{{ \Carbon\Carbon::parse($bm->bm_date)->format('d F Y') }}</td>
                                    <td class="p-4 font-bold text-emerald-400">+{{ $bm->bm_qty }} Kg</td>
                                    <td class="p-4 text-center pr-6">
                                        <div class="flex items-center justify-center gap-3">
                                            <button onclick="openEditMasuk({{ $bm->bm_id }}, {{ $bm->bm_mb_id }}, '{{ $bm->bm_grade }}', {{ $bm->bm_qty }}, '{{ $bm->bm_date }}')" class="w-7 h-7 bg-slate-800 hover:bg-[#5b50e6] text-slate-400 hover:text-white rounded-lg flex items-center justify-center transition-all">
                                                <i class="far fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.masuk.delete', $bm->bm_id) }}" method="POST" onsubmit="return confirm('Hapus transaksi masuk ini? Stok akan berkurang otomatis.')">
                                                @csrf
                                                <button type="submit" class="w-7 h-7 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white rounded-lg flex items-center justify-center transition-all">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="p-8 text-center text-slate-500 font-medium">Belum ada pencatatan barang masuk.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 3: BARANG KELUAR -->
            <div id="tab-barang-keluar" class="tab-content hidden space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold tracking-wide text-slate-400 uppercase">Pencatatan Penjualan (Barang Keluar)</h2>
                        <p class="text-xs text-slate-500 mt-1">Catat transaksi penjualan beras ke pelanggan</p>
                    </div>
                    <button onclick="toggleModal('modal-add-keluar')" class="purple-btn text-white text-xs font-bold px-4 py-2.5 rounded-lg flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/10 w-full sm:w-auto">
                        <i class="fas fa-plus"></i> Catat Penjualan Baru
                    </button>
                </div>

                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[720px]">
                            <thead>
                                <tr class="bg-slate-900/80 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                                    <th class="p-4 pl-6">ID Keluar</th>
                                    <th class="p-4">Nama Produk</th>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4">Jumlah</th>
                                    <th class="p-4">Total Pendapatan</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-center pr-6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @forelse($barangKeluar as $bk)
                                <tr class="hover:bg-slate-900/20 transition-all">
                                    <td class="p-4 pl-6 font-bold text-indigo-400">#OUT-{{ $bk->bk_id }}</td>
                                    <td class="p-4">
                                        <div class="font-semibold text-white">{{ $bk->mb_desc }}</div>
                                        @if($bk->bk_grade)
                                            <div class="text-[10px] text-indigo-400 font-bold uppercase mt-0.5">Grade {{ $bk->bk_grade }}</div>
                                        @endif
                                    </td>
                                    <td class="p-4 text-slate-300 font-medium"><i class="far fa-calendar-alt text-slate-600 mr-1.5"></i>{{ \Carbon\Carbon::parse($bk->bk_date)->format('d F Y') }}</td>
                                    <td class="p-4 font-bold text-red-400">-{{ $bk->bk_qty }} Kg</td>
                                    <td class="p-4 font-bold text-white">Rp {{ number_format($bk->bk_total_harga, 0, ',', '.') }}</td>
                                    <td class="p-4">
                                        @if($bk->status === 'validated')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 font-bold text-[9px] uppercase border border-emerald-500/20"><span class="w-1 h-1 rounded-full bg-emerald-400"></span> Validated</span>
                                        @elseif($bk->status === 'rejected')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-red-500/10 text-red-400 font-bold text-[9px] uppercase border border-red-500/20"><span class="w-1 h-1 rounded-full bg-red-400"></span> Rejected</span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-400 font-bold text-[9px] uppercase border border-amber-500/20"><span class="w-1 h-1 rounded-full bg-amber-400 animate-pulse"></span> Pending</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center pr-6">
                                        <div class="flex items-center justify-center gap-3">
                                            <button onclick="openEditKeluar({{ $bk->bk_id }}, {{ $bk->bk_mb_id }}, '{{ $bk->bk_grade }}', {{ $bk->bk_qty }}, '{{ $bk->bk_date }}')" class="w-7 h-7 bg-slate-800 hover:bg-[#5b50e6] text-slate-400 hover:text-white rounded-lg flex items-center justify-center transition-all">
                                                <i class="far fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.keluar.delete', $bk->bk_id) }}" method="POST" onsubmit="return confirm('Hapus transaksi keluar ini? Stok akan dikembalikan otomatis.')">
                                                @csrf
                                                <button type="submit" class="w-7 h-7 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white rounded-lg flex items-center justify-center transition-all">
                                                    <i class="far fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="p-8 text-center text-slate-500 font-medium">Belum ada pencatatan penjualan/barang keluar.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 4: VERIFIKASI -->
            <div id="tab-verifikasi" class="tab-content hidden space-y-6">
                <div>
                    <h2 class="text-sm font-bold tracking-wide text-slate-400 uppercase">Validasi Transaksi Penjualan</h2>
                    <p class="text-xs text-slate-500 mt-1">Verifikasi atau tolak transaksi penjualan baru untuk sinkronisasi akuntansi & stok</p>
                </div>
                <div class="glass-card rounded-2xl overflow-hidden shadow-2xl">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs min-w-[720px]">
                            <thead>
                                <tr class="bg-slate-900/80 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                                    <th class="p-4 pl-6">ID Keluar</th>
                                    <th class="p-4">Nama Produk</th>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4">Kuantitas</th>
                                    <th class="p-4">Total Pendapatan</th>
                                    <th class="p-4">Status Sesi</th>
                                    <th class="p-4 text-center pr-6">Tindakan Validasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @php $pendingItems = $barangKeluar->where('status', 'pending'); @endphp
                                @forelse($pendingItems as $pk)
                                <tr class="hover:bg-slate-900/20 transition-all bg-amber-500/[0.02]">
                                    <td class="p-4 pl-6 font-bold text-amber-400">#OUT-{{ $pk->bk_id }}</td>
                                    <td class="p-4">
                                        <div class="font-semibold text-white">{{ $pk->mb_desc }}</div>
                                        @if($pk->bk_grade)<div class="text-[10px] text-indigo-400 font-bold uppercase mt-0.5">Grade {{ $pk->bk_grade }}</div>@endif
                                    </td>
                                    <td class="p-4 font-medium text-slate-300">{{ \Carbon\Carbon::parse($pk->bk_date)->format('d F Y') }}</td>
                                    <td class="p-4 font-bold text-red-400">{{ $pk->bk_qty }} Kg</td>
                                    <td class="p-4 font-bold text-white">Rp {{ number_format($pk->bk_total_harga, 0, ',', '.') }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 font-bold text-[9px] uppercase border border-amber-500/20 flex items-center w-max gap-1">
                                            <span class="w-1 h-1 rounded-full bg-amber-400 animate-ping"></span> Menunggu Validasi
                                        </span>
                                    </td>
                                    <td class="p-4 text-center pr-6">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            <form action="{{ route('admin.keluar.validate', ['id' => $pk->bk_id, 'status' => 'validated']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold px-3 py-1.5 rounded-lg flex items-center gap-1 transition-all uppercase text-[10px]"><i class="fas fa-check"></i> SETUJUI</button>
                                            </form>
                                            <form action="{{ route('admin.keluar.validate', ['id' => $pk->bk_id, 'status' => 'rejected']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-red-500/10 hover:bg-red-500 hover:text-white border border-red-500/30 text-red-400 font-bold px-3 py-1.5 rounded-lg flex items-center gap-1 transition-all uppercase text-[10px]"><i class="fas fa-times"></i> TOLAK</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-500 font-medium">
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
        </main>
    </div>

    <!-- MODAL: DETAIL BARANG -->
    <div id="modal-detail-barang" class="fixed inset-0 bg-[#000]/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-[#1a1d2e] border border-slate-800 rounded-2xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-white uppercase"><i class="fas fa-info-circle text-indigo-400 mr-2"></i> Detail Produk</h3>
                <button onclick="toggleModal('modal-detail-barang')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <div id="detail-barang-content" class="space-y-4 text-xs"></div>
        </div>
    </div>

    <!-- MODAL: ADD BARANG -->
    <div id="modal-add-barang" class="fixed inset-0 bg-[#000]/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-lg bg-[#1a1d2e] border border-slate-800 rounded-2xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-white uppercase"><i class="fas fa-box text-indigo-400 mr-2"></i> Tambah Produk</h3>
                <button onclick="toggleModal('modal-add-barang')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.barang.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Nama Produk</label>
                    <input type="text" name="mb_desc" required placeholder="Contoh: Beras Premium Super" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <div id="dynamic-grades-container" class="space-y-3">
                    <div class="p-4 bg-slate-900/40 rounded-xl border border-slate-800 space-y-3 grade-block">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-bold text-indigo-400 uppercase text-[10px] tracking-wider">Grade</h4>
                            <button type="button" onclick="this.closest('.grade-block').remove()" class="text-red-400 hover:text-red-500 text-[10px] font-bold uppercase"><i class="fas fa-times"></i> Hapus</button>
                        </div>
                        <div>
                            <input type="text" name="grades[]" placeholder="Nama Grade (Misal: A)" required class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2 text-white focus:outline-none focus:border-[#5b50e6] mb-2">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-500 font-semibold mb-1">Stok Awal (Kg)</label>
                                <input type="number" name="stock[]" min="0" value="0" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                            </div>
                            <div>
                                <label class="block text-slate-500 font-semibold mb-1">HPP / Kg (Rp)</label>
                                <input type="number" name="hpp[]" min="0" step="0.01" value="0" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                            </div>
                            <div>
                                <label class="block text-slate-500 font-semibold mb-1">Keuntungan / Kg (Rp)</label>
                                <input type="number" name="profit[]" min="0" step="0.01" value="0" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addGradeBlock()" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-2 rounded-lg text-xs transition-all mb-4 border border-slate-700 border-dashed">
                    <i class="fas fa-plus mr-1"></i> Tambah Grade
                </button>
                <button type="submit" class="w-full purple-btn text-white font-bold py-2.5 rounded-lg shadow-lg">Simpan Produk</button>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT BARANG -->
    <div id="modal-edit-barang" class="fixed inset-0 bg-[#000]/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-lg bg-[#1a1d2e] border border-slate-800 rounded-2xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-white uppercase"><i class="fas fa-box text-indigo-400 mr-2"></i> Edit Produk</h3>
                <button onclick="toggleModal('modal-edit-barang')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form id="form-edit-barang" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Nama Produk</label>
                    <input type="text" name="mb_desc" id="edit-mb-desc" required class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <div id="edit-grade-pricing" class="space-y-3"></div>
                <button type="button" onclick="addEditGradeBlock()" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold py-2 rounded-lg text-xs transition-all mb-4 border border-slate-700 border-dashed">
                    <i class="fas fa-plus mr-1"></i> Tambah Grade
                </button>
                <button type="submit" class="w-full purple-btn text-white font-bold py-2.5 rounded-lg shadow-lg">Simpan Perubahan</button>
            </form>
        </div>
    </div>

    <!-- MODAL: ADD BARANG MASUK -->
    <div id="modal-add-masuk" class="fixed inset-0 bg-[#000]/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-[#1a1d2e] border border-slate-800 rounded-2xl shadow-2xl p-6 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-white uppercase"><i class="fas fa-arrow-circle-down text-emerald-400 mr-2"></i> Catat Barang Masuk</h3>
                <button onclick="toggleModal('modal-add-masuk')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.masuk.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Nama Barang</label>
                    <select name="bm_mb_id" id="add-masuk-product" required onchange="loadGrades(this.value, 'add-masuk-grade', 'add-masuk-stock-hint')" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($barang as $b)
                            <option value="{{ $b->mb_id }}">{{ $b->mb_desc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Grade</label>
                    <select name="bm_grade" id="add-masuk-grade" required class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                        <option value="">-- Pilih Grade --</option>
                    </select>
                    <p id="add-masuk-stock-hint" class="text-[10px] text-slate-500 mt-1"></p>
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Jumlah Barang (Kg)</label>
                    <input type="number" name="bm_qty" required min="1" placeholder="Masukkan jumlah kg" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Tanggal Masuk</label>
                    <input type="date" name="bm_date" value="{{ today()->format('Y-m-d') }}" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <button type="submit" class="w-full purple-btn text-white font-bold py-2.5 rounded-lg shadow-lg">Simpan Transaksi Masuk</button>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT BARANG MASUK -->
    <div id="modal-edit-masuk" class="fixed inset-0 bg-[#000]/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-[#1a1d2e] border border-slate-800 rounded-2xl shadow-2xl p-6 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-white uppercase"><i class="fas fa-edit text-[#5b50e6] mr-2"></i> Edit Barang Masuk</h3>
                <button onclick="toggleModal('modal-edit-masuk')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form id="form-edit-masuk" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Nama Barang</label>
                    <select name="bm_mb_id" id="edit-masuk-product" required onchange="loadGrades(this.value, 'edit-masuk-grade', 'edit-masuk-stock-hint')" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                        @foreach($barang as $b)
                            <option value="{{ $b->mb_id }}">{{ $b->mb_desc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Grade</label>
                    <select name="bm_grade" id="edit-masuk-grade" required class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]"></select>
                    <p id="edit-masuk-stock-hint" class="text-[10px] text-slate-500 mt-1"></p>
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Jumlah Barang (Kg)</label>
                    <input type="number" name="bm_qty" id="edit-bm-qty" required min="1" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Tanggal Masuk</label>
                    <input type="date" name="bm_date" id="edit-bm-date" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <button type="submit" class="w-full purple-btn text-white font-bold py-2.5 rounded-lg shadow-lg">Simpan Transaksi</button>
            </form>
        </div>
    </div>

    <!-- MODAL: ADD BARANG KELUAR -->
    <div id="modal-add-keluar" class="fixed inset-0 bg-[#000]/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-[#1a1d2e] border border-slate-800 rounded-2xl shadow-2xl p-6 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-white uppercase"><i class="fas fa-arrow-circle-up text-red-400 mr-2"></i> Catat Penjualan</h3>
                <button onclick="toggleModal('modal-add-keluar')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.keluar.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Nama Produk</label>
                    <select name="bk_mb_id" id="add-keluar-product" required onchange="loadGrades(this.value, 'add-keluar-grade', 'add-keluar-stock-hint')" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($barang as $b)
                            <option value="{{ $b->mb_id }}">{{ $b->mb_desc }} (Stok: {{ $b->mb_stok }} Kg)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Grade</label>
                    <select name="bk_grade" id="add-keluar-grade" required class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                        <option value="">-- Pilih Grade --</option>
                    </select>
                    <p id="add-keluar-stock-hint" class="text-[10px] text-slate-500 mt-1"></p>
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Jumlah Barang (Kg)</label>
                    <input type="number" name="bk_qty" required min="1" placeholder="Masukkan berat kg" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Tanggal Keluar</label>
                    <input type="date" name="bk_date" value="{{ today()->format('Y-m-d') }}" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <button type="submit" class="w-full purple-btn text-white font-bold py-2.5 rounded-lg shadow-lg">Simpan Penjualan</button>
            </form>
        </div>
    </div>

    <!-- MODAL: EDIT BARANG KELUAR -->
    <div id="modal-edit-keluar" class="fixed inset-0 bg-[#000]/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="w-full max-w-md bg-[#1a1d2e] border border-slate-800 rounded-2xl shadow-2xl p-6 relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-bold text-white uppercase"><i class="fas fa-edit text-[#5b50e6] mr-2"></i> Edit Penjualan</h3>
                <button onclick="toggleModal('modal-edit-keluar')" class="text-slate-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form id="form-edit-keluar" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Nama Produk</label>
                    <select name="bk_mb_id" id="edit-keluar-product" required onchange="loadGrades(this.value, 'edit-keluar-grade', 'edit-keluar-stock-hint')" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                        @foreach($barang as $b)
                            <option value="{{ $b->mb_id }}">{{ $b->mb_desc }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Grade</label>
                    <select name="bk_grade" id="edit-keluar-grade" required class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]"></select>
                    <p id="edit-keluar-stock-hint" class="text-[10px] text-slate-500 mt-1"></p>
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Jumlah Barang (Kg)</label>
                    <input type="number" name="bk_qty" id="edit-bk-qty" required min="1" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <div>
                    <label class="block text-slate-400 font-bold mb-2">Tanggal Keluar</label>
                    <input type="date" name="bk_date" id="edit-bk-date" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                </div>
                <button type="submit" class="w-full purple-btn text-white font-bold py-2.5 rounded-lg shadow-lg">Simpan Penjualan</button>
            </form>
        </div>
    </div>

    <script>
        const productsData = @json($productsData);

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

        function loadGrades(productId, gradeSelectId, hintId, selectedGrade = null) {
            const select = document.getElementById(gradeSelectId);
            const hint = document.getElementById(hintId);
            select.innerHTML = '<option value="">-- Pilih Grade --</option>';
            hint.textContent = '';

            if (!productId) return;

            const product = productsData.find(p => p.id == productId);
            if (!product || !product.grades.length) {
                hint.textContent = 'Produk belum memiliki data grade.';
                return;
            }

            product.grades.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.grade;
                opt.textContent = `Grade ${g.grade} — Stok: ${g.stock} Kg — ${formatRupiah(g.hpp + g.profit)}/Kg`;
                if (selectedGrade && selectedGrade === g.grade) opt.selected = true;
                select.appendChild(opt);
            });

            select.onchange = () => {
                const g = product.grades.find(x => x.grade === select.value);
                hint.textContent = g ? `Stok tersedia Grade ${g.grade}: ${g.stock} Kg | Harga: ${formatRupiah(g.hpp + g.profit)}/Kg` : '';
            };
            select.dispatchEvent(new Event('change'));
        }

        function openDetailBarang(id, desc, grades) {
            const gradesArr = typeof grades === 'string' ? JSON.parse(grades) : grades;
            let totalStock = 0;
            let html = `<div class="font-bold text-white text-sm mb-4">${desc}</div>`;
            html += '<div class="space-y-2">';
            gradesArr.forEach(g => {
                const stock = g.pg_stock ?? g.stock ?? 0;
                const hpp = g.pg_hpp ?? g.hpp ?? 0;
                const profit = g.pg_profit ?? g.profit ?? 0;
                const grade = g.pg_grade ?? g.grade;
                totalStock += parseInt(stock);
                html += `<div class="flex justify-between items-center p-3 bg-slate-900/50 rounded-lg border border-slate-800">
                    <span class="font-bold text-indigo-400">Grade ${grade}</span>
                    <div class="text-right">
                        <div class="font-bold text-emerald-400">${stock} Kg</div>
                        <div class="text-slate-500 text-[10px]">${formatRupiah(parseFloat(hpp) + parseFloat(profit))}/Kg</div>
                    </div>
                </div>`;
            });
            html += '</div>';
            html += `<div class="mt-4 p-3 bg-indigo-500/10 rounded-lg border border-indigo-500/20 text-center">
                <span class="text-slate-400 text-[10px] uppercase font-bold">Total Stok Tersedia</span>
                <div class="text-lg font-extrabold text-white mt-1">${totalStock} Kg</div>
            </div>`;
            document.getElementById('detail-barang-content').innerHTML = html;
            toggleModal('modal-detail-barang');
        }

        function addGradeBlock() {
            const container = document.getElementById('dynamic-grades-container');
            const html = `
            <div class="p-4 bg-slate-900/40 rounded-xl border border-slate-800 space-y-3 grade-block mt-3">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-bold text-indigo-400 uppercase text-[10px] tracking-wider">Grade Baru</h4>
                    <button type="button" onclick="this.closest('.grade-block').remove()" class="text-red-400 hover:text-red-500 text-[10px] font-bold uppercase"><i class="fas fa-times"></i> Hapus</button>
                </div>
                <div>
                    <input type="text" name="grades[]" placeholder="Nama Grade" required class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2 text-white focus:outline-none focus:border-[#5b50e6] mb-2">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">Stok Awal (Kg)</label>
                        <input type="number" name="stock[]" min="0" value="0" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                    </div>
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">HPP / Kg (Rp)</label>
                        <input type="number" name="hpp[]" min="0" step="0.01" value="0" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                    </div>
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">Keuntungan / Kg (Rp)</label>
                        <input type="number" name="profit[]" min="0" step="0.01" value="0" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                    </div>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        function addEditGradeBlock() {
            const container = document.getElementById('edit-grade-pricing');
            const html = `
            <div class="p-4 bg-slate-900/40 rounded-xl border border-slate-800 space-y-3 grade-block mt-3">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-bold text-indigo-400 uppercase text-[10px] tracking-wider">Grade Baru</h4>
                    <button type="button" onclick="this.closest('.grade-block').remove()" class="text-red-400 hover:text-red-500 text-[10px] font-bold uppercase"><i class="fas fa-times"></i> Hapus</button>
                </div>
                <div>
                    <input type="text" name="grades[]" placeholder="Nama Grade" required class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2 text-white focus:outline-none focus:border-[#5b50e6] mb-2">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">HPP / Kg (Rp)</label>
                        <input type="number" name="hpp[]" min="0" step="0.01" value="0" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                    </div>
                    <div>
                        <label class="block text-slate-500 font-semibold mb-1">Keuntungan / Kg (Rp)</label>
                        <input type="number" name="profit[]" min="0" step="0.01" value="0" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                    </div>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }

        function openEditBarang(id, desc, grades) {
            document.getElementById('form-edit-barang').action = '/admin/barang/update/' + id;
            document.getElementById('edit-mb-desc').value = desc;
            const gradesArr = typeof grades === 'string' ? JSON.parse(grades) : grades;
            const container = document.getElementById('edit-grade-pricing');
            container.innerHTML = '';
            gradesArr.forEach(g => {
                const grade = g.pg_grade ?? g.grade;
                const hpp = g.pg_hpp ?? g.hpp ?? 0;
                const profit = g.pg_profit ?? g.profit ?? 0;
                container.innerHTML += `
                <div class="p-4 bg-slate-900/40 rounded-xl border border-slate-800 space-y-3 grade-block">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-bold text-indigo-400 uppercase text-[10px]">Grade ${grade}</h4>
                        <button type="button" onclick="this.closest('.grade-block').remove()" class="text-red-400 hover:text-red-500 text-[10px] font-bold uppercase"><i class="fas fa-times"></i> Hapus</button>
                    </div>
                    <input type="hidden" name="grades[]" value="${grade}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-500 font-semibold mb-1">HPP / Kg (Rp)</label>
                            <input type="number" name="hpp[]" min="0" step="0.01" value="${hpp}" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                        </div>
                        <div>
                            <label class="block text-slate-500 font-semibold mb-1">Keuntungan / Kg (Rp)</label>
                            <input type="number" name="profit[]" min="0" step="0.01" value="${profit}" class="w-full bg-[#0f111a] border border-slate-800 rounded-lg p-2.5 text-white focus:outline-none focus:border-[#5b50e6]">
                        </div>
                    </div>
                </div>`;
            });
            toggleModal('modal-edit-barang');
        }

        function openEditMasuk(id, mbId, grade, qty, date) {
            document.getElementById('form-edit-masuk').action = '/admin/barang-masuk/update/' + id;
            document.getElementById('edit-masuk-product').value = mbId;
            loadGrades(mbId, 'edit-masuk-grade', 'edit-masuk-stock-hint', grade);
            document.getElementById('edit-bm-qty').value = qty;
            document.getElementById('edit-bm-date').value = date;
            toggleModal('modal-edit-masuk');
        }

        function openEditKeluar(id, mbId, grade, qty, date) {
            document.getElementById('form-edit-keluar').action = '/admin/barang-keluar/update/' + id;
            document.getElementById('edit-keluar-product').value = mbId;
            loadGrades(mbId, 'edit-keluar-grade', 'edit-keluar-stock-hint', grade);
            document.getElementById('edit-bk-qty').value = qty;
            document.getElementById('edit-bk-date').value = date;
            toggleModal('modal-edit-keluar');
        }

        window.addEventListener('DOMContentLoaded', () => {
            const savedTab = localStorage.getItem('active_admin_tab');
            if (savedTab && document.getElementById('tab-' + savedTab)) switchTab(savedTab);
        });
    </script>
</body>
</html>
