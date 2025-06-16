<?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user_role'] !== 'user') {
        header('Location: index.php');
        exit;
    }
    require 'koneksi.php';

    $current_user_id = $_SESSION['user_id'];

    $sql_history = "SELECT 
                        b.tanggal_booking, b.total_harga, b.status_booking, b.bukti_transfer,
                        s.nama_service, k.nama_kapster, sw.jam,
                        IFNULL(mp.nama_metode, 'Tidak ada') AS nama_metode
                    FROM bookings b
                    JOIN services s ON b.id_service = s.id_service
                    JOIN kapsters k ON b.id_kapster = k.id_kapster
                    JOIN slot_waktu sw ON b.id_slot = sw.id_slot
                    LEFT JOIN metode_pembayaran mp ON b.id_metode = mp.id_metode
                    WHERE b.user_id = ?
                    ORDER BY b.tanggal_booking DESC, sw.jam DESC";

    $stmt_history = $conn->prepare($sql_history);
    if ($stmt_history === false) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt_history->bind_param("i", $current_user_id);
    $stmt_history->execute();
    $history_result = $stmt_history->get_result();
    $history_bookings = $history_result->fetch_all(MYSQLI_ASSOC);
    $stmt_history->close();
    $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Booking - Coga Barbershop</title>
    <link rel="icon" type="image/png" href="gambar/logo.jpg">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="gambar/logo.png" alt="Coga Barbershop Logo" class="logo-image">
        </div>
        <nav class="navigation">
            <a href="user_page.php">HOME</a>
        </nav>
    </header>

    <section class="history-section">
        <div class="container">

            <h2>Riwayat Booking Anda</h2>
            
            <?php if (empty($history_bookings)): ?>
                <p class="no-history">Anda belum memiliki riwayat booking.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Layanan</th>
                                <th>Kapster</th>
                                <th>Metode Bayar</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                                <th>Bukti Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history_bookings as $booking): ?>
                                <tr>
                                    <td><?php echo date("d M Y", strtotime($booking['tanggal_booking'])); ?><br><small><?php echo date("h:i A", strtotime($booking['jam'])); ?></small></td>
                                    <td><?php echo htmlspecialchars($booking['nama_service']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['nama_kapster']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['nama_metode']); ?></td>
                                    <td>Rp <?php echo number_format($booking['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php $status_class = 'status-' . strtolower(str_replace(' ', '-', $booking['status_booking'])); ?>
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <?php echo htmlspecialchars($booking['status_booking']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($booking['bukti_transfer'])): ?>
                                            <a href="uploads/<?php echo htmlspecialchars($booking['bukti_transfer']); ?>" target="_blank" class="view-proof-btn">Lihat</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>