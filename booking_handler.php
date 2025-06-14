<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_id'])) {
    header('Location: user_page.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $id_service = $_POST['id_service'];
    $id_kapster = $_POST['id_kapster'];
    $tanggal_booking = $_POST['tanggal_booking'];
    $id_slot = $_POST['id_slot'];
    $id_metode = $_POST['id_metode'];
    $bukti_transfer_filename = null;

    $check_sql = "SELECT COUNT(*) FROM bookings 
                  WHERE id_kapster = ? 
                  AND id_slot = ? 
                  AND tanggal_booking = ? 
                  AND status_booking != 'Batal'";
                  
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("sis", $id_kapster, $id_slot, $tanggal_booking);
    $stmt_check->execute();
    $stmt_check->bind_result($booking_count);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($booking_count > 0) {
        $_SESSION['booking_status'] = "Error: Maaf, jadwal untuk kapster tersebut pada waktu yang dipilih sudah terisi. Silakan pilih waktu atau kapster lain.";
        header('Location: user_page.php');
        exit; 
    }

    $stmt_price = $conn->prepare("SELECT harga FROM services WHERE id_service = ?");
    $stmt_price->bind_param("s", $id_service); 
    $stmt_price->execute();
    $result_price = $stmt_price->get_result();
    if ($result_price->num_rows > 0) {
        $total_harga = $result_price->fetch_assoc()['harga'];
    } else {
        $_SESSION['booking_status'] = "Error: Layanan yang dipilih tidak valid.";
        header('Location: user_page.php');
        exit;
    }
    $stmt_price->close();

    if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["bukti_transfer"]["name"], PATHINFO_EXTENSION);
        $bukti_transfer_filename = uniqid('transfer_', true) . '.' . $file_extension;
        $target_file = $target_dir . $bukti_transfer_filename;

        if (!move_uploaded_file($_FILES["bukti_transfer"]["tmp_name"], $target_file)) {
            $_SESSION['booking_status'] = "Maaf, terjadi error saat mengupload file Anda.";
            header('Location: user_page.php');
            exit;
        }
    }

    $sql = "INSERT INTO bookings (user_id, id_service, id_kapster, tanggal_booking, id_slot, id_metode, total_harga, bukti_transfer) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssiiis", $user_id, $id_service, $id_kapster, $tanggal_booking, $id_slot, $id_metode, $total_harga, $bukti_transfer_filename);

    if ($stmt->execute()) {
        $_SESSION['booking_status'] = "Booking Anda berhasil! Silakan tunggu konfirmasi dari kami.";
    } else {
        $_SESSION['booking_status'] = "Error: Booking gagal. " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header('Location: user_page.php');
    exit;

} else {
    header('Location: user_page.php');
    exit;
}
?>