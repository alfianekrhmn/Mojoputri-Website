<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CV Mojoputri Sales Analysis</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f111a;
            color: #ffffff;
            overflow-x: hidden;
        }
        .glow-orb {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(91, 80, 230, 0.15) 0%, rgba(0,0,0,0) 70%);
            filter: blur(40px);
            z-index: 0;
        }
        .glass-card {
            background: rgba(26, 29, 46, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }
        .purple-btn {
            background-color: #5b50e6;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .purple-btn:hover {
            background-color: #4c42c2;
            box-shadow: 0 0 20px rgba(91, 80, 230, 0.4);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="relative flex items-center justify-center min-h-screen px-4">

    <!-- Ambient Light Effects -->
    <div class="glow-orb -top-20 -left-20 hidden sm:block"></div>
    <div class="glow-orb -bottom-20 -right-20 hidden sm:block"></div>

    <div class="w-full max-w-md p-4 sm:p-6 relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 bg-gradient-to-tr from-[#5b50e6] to-[#8b5cf6] shadow-lg shadow-indigo-500/20">
                <i class="fas fa-chart-pie text-white text-3xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-indigo-300 bg-clip-text text-transparent">CV MOJOPUTRI</h1>
            <p class="text-slate-400 text-sm mt-2">Dashboard Analisis Penjualan & Peramalan Tren</p>
        </div>

        <!-- Glass Login Card -->
        <div class="glass-card rounded-2xl p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-[3px] bg-gradient-to-r from-transparent via-[#5b50e6] to-transparent"></div>

            <form action="{{ url('/login') }}" method="POST" class="space-y-6">
                @csrf

                @if(session('error'))
                    <div class="bg-red-500/10 border border-red-500/30 text-red-400 p-3.5 rounded-xl text-sm flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 p-3.5 rounded-xl text-sm flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-400"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2 block">Username / Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-500">
                            <i class="far fa-user"></i>
                        </span>
                        <input type="text" name="email" required
                            class="w-full bg-[#0f111a]/80 border border-slate-800 rounded-xl py-3 pl-11 pr-4 text-white placeholder-slate-600 focus:outline-none focus:border-[#5b50e6] focus:ring-2 focus:ring-[#5b50e6]/20 transition-all text-sm"
                            placeholder="Masukkan username">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-400 block">Password</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                            class="w-full bg-[#0f111a]/80 border border-slate-800 rounded-xl py-3 pl-11 pr-4 text-white placeholder-slate-600 focus:outline-none focus:border-[#5b50e6] focus:ring-2 focus:ring-[#5b50e6]/20 transition-all text-sm"
                            placeholder="Masukkan password">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" class="w-4.5 h-4.5 rounded border-slate-800 bg-[#0f111a] text-[#5b50e6] focus:ring-0 focus:ring-offset-0">
                        <span class="text-xs text-slate-400">Ingat sesi saya</span>
                    </label>
                    <span class="text-xs text-slate-500"><i class="fas fa-shield-alt mr-1"></i> Sesi Langsung</span>
                </div>

                <button type="submit"
                    class="w-full purple-btn text-white font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-600/10 text-sm tracking-wide">
                    MASUK KE PORTAL <i class="fas fa-arrow-right ml-2 text-xs"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-600 mt-8">
            &copy; 2026 CV Mojoputri Inventory and Sales Analytics.
        </p>
    </div>

</body>
</html>
