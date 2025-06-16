<?php
session_start();
// Mengimpor file koneksi dan autoloader dari Composer
require 'koneksi.php';
require 'vendor/autoload.php';

// Menggunakan kelas PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- VALIDASI AKSES DAN INPUT ---
// Memastikan hanya admin yang sudah login dan menggunakan metode POST yang bisa mengakses halaman ini.
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin' || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$booking_id = $_POST['id_booking'];
$new_status = $_POST['status_booking'];

// Memastikan data yang diterima tidak kosong.
if (empty($booking_id) || empty($new_status)) {
    $_SESSION['update_status'] = "Error: Data yang diterima tidak lengkap.";
    header('Location: admin_page.php');
    exit;
}

// --- PROSES UPDATE STATUS BOOKING ---
$conn->begin_transaction(); // Memulai transaksi untuk memastikan integritas data

try {
    // Menyiapkan query untuk memperbarui status booking
    $sql_update = "UPDATE bookings SET status_booking = ? WHERE id_booking = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update === false) {
        throw new Exception("SQL Error pada prepare UPDATE: " . $conn->error);
    }
    $stmt_update->bind_param("si", $new_status, $booking_id);
    $stmt_update->execute();

    // Memeriksa apakah update berhasil
    if ($stmt_update->affected_rows > 0) {
        $_SESSION['update_status'] = "Status untuk Booking #" . htmlspecialchars($booking_id) . " berhasil diperbarui menjadi '" . htmlspecialchars($new_status) . "'.";

        // --- PENGIRIMAN EMAIL BERDASARKAN STATUS BARU ---
        // Jika status adalah 'Dikonfirmasi' atau 'Selesai', kirim email notifikasi.
        if ($new_status === 'Dikonfirmasi' || $new_status === 'Selesai') {
            
            // Query untuk mengambil detail lengkap booking, termasuk harga layanan
            $sql_details = 
                "SELECT 
                    u.email, u.full_name, b.tanggal_booking, 
                    sw.jam, k.nama_kapster, s.nama_service, s.harga
                 FROM bookings AS b 
                 JOIN users AS u ON b.user_id = u.user_id 
                 JOIN slot_waktu AS sw ON b.id_slot = sw.id_slot 
                 JOIN kapsters AS k ON b.id_kapster = k.id_kapster 
                 JOIN services AS s ON b.id_service = s.id_service 
                 WHERE b.id_booking = ?";

            $stmt_details = $conn->prepare($sql_details);
            if ($stmt_details === false) {
                throw new Exception("SQL Error pada prepare SELECT: " . $conn->error);
            }
            $stmt_details->bind_param("i", $booking_id);
            $stmt_details->execute();
            $booking_details = $stmt_details->get_result()->fetch_assoc();
            $stmt_details->close();

            if ($booking_details) {
                $mail = new PHPMailer(true);

                // Konfigurasi Server SMTP
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'rahmadatul68@gmail.com'; // Ganti dengan alamat email Anda
                $mail->Password   = 'eyyw uuqt cbtq pqlf';    // Ganti dengan App Password Gmail Anda
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                $mail->CharSet    = 'UTF-8';

                // Pengirim dan Penerima
                $mail->setFrom('no-reply@cogabarbershop.com', 'Coga Barbershop');
                $mail->addAddress($booking_details['email'], $booking_details['full_name']);
                
                $mail->isHTML(true);

                // Menyesuaikan subjek dan isi email berdasarkan status
                if ($new_status === 'Dikonfirmasi') {
                    // --- EMAIL KONFIRMASI ---
                    $mail->Subject = 'Booking Anda di Coga Barbershop Dikonfirmasi! (ID: #' . $booking_id . ')';
                    $mail->Body    = 
                        "<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;'>"
                        . "<h2>Halo " . htmlspecialchars($booking_details['full_name']) . ",</h2>"
                        . "<p>Kabar baik! Booking Anda di Coga Barbershop sudah kami konfirmasi. Kami tidak sabar untuk membuat Anda tampil lebih keren!</p>"
                        . "<p>Berikut adalah detail booking Anda:</p>"
                        . "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>"
                        . "<tr><td style='padding: 8px; border: 1px solid #ddd; width: 30%;'><strong>ID Booking:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>#" . $booking_id . "</td></tr>"
                        . "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Layanan:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($booking_details['nama_service']) . "</td></tr>"
                        . "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Kapster:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($booking_details['nama_kapster']) . "</td></tr>"
                        . "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Tanggal:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . date('l, d F Y', strtotime($booking_details['tanggal_booking'])) . "</td></tr>"
                        . "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Waktu:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . date('H:i', strtotime($booking_details['jam'])) . " WIB</td></tr>"
                        . "</table>"
                        . "<p>Mohon datang 5-10 menit lebih awal. Jika Anda perlu mengubah jadwal, silakan hubungi kami. Sampai jumpa!</p>"
                        . "<p><strong>Salam hangat,<br>Tim Coga Barbershop</strong></p>"
                        . "</div>";

                } elseif ($new_status === 'Selesai') {
                    // --- EMAIL RESI SETELAH LAYANAN SELESAI ---
                    $harga_formatted = "Rp " . number_format($booking_details['harga'], 0, ',', '.');
                    $mail->Subject = 'Terima Kasih atas Kunjungan Anda! Resi Coga Barbershop (ID: #' . $booking_id . ')';
                    $mail->Body    = 
                        "<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: auto; border: 1px solid #ddd; padding: 20px;'>"
                        . "<h2>Terima Kasih, " . htmlspecialchars($booking_details['full_name']) . "!</h2>"
                        . "<p>Terima kasih telah mempercayakan penampilan Anda kepada Coga Barbershop. Kami harap Anda puas dengan hasilnya!</p>"
                        . "<p>Berikut adalah rincian transaksi Anda:</p>"
                        . "<table style='width: 100%; border-collapse: collapse; margin: 20px 0; background-color: #f9f9f9;'>"
                        . "<tr><td style='padding: 10px; border: 1px solid #ddd; width: 30%;'><strong>ID Booking:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>#" . $booking_id . "</td></tr>"
                        . "<tr><td style='padding: 10px; border: 1px solid #ddd;'><strong>Layanan:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($booking_details['nama_service']) . "</td></tr>"
                        . "<tr><td style='padding: 10px; border: 1px solid #ddd;'><strong>Kapster:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($booking_details['nama_kapster']) . "</td></tr>"
                        . "<tr><td style='padding: 10px; border: 1px solid #ddd;'><strong>Tanggal:</strong></td><td style='padding: 10px; border: 1px solid #ddd;'>" . date('l, d F Y', strtotime($booking_details['tanggal_booking'])) . "</td></tr>"
                        . "</table>"
                        . "<h3 style='border-bottom: 2px solid #eee; padding-bottom: 5px;'>Rincian Biaya</h3>"
                        . "<table style='width: 100%;'>"
                        . "<tr><td style='padding: 5px 0;'>" . htmlspecialchars($booking_details['nama_service']) . "</td><td style='padding: 5px 0; text-align: right;'>" . $harga_formatted . "</td></tr>"
                        . "<tr style='font-weight: bold; border-top: 1px solid #ddd;'><td style='padding: 10px 0;'>Total Pembayaran</td><td style='padding: 10px 0; text-align: right;'>" . $harga_formatted . "</td></tr>"
                        . "</table>"
                        . "<p>Kami sangat menantikan kedatangan Anda berikutnya. Jangan ragu untuk memberikan ulasan atau masukan kepada kami.</p>"
                        . "<p><strong>Salam Keren,<br>Tim Coga Barbershop</strong></p>"
                        . "</div>";
                }

                // Kirim email dan tambahkan notifikasi ke sesi
                $mail->send();
                $_SESSION['update_status'] .= " Email notifikasi telah berhasil dikirim.";

            } else {
                $_SESSION['update_status'] .= " Namun, data booking tidak ditemukan untuk pengiriman email.";
            }
        }
    } else {
        $_SESSION['update_status'] = "Tidak ada perubahan status. Kemungkinan status sudah sama.";
    }

    $stmt_update->close();
    $conn->commit(); // Konfirmasi semua perubahan jika berhasil

} catch (Exception $e) {
    $conn->rollback(); // Batalkan semua perubahan jika terjadi error
    // Mencatat error ke sesi untuk ditampilkan di halaman admin
    $_SESSION['update_status'] = "Error: Terjadi kesalahan. " . $e->getMessage();
}

$conn->close();

// Mengarahkan kembali ke halaman admin
header('Location: admin_page.php');
exit;
?>
