Proyek Sistem Booking Coga Barbershop
Proyek ini dikembangkan sebagai bagian dari tugas Sistem Basis Data (SBD) oleh Kelompok 2 yang terdiri dari:
1. Ghina Gaitsa         2311511003
2. Aulia Digyana Irwan  2311512001
3. Rahmadatul Afdal     2311512021
4. Rafi Ghani Alghifari 2311513003
5. Faris ArditioArafat  2311513025
Ini adalah proyek aplikasi web untuk sistem manajemen dan booking online untuk "Coga Barbershop". Aplikasi ini memungkinkan pelanggan untuk mendaftar, login, dan membuat janji temu secara online, sementara admin dapat mengelola dan memverifikasi semua booking yang masuk.
Fitur Utama
    •	Otentikasi Pengguna:
        o	Registrasi pengguna baru.
        o	Sistem Login yang aman dengan password yang di-hash.
        o	Perbedaan hak akses (role) antara User dan Admin.
    •	Tampilan Berdasarkan Peran:
        o	Halaman User: Pengguna yang telah login dapat melihat halaman utama dan mengakses form untuk membuat janji temu.
        o	Halaman Admin: Admin memiliki dashboard khusus untuk melihat, mengelola, dan memverifikasi semua data booking yang masuk.
    •	Sistem Booking:
        o	Form booking dinamis yang mengambil data layanan, kapster, dan slot waktu langsung dari database.
        o	Pengguna hanya dapat melakukan booking jika sudah login.
        o	Sistem pembayaran menggunakan metode transfer dengan kewajiban mengunggah bukti pembayaran.
        o	Pengecekan otomatis untuk mencegah double booking pada kapster di tanggal dan waktu yang sama.
    •	Dashboard Admin:
        o	Menampilkan semua data booking dalam format tabel yang rapi.
        o	Admin dapat melihat bukti pembayaran yang diunggah oleh pengguna.
        o	Fitur untuk memfilter booking berdasarkan tanggal.
        o	Admin dapat mengubah status booking (misalnya dari 'Pending' menjadi 'Dikonfirmasi' atau 'Ditolak').
Teknologi yang Digunakan
    •	Frontend: HTML, CSS, JavaScript
    •	Backend: PHP
    •	Database: MySQL / MariaDB
    •	Web Server: Apache (via XAMPP)
Struktur Database
Sistem ini menggunakan database relasional dengan beberapa tabel utama yang saling terhubung:
    1.	users: Menyimpan data pengguna dan peran (admin/user).
    2.	services: Menyimpan daftar layanan yang ditawarkan beserta harganya.
    3.	kapsters: Menyimpan daftar nama hair artist.
    4.	slot_waktu: Tabel master untuk slot waktu yang tersedia setiap hari.
    5.  metode_oembayaran: Menyimpan daftar metoode pembayaran
    6.	bookings: Tabel relasi utama yang mencatat setiap transaksi booking, menghubungkan semua tabel di atas dan menyimpan detail seperti tanggal, status, dan bukti pembayaran.
Instalasi dan Setup
Untuk menjalankan proyek ini di lingkungan lokal, ikuti langkah-langkah berikut:
    1.	Prasyarat: Pastikan Anda sudah menginstal XAMPP dengan layanan Apache dan MySQL yang sudah berjalan.
    2.	Letakkan Folder Proyek:
        o	Salin seluruh folder proyek Anda (misalnya Coga Barber) ke dalam direktori htdocs di dalam folder instalasi XAMPP Anda (contoh: C:\xampp\htdocs\).
    3.	Setup Database:
        o	Buka browser dan akses http://localhost/phpmyadmin.
        o	Buat database baru dengan nama yang Anda inginkan (misalnya coga_barber).
        o	Pilih database yang baru dibuat, lalu buka tab SQL.
        o	Salin dan jalankan semua perintah CREATE TABLE dari file skema database Anda untuk membuat semua tabel yang diperlukan.
        o	Isi tabel master (services, kapsters, slot_waktu, metode_pembayaran) dengan data awal.
    4.	Konfigurasi Koneksi:
        o	Buka file auth_handler.php atau koneksi.php di dalam proyek Anda.
        o	Pastikan variabel $dbname sesuai dengan nama database yang Anda buat pada langkah 3.
    5.	Buat Folder uploads:
        o	Di dalam folder proyek Anda, buat sebuah folder baru dengan nama uploads. Folder ini akan digunakan untuk menyimpan semua file bukti pembayaran yang diunggah oleh pengguna.
    6.	Akses Aplikasi:
        o	Buka browser dan akses URL: http://localhost/Coga Barber/ 