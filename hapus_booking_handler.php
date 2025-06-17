<?php
session_start();
// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

require 'koneksi.php';

// Cek apakah ID booking ada dan valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['update_status'] = "Error: ID Booking tidak valid.";
    header('Location: admin_page.php');
    exit;
}

$booking_id = $_GET['id'];

// Siapkan dan jalankan statement DELETE
$stmt = $conn->prepare("DELETE FROM bookings WHERE id_booking = ?");
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    // Jika penghapusan berhasil
    $_SESSION['update_status'] = "Sukses: Booking #" . $booking_id . " telah berhasil dihapus.";
} else {
    // Jika penghapusan gagal
    $_SESSION['update_status'] = "Error: Gagal menghapus booking. " . $stmt->error;
}

$stmt->close();
$conn->close();

// Arahkan kembali ke halaman admin
header('Location: admin_page.php');
exit;
?>