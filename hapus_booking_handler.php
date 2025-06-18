<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

require 'koneksi.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['update_status'] = "Error: ID Booking tidak valid.";
    header('Location: admin_page.php');
    exit;
}

$booking_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM bookings WHERE id_booking = ?");
$stmt->bind_param("i", $booking_id);

if ($stmt->execute()) {
    $_SESSION['update_status'] = "Sukses: Booking #" . $booking_id . " telah berhasil dihapus.";
} else {
    $_SESSION['update_status'] = "Error: Gagal menghapus booking. " . $stmt->error;
}

$stmt->close();
$conn->close();

header('Location: admin_page.php');
exit;
?>