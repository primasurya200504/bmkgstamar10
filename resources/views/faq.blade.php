<!-- resources/views/faq.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - BMKG Stasiun Meteorologi Maritim Pontianak</title>
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

        .faq-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        .faq-item {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .faq-question {
            padding: 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.25rem;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .faq-question:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .faq-answer {
            padding: 0 1.5rem 1.5rem;
            display: none;
            font-size: 1rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.9);
        }

        .faq-item.active .faq-answer {
            display: block;
        }

        .faq-icon {
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-icon {
            transform: rotate(180deg);
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

            .faq-container {
                padding: 1rem;
            }

            .faq-item {
                margin-bottom: 1rem;
            }

            .faq-question {
                padding: 1rem;
                font-size: 1rem;
            }

            .faq-answer {
                padding: 0 1rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="background-dots"></div>
    <div class="hero-section">
        <h1 class="text-5xl font-bold mb-4">Frequently Asked Questions</h1>
        <h2 class="text-2xl font-semibold">BMKG Stasiun Meteorologi Maritim Pontianak</h2>
    </div>

    <div class="faq-container">
        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Apa itu BMKG Stasiun Meteorologi Maritim Pontianak?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                BMKG Stasiun Meteorologi Maritim Pontianak adalah unit pelaksana teknis Badan Meteorologi, Klimatologi, dan Geofisika yang berfokus pada pengamatan dan pelayanan informasi meteorologi maritim di wilayah Pontianak dan sekitarnya.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Bagaimana cara mendapatkan informasi cuaca terkini?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Anda dapat mengakses informasi cuaca terkini melalui website resmi BMKG (www.bmkg.go.id), aplikasi mobile BMKG, atau menghubungi stasiun meteorologi langsung di nomor telepon (0561) 734017.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Apa saja layanan yang disediakan oleh stasiun ini?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Kami menyediakan prakiraan cuaca, data maritim (gelombang laut, pasang surut), analisis iklim, peringatan dini bencana, konsultasi meteorologi, dan layanan pendidikan serta pelatihan.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Bagaimana cara mendapatkan peringatan dini cuaca ekstrem?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Peringatan dini cuaca ekstrem dapat diakses melalui website BMKG, aplikasi, media sosial resmi BMKG, atau sistem peringatan otomatis yang dikirim ke nomor telepon terdaftar.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Apakah stasiun ini menyediakan layanan konsultasi cuaca?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Ya, kami menyediakan layanan konsultasi cuaca untuk keperluan acara, perjalanan laut, atau kegiatan lainnya. Silakan hubungi kami untuk informasi lebih lanjut.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Di mana lokasi Stasiun Meteorologi Maritim Pontianak?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Stasiun Meteorologi Maritim Pontianak berlokasi di Jalan Adisucipto KM 10, Pontianak, Kalimantan Barat.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Bagaimana cara melaporkan fenomena cuaca atau geofisika?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Anda dapat melaporkan melalui aplikasi BMKG, website resmi, atau menghubungi call center BMKG di 196. Pastikan menyertakan detail lokasi, waktu, dan deskripsi fenomena yang diamati.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Apakah ada program pendidikan atau pelatihan yang diselenggarakan?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Ya, kami menyelenggarakan berbagai program pendidikan dan pelatihan tentang meteorologi, klimatologi, dan geofisika. Informasi jadwal dan pendaftaran dapat diakses melalui website BMKG atau menghubungi stasiun kami.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Bagaimana cara mendaftar akun di sistem ini?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Untuk mendaftar akun:<br>
                1. Klik tombol "Register" di halaman login<br>
                2. Isi nama lengkap, alamat email, nomor telepon (opsional), dan kata sandi<br>
                3. Konfirmasi kata sandi dengan mengetik ulang<br>
                4. Klik tombol "Register" untuk menyelesaikan pendaftaran<br>
                5. Anda akan diarahkan ke halaman login setelah berhasil mendaftar
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Bagaimana cara login ke sistem?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Untuk login ke sistem:<br>
                1. Buka halaman login<br>
                2. Masukkan alamat email dan kata sandi yang telah didaftarkan<br>
                3. Centang "Remember me" jika ingin tetap login di perangkat ini (opsional)<br>
                4. Klik tombol "Log in"<br>
                5. Jika lupa kata sandi, klik link "Forgot your password?" untuk mereset kata sandi
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Bagaimana cara mengajukan permintaan data atau surat?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Untuk mengajukan permintaan data atau surat:<br>
                1. Login ke akun Anda<br>
                2. Klik menu "Ajukan Surat/Data" di sidebar<br>
                3. Pilih jenis layanan dari dropdown yang tersedia<br>
                4. Jelaskan tujuan penggunaan data secara detail<br>
                5. Tentukan periode tanggal mulai dan akhir data yang dibutuhkan<br>
                6. Upload dokumen pendukung yang diperlukan (jika ada)<br>
                7. Klik tombol "Ajukan Permohonan" untuk mengirim<br>
                8. Tunggu verifikasi dari admin
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Bagaimana proses pembayaran untuk layanan berbayar?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Untuk layanan berbayar (PNBP):<br>
                1. Setelah pengajuan diverifikasi, admin akan mengupload file e-Billing<br>
                2. Download file e-Billing dari menu "Riwayat Pengajuan" atau detail pengajuan<br>
                3. Lakukan pembayaran sesuai dengan instruksi di e-Billing<br>
                4. Upload bukti pembayaran melalui tombol "Bayar" di riwayat pengajuan<br>
                5. Pilih metode pembayaran dan isi nomor referensi<br>
                6. Upload foto/scan bukti pembayaran (format JPG, PNG, max 5MB)<br>
                7. Tunggu verifikasi pembayaran dari admin<br>
                8. Jika ditolak, perbaiki dan upload ulang bukti pembayaran
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Bagaimana cara melacak status pengajuan saya?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Untuk melacak status pengajuan:<br>
                1. Login ke akun Anda<br>
                2. Klik menu "Riwayat Pengajuan" di sidebar<br>
                3. Lihat status pengajuan di kolom "Status"<br>
                4. Klik "Detail" untuk melihat informasi lengkap pengajuan<br>
                5. Status yang mungkin: Menunggu Review, Diproses, Terverifikasi, Menunggu Pembayaran, Sudah Bayar, Sedang Diproses, Selesai, atau Ditolak<br>
                6. Untuk pengajuan yang ditolak, klik "Kesalahan" untuk melihat alasan penolakan
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Apa yang harus dilakukan jika pengajuan ditolak?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Jika pengajuan ditolak:<br>
                1. Baca alasan penolakan di detail pengajuan<br>
                2. Perbaiki dokumen atau data yang bermasalah<br>
                3. Klik tombol "Upload File" untuk mengupload file tambahan<br>
                4. Atau klik "Kirim Ulang" untuk mengirim ulang pengajuan<br>
                5. Jika perlu membuat pengajuan baru, klik "Buat Pengajuan Baru"<br>
                6. Pastikan semua persyaratan terpenuhi sebelum mengirim ulang
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question text-white" onclick="toggleFAQ(this)">
                Bagaimana cara mengupdate profil saya?
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Untuk mengupdate profil:<br>
                1. Login ke akun Anda<br>
                2. Klik menu "Profil Saya" di sidebar<br>
                3. Update nama lengkap, email, atau nomor telepon<br>
                4. Jika ingin mengubah kata sandi, isi field "Password Baru" (opsional)<br>
                5. Konfirmasi password baru jika diubah<br>
                6. Klik tombol "Update Profil" untuk menyimpan perubahan
            </div>
        </div>


    </div>

    <!-- Footer -->
    <footer class="mt-12 py-8">
        <div class="content-card">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h4 class="text-xl font-bold mb-4">BMKG Stasiun Meteorologi Maritim Pontianak</h4>
                    <p class="text-sm">Menyediakan layanan meteorologi yang mencakup informasi cuaca, iklim, dan geofisika untuk mendukung keselamatan pelayaran dan kegiatan masyarakat di wilayah Pontianak.</p>
                </div>
                <div>
                    <h4 class="text-xl font-bold mb-4">Kontak Kami</h4>
                    <p class="text-sm"><i class="fas fa-map-marker-alt mr-2"></i>Stasiun Meteorologi Maritim Pontianak, Komplek Pelabuhan Dwikora Pontianak, Pontianak, Indonesia</p>
                    <p class="text-sm"><i class="fas fa-phone mr-2"></i>0561769906 / 08989111213</p>
                    <p class="text-sm"><i class="fas fa-envelope mr-2"></i>info@bmkg-pontianak.go.id</p>
                    <div class="mt-4">
                        <p class="text-sm font-semibold mb-2">Ikuti Kami:</p>
                        <div class="flex space-x-4">
                            <a href="https://www.instagram.com/infobmkg.maritimkalbar/" target="_blank" class="text-white hover:text-blue-300"><i class="fab fa-instagram text-lg"></i></a>
                            <a href="https://www.facebook.com/infobmkg.maritimkalbar" target="_blank" class="text-white hover:text-blue-300"><i class="fab fa-facebook text-lg"></i></a>
                            <a href="https://twitter.com/bmkgmaritimpnk" target="_blank" class="text-white hover:text-blue-300"><i class="fab fa-twitter text-lg"></i></a>
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
                        <li><a href="https://www.bmkg.go.id/" target="_blank" class="hover:text-blue-300">BMKG Pusat</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/20 mt-8 pt-4 text-center">
                <p class="text-sm">© 2025 BMKG Stasiun Meteorologi Maritim Pontianak. All rights reserved. Intern students of the Informatics Facilities Development (UBSI Kota Pontianak) program.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleFAQ(element) {
            const faqItem = element.parentElement;
            faqItem.classList.toggle('active');
        }
    </script>
</body>
</html>
