<!-- resources/views/beranda.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - BMKG Stasiun Meteorologi Maritim Pontianak</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom, #87CEEB, #2E86C1);
            overflow: auto;
        }

        .background-dots {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            animation: moveDots 20s linear infinite;
        }

        @keyframes moveDots {
            from { background-position: 0 0; }
            to { background-position: 200px 200px; }
        }

        .hero-section {
            position: relative;
            z-index: 10;
            text-align: center;
            color: white;
            padding: 4rem 2rem;
        }

        .content-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 800px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            color: white;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 1rem;
            }

            .hero-section h1 {
                font-size: 2rem;
            }

            .hero-section h2 {
                font-size: 1.5rem;
            }

            .content-card {
                margin: 1rem auto;
                padding: 1rem;
                max-width: 100%;
            }

            .feature-grid {
                gap: 1rem;
            }

            .feature-card {
                padding: 1rem;
            }

            .feature-icon {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="background-dots"></div>
    <div class="hero-section">
        <h1 class="text-5xl font-bold mb-4">Selamat Datang di</h1>
        <h2 class="text-3xl font-semibold mb-8">BMKG Stasiun Meteorologi Maritim Pontianak</h2>
        <p class="text-xl max-w-2xl mx-auto">
            Menyediakan informasi cuaca, iklim, dan geofisika terkini untuk mendukung keselamatan pelayaran dan kegiatan masyarakat di wilayah Pontianak dan sekitarnya.
        </p>
    </div>

    <div class="content-card">
        <h3 class="text-2xl font-bold text-center mb-6">Layanan Utama Kami</h3>
        <div class="feature-grid">
            <div class="feature-card">
                <i class="fas fa-cloud-sun feature-icon"></i>
                <h4 class="text-xl font-semibold mb-2">Prakiraan Cuaca</h4>
                <p>Informasi cuaca harian dan peringatan dini untuk membantu perencanaan kegiatan Anda.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-water feature-icon"></i>
                <h4 class="text-xl font-semibold mb-2">Informasi Maritim</h4>
                <p>Data gelombang laut, pasang surut, dan kondisi laut untuk keselamatan pelayaran.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line feature-icon"></i>
                <h4 class="text-xl font-semibold mb-2">Analisis Iklim</h4>
                <p>Studi dan analisis iklim jangka panjang untuk perencanaan pembangunan.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-exclamation-triangle feature-icon"></i>
                <h4 class="text-xl font-semibold mb-2">Peringatan Dini</h4>
                <p>Sistem peringatan dini untuk bencana hidrometeorologi dan geofisika.</p>
            </div>
        </div>
    </div>

    <div class="content-card">
        <h3 class="text-2xl font-bold text-center mb-6">Berita Terkini</h3>
        <p class="text-center">Informasi terkini tentang cuaca, iklim, dan kegiatan BMKG akan ditampilkan di sini.</p>
    </div>
</body>
</html>
