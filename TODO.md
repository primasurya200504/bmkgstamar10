# TODO: Perbaikan Fitur Filter dan Export PDF di Arsip & Laporan

## Tugas Utama

-   [x] Perbaiki filter (cari, tahun, bulan, kategori) agar berfungsi di halaman arsip
-   [x] Perbaiki export PDF terpilih agar berfungsi saat checklist pengajuan

## Langkah-langkah

1. [x] Update method `archives()` di AdminController untuk menerapkan filter sebelum pagination
2. [x] Perbaiki struktur form export di view agar checkbox termasuk dalam form
3. [x] Test filter dengan berbagai parameter
4. [x] Test export PDF terpilih dengan item yang dipilih
5. [x] Pastikan pagination bekerja dengan filter yang diterapkan

## File yang Terpengaruh

-   `app/Http/Controllers/AdminController.php` - update method archives()
-   `resources/views/admin/archives/index.blade.php` - perbaiki struktur form export

## Status

✅ **SELESAI** - Semua fitur filter dan export PDF terpilih telah diperbaiki dan berfungsi dengan benar.
