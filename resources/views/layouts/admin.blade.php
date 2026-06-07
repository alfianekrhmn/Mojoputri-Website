<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Mojoputri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fc; font-family: 'Nunito', sans-serif; }
        .sidebar {
            min-height: 100vh;
            background: #4e73df; /* Warna Biru sesuai gambar */
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }
        .sidebar .nav-link { color: rgba(255,255,255,.8); font-weight: 500; border-bottom: 1px solid rgba(255,255,255,.1); padding: 1rem; }
        .sidebar .nav-link:hover { color: #fff; }
        .sidebar .nav-link.active { color: #fff; font-weight: 700; }
        .sidebar .sidebar-brand { color: #fff; text-transform: uppercase; letter-spacing: 0.1rem; font-weight: 800; }
        main { background: #0D0B61; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar py-3">
                <div class="position-sticky">
                    <div class="sidebar-brand px-3 mb-4 d-flex align-items-center">
                        <i class="fas fa-laugh-wink fa-2x me-2"></i>
                        <span>Mojoputri</span>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-fw fa-tachometer-alt me-2"></i> Inventory Operations Overview
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-fw fa-box me-2"></i> Product Catalog Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-fw fa-chart-area me-2"></i> Incoming Goods Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-fw fa-chart-area me-2"></i> Outgoing Goods
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main class="col-md-10 ms-sm-auto px-md-4">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow-sm">
                    <div class="container-fluid justify-content-end">
                        <span class="me-3 text-gray-600 small">Admin Mojoputri</span>
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <i class="fas fa-user-circle fa-lg text-gray-400"></i>
                    </div>
                </nav>

                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
