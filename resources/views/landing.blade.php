<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bank Sampah Unit - Ubah Sampah Menjadi Manfaat Nyata</title>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
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
        }

        /* Header Styles */
        header {
            background-color: transparent;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
        }

        .logo {
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .nav-links {
            display: flex;
            gap: 12px;
        }

        .nav-links a {
            text-decoration: none;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            color: white;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        }

        .nav-links a:hover {
            opacity: 0.8;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)),
                        url('https://raw.githubusercontent.com/SittiHadijah-13020230014-B1/Desktop-6/main/Desktop.jpg') center/cover no-repeat;
            background-attachment: fixed;
            background-size: cover;
            color: white;
            padding: 140px 40px;
            text-align: center;
            min-height: 650px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .hero-section h1 {
            font-size: 56px;
            font-weight: 700;
            margin-bottom: 40px;
            line-height: 1.2;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 1;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        .hero-buttons a {
            padding: 14px 40px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            border: 2px solid white;
            cursor: pointer;
        }

        .hero-buttons .register-btn {
            background-color: white;
            color: #333;
        }

        .hero-buttons .register-btn:hover {
            background-color: #f0f0f0;
            transform: translateY(-2px);
        }

        .hero-buttons .login-btn {
            background-color: transparent;
            color: white;
            border-color: white;
        }

        .hero-buttons .login-btn:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* About Section */
        .about-section {
            padding: 100px 40px;
            background-color: #fafafa;
        }

        .about-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-title {
            font-size: 14px;
            color: #4CAF50;
            margin-bottom: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        .about-heading {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 50px;
            line-height: 1.3;
            color: #222; 
            text-align: center;
        }

        .about-description {
            max-width: 700px;
            margin: 0 auto 80px;
            color: #666;
            font-size: 16px;
            line-height: 1.8;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: white;
            padding: 40px 32px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: all 0.3s ease;
            border-top: 4px solid #4CAF50;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            overflow: hidden;
        }

        .feature-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .feature-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #222;
        }

        .feature-description {
            font-size: 15px;
            color: #666;
            line-height: 1.7;
        }

        /* Footer */
        footer {
            background-color: #222;
            color: white;
            text-align: center;
            padding: 40px;
            font-size: 14px;
        }

        footer a {
            color: #8BC34A;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 16px;
                padding: 16px 20px;
            }

            .logo {
                font-size: 14px;
            }

            .nav-links {
                gap: 8px;
            }

            .nav-links a {
                padding: 6px 12px;
                font-size: 12px;
            }

            .hero-section {
                padding: 60px 20px;
                min-height: 450px;
            }

            .hero-section h1 {
                font-size: 36px;
                margin-bottom: 30px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .hero-buttons a {
                width: 100%;
            }

            .about-section {
                padding: 60px 20px;
            }

            .about-heading {
                font-size: 32px;
            }

            .about-description {
                font-size: 15px;
                margin-bottom: 60px;
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .feature-card {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="logo">
            <div class="logo-icon">
                <img src="https://raw.githubusercontent.com/SittiHadijah-13020230014-B1/Desktop-6/main/Logo.jpg" alt="Bank Sampah Unit Mekar Swadaya Logo" style="display: block; max-width: 100%; height: auto;">
            </div>
            <span>BANK SAMPAH UNIT MEKAR SWADAYA</span>
        </div>
        <nav class="nav-links">
            <a href="/">Beranda</a>
            <a href="#about">Tentang Kami</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <h1>UBAH SAMPAH MENJADI<br>MANFAAT NYATA!!!</h1>

        <div class="hero-buttons">
            <a href="/register" class="register-btn">Daftar</a>
            <a href="/login" class="login-btn">Masuk</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="about-container">
            <div class="about-title">Tentang Kami</div>
            <h2 class="about-heading">Bank Sampah Unit adalah<br>Wadah Untuk Mengelola Sampah<br>Menjadi Sumber Daya Berharga</h2>
            
            <p class="about-description">
                Bank Sampah Unit adalah platform revolusioner yang mengubah paradigma pengelolaan sampah. 
                Kami berkomitmen untuk memberikan solusi inovatif dalam pengelolaan limbah sambil memberdayakan komunitas 
                lokal untuk berpartisipasi dalam ekonomi sirkular yang berkelanjutan.
            </p>

            <div class="features-grid">
                <!-- Feature 1 -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="https://raw.githubusercontent.com/SittiHadijah-13020230014-B1/Desktop-6/main/Ramah%20%20Lingkunngan.jpg" alt="Ruang Lapangan">
                    </div>
                    <h3 class="feature-title">Ruang Lapangan</h3>
                    <p class="feature-description">
                        Menyediakan Pengumpulan dan Pengelolaan Sampah Dengan Sistem Yang Terstruktur dan Terpercaya
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="https://raw.githubusercontent.com/SittiHadijah-13020230014-B1/Desktop-6/main/Nilai%20Ekonomis.jpg" alt="Nilai Ekonomis">
                    </div>
                    <h3 class="feature-title">Nilai Ekonomis</h3>
                    <p class="feature-description">
                        Memberikan Insentif Menarik Bagi Pengguna Yang Berkontribusi Dalam Pengumpulan Sampah Bernilai
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="https://raw.githubusercontent.com/SittiHadijah-13020230014-B1/Desktop-6/main/Komunittas%20Solid.jpg" alt="Komunitas Hijau">
                    </div>
                    <h3 class="feature-title">Komunitas Hijau</h3>
                    <p class="feature-description">
                        Menghubungkan Individu dan Komunitas Untuk Menciptakan Dampak Lingkungan Positif Jangka Panjang
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Bank Sampah Unit. Mengubah Sampah Menjadi Manfaat. | <a href="#">Privacy Policy</a> | <a href="#">Contact Us</a></p>
    </footer>
</body>
</html>