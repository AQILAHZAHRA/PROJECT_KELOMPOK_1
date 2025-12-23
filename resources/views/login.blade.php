<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Bank Sampah Unit</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(rgba(0, 0, 0, 0.25), rgba(0, 0, 0, 0.25)),
                        url('https://raw.githubusercontent.com/SittiHadijah-13020230014-B1/Desktop-6/main/Desktop.jpg') center/cover no-repeat;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .form-container {
            width: 100%;
            max-width: 480px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            padding: 40px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            overflow: hidden;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-text {
            font-weight: 800;
            font-size: 14px;
            color: #1f1f1f;
            line-height: 1.2;
        }

        .welcome {
            margin-bottom: 16px;
        }

        .title {
            font-size: 24px;
            font-weight: 800;
            color: #1f1f1f;
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: #555;
            margin-bottom: 16px;
        }

        .welcome-text {
            background: #e8f5e9;
            color: #0f6b2f;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            border: 1px solid #c2e6c8;
            text-align: center;
        }

        .tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 32px;
            background: #f8f9fa;
            border-radius: 12px;
            padding: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .tab {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .tab.active {
            background: linear-gradient(135deg, #0f6b2f, #1a8f3e);
            color: white;
            box-shadow: 0 4px 15px rgba(15, 107, 47, 0.3);
        }

        .tab:hover:not(.active) {
            background: rgba(15, 107, 47, 0.1);
            color: #0f6b2f;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 13px;
            color: #374151;
            font-weight: 600;
            margin-left: 4px;
            margin-bottom: 6px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 18px;
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            background: linear-gradient(145deg, #ffffff, #f9fafb);
            font-family: 'Instrument Sans', sans-serif;
            font-size: 14px;
            color: #333;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        input:focus {
            outline: none;
            border-color: #10b981;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }

        input:hover:not(:focus) {
            border-color: #d1d5db;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .button {
            width: 100%;
            padding: 16px 24px;
            border: none;
            border-radius: 12px;
            font-family: 'Instrument Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3);
        }

        .button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .button:hover::before {
            left: 100%;
        }

        .button:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .button:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }

        .helper {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }

        .helper a {
            color: #d62828;
            text-decoration: none;
            font-weight: 700;
        }

        .helper a:hover {
            text-decoration: underline;
        }

        .back-link {
            text-align: center;
            margin-top: 24px;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 13px;
        }

        .back-link a:hover {
            color: #0f6b2f;
        }

        .errors {
            background: linear-gradient(145deg, #fef2f2, #fee2e2);
            color: #dc2626;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            border: 2px solid #fecaca;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
        }

        .errors ul {
            margin-left: 20px;
        }

        .status {
            background: linear-gradient(145deg, #ecfdf5, #d1fae5);
            color: #065f46;
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            border: 2px solid #a7f3d0;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.1);
        }

        @media (max-width: 480px) {
            .form-container {
                margin: 10px;
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <div class="logo">
                <div class="logo-icon">
                    <img src="https://raw.githubusercontent.com/SittiHadijah-13020230014-B1/Desktop-6/main/Logo.jpg" alt="Bank Sampah Unit Mekar Swadaya Logo">
                </div>
                <div class="logo-text">BANK SAMPAH UNIT MEKAR SWADAYA</div>
            </div>

            <div class="welcome">
                <h1 class="title">Masuk ke Akun</h1>
                <p class="subtitle">Selamat datang kembali di Bank Sampah Unit</p>
                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif
            </div>

            <div class="welcome-text">
                Silakan pilih jenis akun dan masukkan kredensial Anda
            </div>
        </div>

        @php
            $activeRole = $role ?? 'nasabah';
        @endphp
        
        <div class="tabs">
            <a href="{{ route('login') }}" class="tab {{ $activeRole === 'nasabah' ? 'active' : '' }}">Nasabah</a>
            <a href="{{ route('login.pengelola') }}" class="tab {{ $activeRole === 'pengelola' ? 'active' : '' }}">Pengelola</a>
        </div>

        @if ($errors->any())
            <div class="errors">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="role" value="{{ $activeRole }}">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="button">Masuk</button>
        </form>

        @if($activeRole === 'nasabah')
        <div class="helper">
            <span>Belum punya akun?</span>
            <a href="/register">Daftar di sini</a>
        </div>
        @endif

        <div class="back-link">
            <a href="/">← Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
