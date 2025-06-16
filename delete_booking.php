<?php
session_start();
require 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses dan methodnya GET
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Validasi ID booking dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['update_status'] = "Error: ID Booking tidak valid.";
    header('Location: admin_page.php');
    exit;
}

$booking_id = $_GET['id'];

// Menggunakan transaksi untuk memastikan semua proses delete berhasil atau semua dibatalkan
$conn->begin_transaction();

try {
    // PENTING: Hapus data dari tabel yang memiliki relasi terlebih dahulu
    // untuk menghindari error foreign key constraint.
    
    // 1. Hapus dari tabel 'pembayaran' (jika ada dan terhubung)
    // Sesuaikan nama tabel dan kolom jika berbeda
    $stmt_payment = $conn->prepare("DELETE FROM pembayaran WHERE id_booking = ?");
    $stmt_payment->bind_param("i", $id_booking);
    $stmt_payment->execute();
    $stmt_payment->close();

    // 2. Hapus dari tabel 'booking_details' (jika ada dan terhubung)
    $stmt_details = $conn->prepare("DELETE FROM booking_details WHERE id_booking = ?");
    $stmt_details->bind_param("i", $booking_id);
    $stmt_details->execute();
    $stmt_details->close();

    // 3. Setelah itu, baru hapus dari tabel utama 'bookings'
    $stmt_booking = $conn->prepare("DELETE FROM bookings WHERE id_booking = ?");
    $stmt_booking->bind_param("i", $booking_id);
    $stmt_booking->execute();

    // Jika semua query berhasil, simpan perubahan
    $conn->commit();
    $_SESSION['update_status'] = "Booking #" . $booking_id . " berhasil dihapus.";

} catch (mysqli_sql_exception $exception) {
    // Jika ada satu saja query yang gagal, batalkan semua perubahan
    $conn->rollback();
    $_SESSION['update_status'] = "Error: Gagal menghapus booking. " . $exception->getMessage();
}

$conn->close();

// Arahkan kembali ke halaman admin
header('Location: admin_page.php');
exit;
?>
