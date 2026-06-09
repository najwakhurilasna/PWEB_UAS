<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NajaTrip') - Open Trip Banyuwangi & Bali</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0F2B4D;
            --primary-light: #1A4A7A;
            --primary-dark: #0A1E36;
            --accent: #D4AF37;
            --accent-light: #E8C84A;
            --danger: #EF4444;
            --success: #10B981;
            --warning: #F59E0B;
            --info: #3B82F6;
            --gray: #6B7280;
            --gray-light: #F3F4F6;
            --white: #FFFFFF;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #F5F7FA 0%, #E4E8F0 100%);
            min-height: 100vh;
        }

        /* ==================== NAVBAR ==================== */
        .navbar {
            background: var(--primary) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 12px 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .navbar-brand i {
            color: var(--accent);
        }

        .nav-link {
            color: rgba(255,255,255,0.85) !important;
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 8px 16px !important;
            border-radius: 8px;
        }

        .nav-link:hover {
            color: white !important;
            background: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            background-color: var(--accent) !important;
            color: var(--primary) !important;
        }

        /* ==================== DROPDOWN ==================== */
        .dropdown-menu {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
            padding: 8px 0;
            margin-top: 8px;
        }

        .dropdown-item {
            padding: 10px 20px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .dropdown-item:hover {
            background-color: var(--gray-light);
        }

        .dropdown-item.text-danger:hover {
            background-color: #fee2e2;
        }

        /* ==================== HERO GRADIENT ==================== */
        .hero-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        }

        /* ==================== CARDS ==================== */
        .card {
            border: none;
            border-radius: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .card-header {
            font-weight: 600;
            border-bottom: none;
        }

        /* ==================== BUTTONS ==================== */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border: none;
            transition: all 0.3s ease;
            font-weight: 500;
            padding: 10px 24px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            font-weight: 500;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        /* ==================== FOOTER ==================== */
        .footer {
            background: var(--primary-dark);
            color: white;
            padding: 50px 0 20px;
            margin-top: 60px;
        }

        .footer h5 {
            color: var(--accent);
            margin-bottom: 20px;
            font-weight: 600;
        }

        .footer a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .footer a:hover {
            color: var(--accent);
            text-decoration: underline;
        }

        .whatsapp-button {
            background-color: #25D366;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .whatsapp-button:hover {
            background-color: #128C7E;
            color: white;
            transform: translateY(-2px);
        }

        /* ==================== STATUS BADGES ==================== */
        .badge-pending { background-color: var(--warning); color: #fff; }
        .badge-dikonfirmasi { background-color: var(--info); color: #fff; }
        .badge-selesai { background-color: var(--success); color: #fff; }
        .badge-batal { background-color: var(--danger); color: #fff; }

        /* ==================== ALERTS ==================== */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
        }

        /* ==================== FORM ==================== */
        .form-control, .form-select {
            border: 2px solid var(--gray-light);
            border-radius: 12px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        /* ==================== TABLE ==================== */
        .table {
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead th {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px;
        }

        .table tbody tr:hover {
            background: var(--gray-light);
        }

        /* ==================== PAGINATION ==================== */
        .pagination .page-link {
            color: var(--primary);
            border: none;
            border-radius: 8px;
            margin: 0 3px;
            padding: 8px 14px;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            color: white;
        }

        /* ==================== LOADING SPINNER ==================== */
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--gray-light);
            border-top: 4px solid var(--accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }

            .nav-link {
                padding: 8px 12px !important;
            }

            .footer {
                text-align: center;
            }

            .footer .text-md-end {
                text-align: center !important;
            }

            .whatsapp-button {
                justify-content: center;
            }

            .card:hover {
                transform: none;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-plane"></i> NajaTrip
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                {{-- Menu untuk semua user --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('detail') ? 'active' : '' }}" href="{{ route('detail') }}">
                        <i class="fas fa-info-circle"></i> Detail
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('transaksi') ? 'active' : '' }}" href="{{ route('transaksi') }}">
                        <i class="fas fa-shopping-cart"></i> Transaksi
                    </a>
                </li>

                {{-- Riwayat untuk SEMUA USER (admin dan customer) --}}
                @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('riwayat') ? 'active' : '' }}" href="{{ route('riwayat') }}">
                            <i class="fas fa-history"></i> Riwayat
                        </a>
                    </li>
                @endauth

                {{-- Daftar Pesanan hanya untuk ADMIN --}}
                @auth
                    @if(Auth::user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.daftar') ? 'active' : '' }}" href="{{ route('admin.daftar') }}">
                                <i class="fas fa-clipboard-list"></i> Daftar Pesanan
                            </a>
                        </li>
                    @endif
                @endauth

                {{-- Dropdown User / Login Register --}}
                @auth
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                            @if(Auth::user()->isAdmin())
                                <span class="badge bg-danger ms-1">Admin</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="py-4">
    <div class="container">
        {{-- Flash Messages (DIKOMENTAR - TIDAK MUNCUL) --}}
        {{--
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        --}}

        @yield('content')
    </div>
</main>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-4">
                <h5><i class="fas fa-plane"></i> NajaTrip</h5>
                <p>Open Trip Banyuwangi & Bali. Solusi mudah untuk menjelajahi pesona dua dunia.</p>
                <a href="https://wa.me/6282340188130" class="whatsapp-button">
                    <i class="fab fa-whatsapp"></i> Hubungi Admin
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Menu</h5>
                <ul class="list-unstyled">
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('detail') }}">Detail Trip</a></li>
                    <li><a href="{{ route('transaksi') }}">Transaksi</a></li>
                    <li><a href="{{ route('riwayat') }}">Riwayat</a></li>
                    @auth
                        @if(Auth::user()->isAdmin())
                            <li><a href="{{ route('admin.daftar') }}">Daftar Pesanan</a></li>
                        @endif
                    @endauth
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5>Kontak</h5>
                <p><i class="fab fa-whatsapp"></i> 0823-4018-8130</p>
                <p><i class="fab fa-instagram"></i> @najatrip</p>
                <p><i class="fas fa-envelope"></i> info@najatrip.com</p>
                <p><i class="fas fa-clock"></i> 08:00 - 20:00 WIB</p>
            </div>
        </div>
        <div class="text-center pt-4 mt-3 border-top border-secondary">
            <p>&copy; {{ date('Y') }} NajaTrip. All Rights Reserved. | Powered by NajaTrip Team</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Bootstrap dropdown handler
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(function(menu) {
                    menu.classList.remove('show');
                });
            }
        });
    });

    // Auto-hide alerts - TIDAK DIPERLUKAN LAGI KARENA ALERT DIHAPUS
    // setTimeout(function() {
    //     document.querySelectorAll('.alert').forEach(function(alert) {
    //         var bsAlert = new bootstrap.Alert(alert);
    //         bsAlert.close();
    //     });
    // }, 5000);
</script>
@stack('scripts')
</body>
</html>
