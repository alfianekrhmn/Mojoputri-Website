<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sales Analytics BI</title>
    <!-- Gunakan Tailwind CSS via CDN untuk mempermudah styling sesuai gambar -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #0f172a; color: #f8fafc; }
        .glass-card { background: rgba(30, 41, 59, 0.7); border: 1px solid #334155; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md p-8">
        <!-- Logo & Header sesuai 10-Sales Analytics - Login & Role.png -->
        <div class="text-center mb-10">
            <div class="bg-blue-600 w-12 h-12 rounded-lg flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chart-line text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold">Sales Analytics BI</h1>
            <p class="text-gray-400 text-sm mt-2">Sign in to manage inventory and view insights.</p>
        </div>

        <div class="glass-card rounded-2xl p-8 shadow-2xl">
        <form action="{{ url('/login') }}" method="POST">
    @csrf

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500 text-red-500 p-3 rounded-lg mb-6 text-sm text-center">
            {{ session('error') }}
        </div>
    @endif

    <!-- Langsung ke Input Email -->
    <div class="mb-5">
        <label class="text-sm font-medium mb-2 block text-gray-300">Email address</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                <i class="far fa-envelope"></i>
            </span>
            <input type="email" name="email" required
                class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2.5 pl-10 pr-4 focus:outline-none focus:border-blue-500 transition-all"
                placeholder="you@company.com">
        </div>
    </div>

    <!-- Password Input -->
    <div class="mb-6">
        <div class="flex justify-between mb-2">
            <label class="text-sm font-medium text-gray-300">Password</label>
            <a href="#" class="text-sm text-blue-500 hover:underline">Forgot password?</a>
        </div>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" name="password" required
                class="w-full bg-slate-900 border border-slate-700 rounded-lg py-2.5 pl-10 pr-10 focus:outline-none focus:border-blue-500 transition-all"
                placeholder="••••••••">
        </div>
    </div>

    <button type="submit"
        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg shadow-lg shadow-indigo-500/30 transition-all">
        Sign in
    </button>
</form>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">Don't have an account? <span class="text-white font-medium">Contact Administrator</span></p>
            </div>
        </div>

        <p class="text-center text-xs text-gray-500 mt-8">
            <i class="fas fa-lock mr-1"></i> Secure, end-to-end encrypted session
        </p>
    </div>

</body>
</html>
