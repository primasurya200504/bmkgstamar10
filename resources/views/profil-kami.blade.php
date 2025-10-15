<!-- resources/views/profil-kami.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Kami - BMKG Stasiun Meteorologi Maritim Pontianak</title>
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
            max-width: 900px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .vision-mission {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .vm-card {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .service-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
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

            .vision-mission {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .services-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .service-item {
                padding: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="background-dots"></div>
    <div class="hero-section">
        <h1 class="text-5xl font-bold mb-4">Profil Kami</h1>
        <h2 class="text-3xl font-semibold">BMKG Stasiun Meteorologi Maritim Pontianak</h2>
    </div>

    <div class="content-card">
        <h3 class="text-2xl font-bold text-center mb-6">Tentang Kami</h3>
        <p class="text-lg leading-relaxed">
            BMKG Stasiun Meteorologi Maritim Pontianak adalah unit pelaksana teknis Badan Meteorologi, Klimatologi, dan
            Geofisika (BMKG) yang berlokasi di Pontianak, Kalimantan Barat. Kami berkomitmen untuk memberikan layanan
            informasi meteorologi, klimatologi, dan geofisika yang akurat dan tepat waktu, khususnya untuk mendukung
            keselamatan pelayaran dan kegiatan masyarakat di wilayah pesisir dan laut.
        </p>
        <p class="text-lg leading-relaxed mt-4">
            Dengan dukungan teknologi modern dan tenaga ahli yang profesional, kami terus berinovasi untuk memberikan
            pelayanan terbaik kepada masyarakat, pemerintah, dan dunia usaha.
        </p>
    </div>

    <div class="content-card">
        <h3 class="text-2xl font-bold text-center mb-6">Visi dan Misi</h3>
        <div class="vision-mission">
            <div class="vm-card">
                <h4 class="text-xl font-semibold mb-4">Visi</h4>
                <p>Menjadi pusat unggulan informasi meteorologi maritim yang handal dan terpercaya untuk mendukung
                    pembangunan nasional dan keselamatan masyarakat.</p>
            </div>
            <div class="vm-card">
                <h4 class="text-xl font-semibold mb-4">Misi</h4>
                <ul class="text-left list-disc list-inside">
                    <li>Menyediakan informasi cuaca dan iklim yang akurat</li>
                    <li>Mengembangkan sistem peringatan dini bencana</li>
                    <li>Meningkatkan kapasitas sumber daya manusia</li>
                    <li>Mendorong inovasi teknologi meteorologi</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-card">
        <h3 class="text-2xl font-bold text-center mb-6">Struktur Organisasi</h3>
        <div class="text-center">
            <p class="text-lg leading-relaxed mb-4">Struktur organisasi BMKG Stasiun Meteorologi Maritim Pontianak.</p>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                <div class="bg-white/10 rounded-lg p-4 text-center">
                    <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-gray-600"></i>
                    </div>
                    <p class="font-semibold">Kepala Stasiun</p>
                    <p class="text-sm"></p>
                </div>
                <div class="bg-white/10 rounded-lg p-4 text-center">
                    <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-gray-600"></i>
                    </div>
                    <p class="font-semibold">Wakil Kepala Stasiun</p>
                    <p class="text-sm"></p>
                </div>
                <div class="bg-white/10 rounded-lg p-4 text-center">
                    <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-gray-600"></i>
                    </div>
                    <p class="font-semibold">Kepala Bidang Observasi</p>
                    <p class="text-sm"></p>
                </div>
                <div class="bg-white/10 rounded-lg p-4 text-center">
                    <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-gray-600"></i>
                    </div>
                    <p class="font-semibold">Kepala Bidang Analisis</p>
                    <p class="text-sm"></p>
                </div>
                <div class="bg-white/10 rounded-lg p-4 text-center">
                    <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                        <i class="fas fa-user text-2xl text-gray-600"></i>
                    </div>
                    <p class="font-semibold">Kepala Bidang Pelayanan</p>
                    <p class="text-sm"></p>
                </div>
            </div>
            <details class="text-center">
                <summary class="cursor-pointer text-blue-300 hover:text-blue-100">Selengkapnya</summary>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-4">
                    <div class="bg-white/10 rounded-lg p-4 text-center">
                        <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-gray-600"></i>
                        </div>
                        <p class="font-semibold">Staf Observasi 1</p>
                        <p class="text-sm"></p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4 text-center">
                        <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-gray-600"></i>
                        </div>
                        <p class="font-semibold">Staf Observasi 2</p>
                        <p class="text-sm"></p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4 text-center">
                        <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-gray-600"></i>
                        </div>
                        <p class="font-semibold">Staf Analisis 1</p>
                        <p class="text-sm"></p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4 text-center">
                        <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-gray-600"></i>
                        </div>
                        <p class="font-semibold">Staf Analisis 2</p>
                        <p class="text-sm"></p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4 text-center">
                        <div class="w-20 h-20 bg-gray-300 rounded-full mx-auto mb-2 flex items-center justify-center">
                            <i class="fas fa-user text-2xl text-gray-600"></i>
                        </div>
                        <p class="font-semibold">Staf Pelayanan</p>
                        <p class="text-sm"></p>
                    </div>
                </div>
            </details>
        </div>
    </div>

    <div class="content-card">
        <h3 class="text-2xl font-bold text-center mb-6">Layanan Kami</h3>
        <div class="services-grid">
            <div class="service-item">
                <i class="fas fa-cloud-sun text-3xl text-white mb-2"></i>
                <h5 class="font-semibold">Prakiraan Cuaca</h5>
                <p>Informasi cuaca harian dan jangka pendek</p>
            </div>
            <div class="service-item">
                <i class="fas fa-water text-3xl text-white mb-2"></i>
                <h5 class="font-semibold">Data Maritim</h5>
                <p>Gelombang laut, pasang surut, dan kondisi laut</p>
            </div>
            <div class="service-item">
                <i class="fas fa-chart-bar text-3xl text-white mb-2"></i>
                <h5 class="font-semibold">Analisis Iklim</h5>
                <p>Studi iklim regional dan nasional</p>
            </div>
            <div class="service-item">
                <i class="fas fa-exclamation-circle text-3xl text-white mb-2"></i>
                <h5 class="font-semibold">Peringatan Dini</h5>
                <p>Sistem peringatan bencana hidrometeorologi</p>
            </div>
            <div class="service-item">
                <i class="fas fa-users text-3xl text-white mb-2"></i>
                <h5 class="font-semibold">Konsultasi</h5>
                <p>Layanan konsultasi untuk berbagai keperluan</p>
            </div>
            <div class="service-item">
                <i class="fas fa-graduation-cap text-3xl text-white mb-2"></i>
                <h5 class="font-semibold">Magang Mahasiswa</h5>
                <p>Kami menerima mahasiswa untuk program magang di bidang meteorologi maritim.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-12 py-8">
        <div class="content-card">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4">BMKG Stasiun Meteorologi Maritim Pontianak</h4>
                    <p class="text-sm">Menyediakan layanan meteorologi yang mencakup informasi cuaca, iklim, dan geofisika untuk mendukung keselamatan
                        pelayaran dan kegiatan masyarakat di wilayah Pontianak.</p>
                </div>
                <div>
                    <h4 class="text-xl font-bold mb-4">Kontak Kami</h4>
                    <p class="text-sm"><i class="fas fa-map-marker-alt mr-2"></i>Stasiun Meteorologi Maritim
                        Pontianak, Komplek Pelabuhan Dwikora Pontianak, Pontianak, Indonesia</p>
                    <p class="text-sm"><i class="fas fa-phone mr-2"></i>0561769906 / 08989111213</p>
                    <p class="text-sm"><i class="fas fa-envelope mr-2"></i>info@bmkg-pontianak.go.id</p>
                    <div class="mt-4">
                        <p class="text-sm font-semibold mb-2">Ikuti Kami:</p>
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
                    <h4 class="text-xl font-bold mb-4">Tautan</h4>
                    <ul class="text-sm space-y-2">
                        <li><a href="/beranda" class="hover:text-blue-300">Beranda</a></li>
                        <li><a href="/profil-kami" class="hover:text-blue-300">Profil Kami</a></li>
                        <li><a href="/faq" class="hover:text-blue-300">FAQ</a></li>
                        <li><a href="/login" class="hover:text-blue-300">Login</a></li>
                        <li><a href="https://www.bmkg.go.id/" target="_blank" class="hover:text-blue-300">BMKG
                                Pusat</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-4 text-center">
                <p class="text-sm">© 2025 BMKG Stasiun Meteorologi Maritim Pontianak. All rights reserved. Intern students of the Informatics Facilities Development (UBSI Kota Pontianak) program.</p>
            </div>
        </div>
    </footer>
</body>

</html>
