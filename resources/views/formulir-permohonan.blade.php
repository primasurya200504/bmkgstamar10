<!-- resources/views/formulir-permohonan.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Permohonan - BMKG Stasiun Meteorologi Maritim Pontianak</title>
    <link rel="icon" type="image/x-icon" href="/img/logo.png">
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
            from {
                background-position: 0 0;
            }

            to {
                background-position: 200px 200px;
            }
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

        .form-button {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: transform 0.3s ease, background 0.3s ease;
            cursor: pointer;
        }

        .form-button:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.25);
        }

        .form-icon {
            font-size: 3rem;
            color: white;
            margin-bottom: 1rem;
        }

        .form-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.5rem;
        }

        .form-description {
            font-size: 0.875rem;
            color: white;
            opacity: 0.9;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .hero-section {
            position: relative;
            z-index: 10;
            text-align: center;
            color: white;
            padding: 4rem 2rem;
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
        <h1 class="text-5xl font-bold mb-4">Formulir Permohonan</h1>
        <h2 class="text-3xl font-semibold mb-8">BMKG Stasiun Meteorologi Maritim Pontianak</h2>
        <p class="text-xl max-w-2xl mx-auto">
            Akses formulir permohonan data meteorologi dan iklim untuk keperluan penelitian, konsultasi teknis, dan
            sertifikat maritim.
        </p>
    </div>

    <div class="content-card">
        <h3 class="text-2xl font-bold text-center mb-6">Highlight Terkini</h3>
        <div class="relative overflow-hidden rounded-lg mb-6">
            <div class="slider flex transition-transform duration-500 ease-in-out" id="slider">
                <div class="slide flex-shrink-0 w-full">
                    <img src="img/fpermohonan/Fpermohonan1.png" alt="Berita 1" class="w-full h-full object-cover">
                </div>
                <div class="slide flex-shrink-0 w-full">
                    <img src="img/fpermohonan/Fpermohonan2.png" alt="Berita 2" class="w-full h-full object-cover">
                </div>
                <div class="slide flex-shrink-0 w-full">
                    <img src="img/beranda/ptsp3.png" alt="Berita 3" class="w-full h-full object-cover">
                </div>
            </div>
            <div class="flex justify-center space-x-2 mt-4">
                <button class="dot w-3 h-3 bg-white rounded-full opacity-50" onclick="goToSlide(0)"></button>
                <button class="dot w-3 h-3 bg-white rounded-full opacity-50" onclick="goToSlide(1)"></button>
                <button class="dot w-3 h-3 bg-white rounded-full opacity-50" onclick="goToSlide(2)"></button>
            </div>
        </div>
    </div>

    <div class="content-card">
        <h1 class="text-3xl font-bold text-center text-white mb-8">Pilih Formulir Permohonan</h1>
        <p class="text-center text-white mb-8">Pilih formulir permohonan yang diperlukan. Klik tombol untuk mengunduh
            file.</p>

        <div class="form-grid">
            <div class="form-button" onclick="downloadForm('formulir-1')">
                <i class="fas fa-file-pdf form-icon"></i>
                <div class="form-title">Formulir Permohonan Data RP.0</div>
                <div class="form-description">Formulir untuk mengajukan permintaan data meteorologi maritim ke BMKG
                    secara gratis (tarif nol rupiah),<p>biasanya digunakan untuk penelitian atau tugas akhir siswa</p>
                </div>
            </div>

            <div class="form-button" onclick="downloadForm('formulir-2')">
                <i class="fas fa-file-pdf form-icon"></i>
                <div class="form-title">Surat Pengantar Tertulis Untuk Penelitian Perorangan</div>
                <div class="form-description">Surat pengantar dari kampus atau instansi untuk permohonan data cuaca dan
                    iklim,,<p>biasanya digunakan untuk penelitian atau tugas akhir siswa</p>
                </div>
            </div>

            <div class="form-button" onclick="downloadForm('formulir-3')">
                <i class="fas fa-file-pdf form-icon"></i>
                <div class="form-title">Surat Pernyataan Tidak Digunakan Untuk Kepentingan Lain Dan Kesediaan
                    Menyerahkan Salinan Hasil Penelitian</div>
                <div class="form-description">U Surat menyatakan bahwa data yang diminta hanya untuk penelitian dan siap
                    menyerahkan hasil penelitian ke BMKG setelah selesai.</div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-12 py-8">
        <div class="content-card">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4 text-white">BMKG Stasiun Meteorologi Maritim Pontianak</h4>
                    <p class="text-sm text-white">Menyediakan layanan meteorologi yang mencakup informasi cuaca, iklim,
                        dan geofisika untuk mendukung
                        keselamatan pelayaran dan kegiatan masyarakat di wilayah Pontianak.</p>
                </div>
                <div>
                    <h4 class="text-xl font-bold mb-4 text-white">Kontak Kami</h4>
                    <p class="text-sm text-white"><i class="fas fa-map-marker-alt mr-2"></i>Stasiun Meteorologi Maritim
                        Pontianak, Komplek Pelabuhan Dwikora Pontianak, Pontianak, Indonesia</p>
                    <p class="text-sm text-white"><i class="fas fa-phone mr-2"></i>0561769906 / 08989111213</p>
                    <p class="text-sm text-white"><i class="fas fa-envelope mr-2"></i>info@bmkg-pontianak.go.id</p>
                    <div class="mt-4">
                        <p class="text-sm font-semibold mb-2 text-white">Ikuti Kami:</p>
                        <div class="flex space-x-4">
                            <a href="https://www.instagram.com/infobmkg.maritimkalbar/" target="_blank"
                                class="text-white hover:text-blue-300"><i class="fab fa-instagram text-lg"></i></a>
                            <a href="https://www.facebook.com/infobmkg.maritimkalbar" target="_blank"
                                class="text-white hover:text-blue-300"><i class="fab fa-facebook text-lg"></i></a>
                            <a href="https://twitter.com/bmkgmaritimpnk" target="_blank"
                                class="text-white hover:text-blue-300"><i class="fab fa-twitter text-lg"></i></a>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-xl font-bold mb-4 text-white">Tautan</h4>
                    <ul class="text-sm space-y-2 text-white">
                        <li><a href="/" class="hover:text-blue-300">Beranda</a></li>
                        <li><a href="/profil-kami" class="hover:text-blue-300">Profil Kami</a></li>
                        <li><a href="/faq" class="hover:text-blue-300">FAQ</a></li>
                        <li><a href="/login" class="hover:text-blue-300">Login</a></li>
                        <li><a href="https://www.bmkg.go.id/" target="_blank" class="hover:text-blue-300">BMKG
                                Pusat</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-4 text-center">
                <p class="text-sm text-white">© 2025 BMKG Stasiun Meteorologi Maritim Pontianak. All rights
                    reserved. Intern students of the Informatics Facilities Development (UBSI Kota Pontianak) program.
                </p>
            </div>
        </div>
    </footer>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');

        function showSlide(index) {
            const slider = document.getElementById('slider');
            slider.style.transform = `translateX(-${index * 100}%)`;
            dots.forEach((dot, i) => {
                dot.classList.toggle('opacity-100', i === index);
                dot.classList.toggle('opacity-50', i !== index);
            });
        }

        function goToSlide(index) {
            currentSlide = index;
            showSlide(currentSlide);
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        setInterval(nextSlide, 4000); // Auto slide every 4 seconds

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            showSlide(currentSlide);
        });

        function downloadForm(formType) {
            // Placeholder untuk download file
            // Ganti dengan URL file yang sebenarnya
            const fileUrls = {
                'formulir-1': '/files/formulir-permohonan-0RP.pdf',
                'formulir-2': '/files/formulir-permohonan-perorangan.pdf',
                'formulir-3': '/files/formulir-permohonan-menyerahkanhasilsalinan.pdf',
            };

            const url = fileUrls[formType];
            if (url) {
                window.open(url, '_blank');
            } else {
                alert('File tidak tersedia.');
            }
        }
    </script>
</body>

</html>
