# SMART BOOKS — Laravel 12

Aplikasi manajemen toko buku dengan katalog online, keranjang dan checkout, pembayaran transfer/QRIS/COD, POS kasir, stok, pengelolaan pengguna, laporan, penugasan kurir, lokasi pengiriman, dan bukti penerimaan.

## Persyaratan

- PHP 8.2 atau lebih baru beserta ekstensi Laravel yang diperlukan (PDO MySQL, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo).
- Composer 2.
- MySQL/MariaDB.
- Node.js dan npm bila ingin membangun ulang aset frontend.

## Instalasi lokal (Laragon / Windows)

1. Ekstrak folder project ke direktori web Laragon.
2. Buka terminal di folder project dan jalankan `composer install`.
3. Salin `.env.example` menjadi `.env`.
4. Buat database MySQL kosong, lalu sesuaikan `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env`.
5. Jalankan `php artisan key:generate`.
6. Jalankan `php artisan migrate --seed` untuk membuat struktur database dan data katalog/distributor contoh. Seeder tidak lagi membuat akun dengan password bawaan atau transaksi palsu. Untuk membuat admin pertama melalui seeder, isi `SMARTBOOKS_ADMIN_USERNAME`, `SMARTBOOKS_ADMIN_EMAIL`, dan `SMARTBOOKS_ADMIN_PASSWORD` (minimal 12 karakter) di `.env` sebelum menjalankan seeder. Akun kasir/kurir dapat dibuat dari menu pengguna setelah admin masuk.
7. Jalankan `php artisan storage:link`.
8. Jalankan `npm install` lalu `npm run build` bila aset frontend perlu dibangun.
9. Jalankan `php artisan serve` atau gunakan virtual host Laragon.

## Setelah update project

- Backup database dan folder `storage/app` terlebih dahulu.
- Jalankan `php artisan migrate` untuk menambahkan perubahan skema terbaru.
- Jalankan `php artisan optimize:clear`.
- Pastikan folder `storage` dan `bootstrap/cache` dapat ditulis oleh PHP.

## Scheduler pesanan kedaluwarsa

Perintah pembatalan otomatis pesanan Pending dijadwalkan oleh Laravel. Untuk menjalankannya otomatis di server, aktifkan scheduler Laravel (`php artisan schedule:run` setiap menit melalui Task Scheduler/Cron) atau jalankan worker sesuai konfigurasi deployment. Jangan mengandalkan scheduler jika belum diaktifkan di komputer/server.

## Keamanan dan distribusi

- Jangan membagikan file `.env`, kredensial database, `APP_KEY`, log, database produksi, atau foto bukti pembayaran/penerimaan. Tidak ada akun demo dengan password bawaan di paket ini.
- Bukti pembayaran dan penerimaan disimpan di disk private; aksesnya melalui route yang memerlukan autentikasi/otorisasi.
- Endpoint katalog API hanya mengembalikan atribut publik dan tidak mengekspos harga pokok.
- Login/registrasi dan checkout memiliki pembatasan request (rate limit); checkout memakai token unik agar pengiriman ulang form tidak membuat pesanan ganda.
- Perubahan status pengiriman online dilakukan oleh kurir yang ditugaskan; penyelesaian membutuhkan bukti penerimaan.
- Akun/buku yang memiliki riwayat transaksi/pasok tidak dapat dihapus melalui panel agar histori tidak rusak; pertahankan akun/buku tersebut dan ubah data yang masih boleh diubah.
- File `database/smart_books_dump.sql` dan SQLite lokal tidak disertakan; gunakan migration Laravel sebagai sumber skema.

## Catatan pengujian

Project ini perlu diuji pada lingkungan MySQL/MariaDB yang digunakan saat deployment. Jalankan `php artisan test`, `php artisan route:list`, dan uji alur pelanggan → kasir → kurir → laporan setelah migrasi. Pemeriksaan sintaks PHP saja tidak menjamin semua alur bisnis bebas bug.
