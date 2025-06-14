<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$booking_id = $_POST['id_booking'];
$new_status = $_POST['status_booking'];

if (empty($booking_id) || empty($new_status)) {
    $_SESSION['update_status'] = "Error: Data tidak lengkap.";
    header('Location: admin_page.php');
    exit;
}

$sql = "UPDATE bookings SET status_booking = ? WHERE id_booking = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $booking_id);

if ($stmt->execute()) {
    $_SESSION['update_status'] = "Booking #" . $booking_id . " berhasil diperbarui.";
} else {
    $_SESSION['update_status'] = "Error: Gagal memperbarui booking. " . $stmt->error;
}

$stmt->close();
$conn->close();

header('Location: admin_page.php');
exit;
?>