<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMKG - Badan Meteorologi, Klimatologi, dan Geofisika</title>
    <link rel="icon" type="image/x-icon" href="/img/logo.png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Bebas+Neue&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom, #87CEEB, #2E86C1);
            /* Gradasi biru muda ke biru tua */
            overflow: hidden;
            /* Mencegah scrolling saat animasi */
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
            from {
                background-position: 0 0;
            }

            to {
                background-position: 200px 200px;
            }
        }

        /* Animasi latar belakang baru */
        .animated-bg-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        /* Animasi Petir */
        #lightning {
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #ffffee;
            /* Warna petir */
            opacity: 0;
            animation: lightningFlash 8s linear infinite;
            /* Frekuensi kilat */
            z-index: 30;
            /* Di atas awan, di bawah logo */
        }

        @keyframes lightningFlash {

            0%,
            99.0% {
                opacity: 0;
            }

            99.1%,
            99.3% {
                opacity: 0.8;
            }

            99.4%,
            99.6% {
                opacity: 0;
            }

            99.7%,
            99.9% {
                opacity: 0.8;
            }

            100% {
                opacity: 0;
            }
        }

        /* Animasi Teks Mengambang */
        .follow-plane {
            position: absolute;
            top: 15%;
            left: -250px;
            animation: floatText 35s linear infinite;
            opacity: 0;
            animation-fill-mode: forwards;
        }

        @keyframes floatText {
            0% {
                transform: translateX(0) translateY(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            20% {
                transform: translateX(20vw) translateY(-20px);
            }
            40% {
                transform: translateX(40vw) translateY(20px);
            }
            60% {
                transform: translateX(60vw) translateY(-20px);
            }
            80% {
                transform: translateX(80vw) translateY(20px);
            }
            100% {
                transform: translateX(calc(100vw + 250px)) translateY(0);
                opacity: 0;
            }
        }

        .follow-plane h1 {
            transform: scaleX(0.8) scaleY(0.8) rotate(5deg);
            color: white;
            font-family: 'Bebas Neue', cursive;
            font-size: 2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.7);
        }

        @keyframes movePlane {
            0% {
                transform: translateX(0);
                opacity: 0.8;
            }

            100% {
                transform: translateX(calc(100vw + 250px));
                opacity: 0.8;
            }
        }

        /* Animasi awan hujan */
        .cloud {
            position: absolute;
            top: 10%;
            left: 0;
            width: 250px;
            height: 80px;
            background: #fff;
            border-radius: 80px;
            animation: moveClouds 40s linear infinite;
            z-index: 1;
            opacity: 0.7;
        }

        .cloud::before,
        .cloud::after {
            content: '';
            position: absolute;
            background: #fff;
            border-radius: 50%;
        }

        .cloud::before {
            width: 100px;
            height: 100px;
            top: -50px;
            left: 40px;
        }

        .cloud::after {
            width: 120px;
            height: 120px;
            top: -60px;
            right: 40px;
        }

        /* Hujan lebih lebat dan aktif */
        .cloud-rain {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 80px;
            left: 0;
            background: radial-gradient(circle, rgba(173, 216, 230, 0.7) 1px, transparent 1px);
            background-size: 3px 15px;
            /* Ukuran rintik lebih kecil dan padat */
            animation: rain 0.3s linear infinite;
            /* Kecepatan hujan lebih aktif */
        }

        @keyframes moveClouds {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(calc(100vw + 250px));
            }
        }

        @keyframes rain {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 0 15px;
            }
        }

        /* Posisi untuk awan-awan */
        #cloud-1 {
            top: 10%;
            animation-duration: 35s;
        }

        #cloud-2 {
            top: 20%;
            animation-duration: 45s;
            left: -20%;
            transform: scale(0.8);
        }

        #cloud-3 {
            top: 45%;
            animation-duration: 40s;
            left: -10%;
        }

        #cloud-4 {
            top: 60%;
            animation-duration: 50s;
            transform: scale(1.1);
        }

        /* Animasi ombak laut */
        .waves-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            overflow: hidden;
            z-index: 10;
        }

        .wave {
            position: absolute;
            bottom: 0;
            width: 200%;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 40%;
            animation: moveWave 15s linear infinite;
        }

        .wave.wave-2 {
            bottom: -10px;
            background: rgba(255, 255, 255, 0.3);
            animation: moveWave 18s linear infinite;
        }

        .wave.wave-3 {
            bottom: -20px;
            background: rgba(255, 255, 255, 0.1);
            animation: moveWave 22s linear infinite;
        }

        @keyframes moveWave {
            0% {
                transform: translateX(0) scaleY(0.8) rotate(2deg);
            }

            50% {
                transform: translateX(-50%) scaleY(1) rotate(-2deg);
            }

            100% {
                transform: translateX(-100%) scaleY(0.8) rotate(2deg);
            }
        }

        /* Animasi kapal di atas ombak */
        #ship-img {
            position: absolute;
            left: -250px;
            bottom: 25px;
            /* Atur posisi agar di atas ombak */
            width: 200px;
            animation: moveShip 25s linear infinite;
            /* Kecepatan kapal dipercepat */
            transform: scaleX(1.2);
            z-index: 11;
        }

        @keyframes moveShip {
            0% {
                transform: translateX(0) translateY(0) rotate(0deg);
                opacity: 0.9;
            }

            25% {
                transform: translateX(25%) translateY(-10px) rotate(2deg);
            }

            50% {
                transform: translateX(50%) translateY(0) rotate(0deg);
            }

            75% {
                transform: translateX(75%) translateY(-10px) rotate(-2deg);
            }

            100% {
                transform: translateX(calc(100vw + 250px)) translateY(0) rotate(0deg);
                opacity: 0.9;
            }
        }

        /* --- Perubahan untuk animasi logo dan bubble --- */
        .logo-container {
            position: relative;
            transform: scale(0);
            transition: transform 1s ease-out;
            animation: pulse 2s infinite;
            cursor: pointer;
            z-index: 50;
        }

        .logo-container.active {
            transform: scale(1);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .feature-bubble {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.5s ease-in-out;
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            pointer-events: none;
            z-index: 40;
        }

        .feature-bubble.visible {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }

        .feature-bubble:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .feature-bubble-icon {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .feature-bubble-text {
            font-size: 0.875rem;
            color: white;
            text-align: center;
        }

        /* Posisi untuk setiap bubble (desktop) - 3 bubble di atas, 3 bubble di bawah, lebih jauh dari logo */
        #beranda {
            top: 15%;
            left: 20%;
        }

        #login-pelayanan {
            top: 15%;
            left: 50%;
            transform: translateX(-50%);
        }

        #profil-kami {
            top: 15%;
            right: 20%;
        }

        #formulir-permohonan {
            top: 80%;
            left: 20%;
        }

        #faq {
            top: 80%;
            left: 50%;
            transform: translateX(-50%);
        }

        #pengaduan-dan-saran {
            top: 80%;
            right: 20%;
        }


        @media (max-width: 768px) {
            .feature-bubble {
                /* Perkecil bubble agar tidak menumpuk */
                width: 80px;
                height: 80px;
            }

            .feature-bubble-icon {
                font-size: 1.5rem;
            }

            .feature-bubble-text {
                font-size: 0.7rem;
            }

            /* Hapus penempatan posisi mutlak (absolute) untuk mobile */
            #beranda,
            #login-pelayanan,
            #profil-kami,
            #formulir-permohonan,
            #faq,
            #pengaduan-dan-saran {
                position: relative;
                top: unset;
                left: unset;
                right: unset;
                margin: 0.5rem;
            }

            /* Atur ulang tata letak bubble agar muncul di bawah logo */
            #bubbles-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                position: relative;
                width: 100%;
                height: auto;
                margin-top: 1rem;
            }
        }

        @media (max-width: 480px) {
            .feature-bubble {
                width: 70px;
                height: 70px;
            }

            .feature-bubble-icon {
                font-size: 1.2rem;
            }

            .feature-bubble-text {
                font-size: 0.6rem;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .feature-bubble {
                width: 100px;
                height: 100px;
            }

            .feature-bubble-icon {
                font-size: 2rem;
            }

            .feature-bubble-text {
                font-size: 0.8rem;
            }
        }



        @media (min-width: 769px) and (max-width: 1024px) {
            .feature-bubble {
                width: 110px;
                height: 110px;
            }

            .feature-bubble-icon {
                font-size: 2.2rem;
            }

            .feature-bubble-text {
                font-size: 0.8rem;
            }
        }

        @media (min-width: 1025px) {
            .feature-bubble {
                width: 120px;
                height: 120px;
            }

            .feature-bubble-icon {
                font-size: 2.5rem;
            }

            .feature-bubble-text {
                font-size: 0.875rem;
            }
        }

        /* Animation for welcome text */
        .animate-fade-in {
            animation: fadeIn 2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">

    <div class="background-dots"></div>

    <!-- Container untuk animasi latar belakang -->
    <div class="animated-bg-container">
        <!-- Petir -->
        <div id="lightning" class="animated-item"></div>

        <!-- Awan Hujan -->
        <div id="cloud-1" class="cloud">
            <div class="cloud-rain"></div>
        </div>
        <div id="cloud-2" class="cloud">
            <div class="cloud-rain"></div>
        </div>
        <div id="cloud-3" class="cloud">
            <div class="cloud-rain"></div>
        </div>
        <div id="cloud-4" class="cloud">
            <div class="cloud-rain"></div>
        </div>
    </div>

    <!-- Container ombak laut -->
    <div class="waves-container">
        <div class="wave wave-1"></div>
        <div class="wave wave-2"></div>
        <div class="wave wave-3"></div>
    </div>

    <!-- Kapal sebagai gambar -->
    <img id="ship-img" src="img/kapal.gif" alt="Kapal">

    <!-- Container utama -->
    <div class="relative w-full h-screen flex flex-col items-center justify-center">
        <!-- Logo BMKG, tambahkan z-index agar selalu di atas dan bisa diklik -->
        <div class="logo-container z-50" id="logo-main">
            <!-- Menggunakan logo BMKG yang berbeda dari sumber lain -->
            <img src="img/logo.png" alt="Logo BMKG" class="sm:w-30 sm:h-40">
        </div>

        <!-- Welcome Text Following Plane -->
        <div class="follow-plane" style="display: none;">
            <h1>Selamat Datang di Stasiun Meteorologi Maritim Pontianak</h1>
        </div>

        <!-- Container untuk bubble fitur -->
        <div id="bubbles-container" class="absolute w-full h-full top-0 left-0">
            <!-- Bubble fitur -->
            <a href="/beranda" id="beranda" class="feature-bubble absolute">
                <i class="fas fa-home feature-bubble-icon"></i>
                <span class="feature-bubble-text">Beranda</span>
            </a>
            <a href="/login" id="login-pelayanan" class="feature-bubble absolute">
                <i class="fas fa-user-lock feature-bubble-icon"></i> <!-- Gabungan ikon -->
                <span class="feature-bubble-text">Login/Pelayanan</span>
            </a>
            <a href="/profil-kami" id="profil-kami" class="feature-bubble absolute">
                <i class="fas fa-user feature-bubble-icon"></i>
                <span class="feature-bubble-text">Profil Kami</span>
            </a>
            <a href="/formulir-permohonan" id="formulir-permohonan" class="feature-bubble absolute">
                <i class="fas fa-file-alt feature-bubble-icon"></i>
                <span class="feature-bubble-text">Formulir Permohonan</span>
            </a>
            <a href="/faq" id="faq" class="feature-bubble absolute">
                <i class="fas fa-question-circle feature-bubble-icon"></i>
                <span class="feature-bubble-text">FAQ</span>
            </a>
            <a href="https://docs.google.com/forms/d/e/1FAIpQLScn6JdMB9vaASfy1ImfaNVld2AAjh4Sf8oVZh0yOPIT20c92Q/viewform?uid=108069731218267343512&pli=1"
                id="pengaduan-dan-saran" class="feature-bubble absolute" target="_blank">
                <i class="fas fa-comment-dots feature-bubble-icon"></i>
                <span class="feature-bubble-text">Pengaduan Dan Saran</span>
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const logo = document.getElementById('logo-main');
            const followPlane = document.querySelector('.follow-plane');
            const bubbles = document.querySelectorAll('.feature-bubble');
            let bubblesVisible = false;

            // Animasikan logo untuk muncul saat halaman dimuat
            setTimeout(() => {
                logo.classList.add('active');
                // Tampilkan teks yang mengikuti pesawat setelah logo
                setTimeout(() => {
                    followPlane.style.display = 'block';
                }, 1000);
            }, 500);

            // Tambahkan event listener ke logo untuk menampilkan/menyembunyikan bubble
            logo.addEventListener('click', () => {
                bubblesVisible = !bubblesVisible;

                if (bubblesVisible) {
                    // Tampilkan bubble dengan animasi berurutan
                    bubbles.forEach((bubble, index) => {
                        setTimeout(() => {
                            bubble.classList.add('visible');
                        }, index * 100);
                    });
                } else {
                    // Sembunyikan bubble dengan animasi berurutan
                    const reversedBubbles = Array.from(bubbles).reverse();
                    reversedBubbles.forEach((bubble, index) => {
                        setTimeout(() => {
                            bubble.classList.remove('visible');
                        }, index * 100);
                    });
                }
            });
        });
    </script>
</body>

</html>
