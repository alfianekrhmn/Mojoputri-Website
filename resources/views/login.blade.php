<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CV Mojoputri Sales Analysis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #EAEAEA; /* Menggunakan EAEAEA sebagai latar utama light mode */
            color: #000000; /* Teks utama hitam */
            overflow-x: hidden;
        }
        .glow-orb {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            /* Menggunakan aksen 66D2CE dan 2DAA9E untuk ambient light lembut */
            background: radial-gradient(circle, rgba(102, 210, 206, 0.25) 0%, rgba(234, 234, 234, 0) 70%);
            filter: blur(40px);
            z-index: 0;
        }
        .glass-card {
            background: rgba(234, 234, 234, 0.8); /* Kombinasi warna krem E3D2C3 semi-transparan */
            backdrop-filter: blur(16px);
            border: 1px solid rgba(45, 170, 158, 0.15); /* Border tipis menggunakan 2DAA9E */
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.08);
        }
        .teal-btn {
            background-color: #2DAA9E; /* Tombol utama menggunakan 2DAA9E */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .teal-btn:hover {
            background-color: #1e877c; /* Tone down sedikit warna teal untuk efek hover */
            box-shadow: 0 0 20px rgba(45, 170, 158, 0.4);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="relative flex items-center justify-center min-h-screen px-4">

    <div class="glow-orb -top-20 -left-20 hidden sm:block"></div>
    <div class="glow-orb -bottom-20 -right-20 hidden sm:block"></div>

    <div class="w-full max-w-md p-4 sm:p-6 relative z-10">
        <div class="text-center mb-8">
            <div class="w-64 h-64 flex items-center justify-center mx-auto mb-0.1">
                <img src="{{ asset('assets/logo-cv-mojoputri.png') }}" alt="Logo CV Mojoputri" class="w-full h-full object-contain drop-shadow-md">
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-black via-slate-800 to-[#2DAA9E] bg-clip-text text-transparent">CV MOJOPUTRI</h1>
        </div>

        <div class="glass-card rounded-2xl p-6 sm:p-8 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-[3px] bg-gradient-to-r from-transparent via-[#66D2CE] to-transparent"></div>

            <form action="{{ url('/login') }}" method="POST" class="space-y-6">
                @csrf

                @if(session('error'))
                    <div class="bg-red-500/10 border border-red-500/30 text-red-700 p-3.5 rounded-xl text-sm flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="bg-[#2DAA9E]/10 border border-[#2DAA9E]/30 text-[#2DAA9E] p-3.5 rounded-xl text-sm flex items-center gap-3">
                        <i class="fas fa-check-circle text-[#2DAA9E]"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 block">Username / Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-600">
                            <i class="far fa-user"></i>
                        </span>
                        <input type="text" name="email" required
                            class="w-full bg-white border border-slate-300 rounded-xl py-3 pl-11 pr-4 text-black placeholder-slate-400 focus:outline-none focus:border-[#2DAA9E] focus:ring-2 focus:ring-[#2DAA9E]/20 transition-all text-sm"
                            placeholder="Masukkan username">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-700 block">Password</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-600">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                            class="w-full bg-white border border-slate-300 rounded-xl py-3 pl-11 pr-4 text-black placeholder-slate-400 focus:outline-none focus:border-[#2DAA9E] focus:ring-2 focus:ring-[#2DAA9E]/20 transition-all text-sm"
                            placeholder="Masukkan password">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2.5 cursor-pointer">
                        <input type="checkbox" class="w-4.5 h-4.5 rounded border-slate-300 bg-white text-[#2DAA9E] focus:ring-0 focus:ring-offset-0">
                        <span class="text-xs font-medium text-slate-600">Ingat sesi saya</span>
                    </label>
                    <span class="text-xs font-medium text-slate-600"><i class="fas fa-shield-alt mr-1 text-[#2DAA9E]"></i> Sesi Langsung</span>
                </div>

                <button type="submit"
                    class="w-full teal-btn text-black font-bold py-3.5 rounded-xl shadow-md shadow-teal-600/10 text-sm tracking-wide">
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
