<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NajaTrip - @yield('title', 'Login')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0F2B4D 0%, #1A4A7A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-card h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #0F2B4D;
            font-weight: 700;
        }

        .auth-card .logo {
            text-align: center;
            font-size: 48px;
            margin-bottom: 10px;
        }

        .auth-card .subtitle {
            text-align: center;
            color: #6B7280;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #374151;
        }

        .form-group label i {
            margin-right: 8px;
            color: #D4AF37;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #D4AF37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .btn-auth {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #0F2B4D, #1A4A7A);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .auth-link {
            text-align: center;
            margin-top: 20px;
        }

        .auth-link a {
            color: #0F2B4D;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .form-check-input {
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .form-check-label {
            color: #6B7280;
            font-size: 14px;
            cursor: pointer;
        }

        .forgot-link {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
        }

        .forgot-link a {
            font-size: 13px;
            color: #6B7280;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="auth-card">
        <div class="logo">
            <i class="fas fa-plane" style="color: #D4AF37;"></i>
        </div>
        <h2>NajaTrip</h2>
        <div class="subtitle">Open Trip Banyuwangi & Bali</div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
