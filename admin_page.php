<?php
    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: index.php');
        exit;
    }

    require 'koneksi.php';

    $filtered_date = $_GET['tanggal'] ?? '';

    $sql_all_bookings = "SELECT 
                            b.id_booking, b.tanggal_booking, b.status_booking, b.bukti_transfer,
                            s.nama_service, k.nama_kapster, sw.jam,
                            u.full_name AS nama_pemesan,
                            u.phone_number,
                            mp.nama_metode
                        FROM bookings b
                        JOIN users u ON b.user_id = u.user_id
                        JOIN services s ON b.id_service = s.id_service
                        JOIN kapsters k ON b.id_kapster = k.id_kapster
                        JOIN slot_waktu sw ON b.id_slot = sw.id_slot
                        LEFT JOIN metode_pembayaran mp ON b.id_metode = mp.id_metode";

    if (!empty($filtered_date)) {
        $sql_all_bookings .= " WHERE b.tanggal_booking = ?";
    }

    $sql_all_bookings .= " ORDER BY b.tanggal_booking DESC, sw.jam DESC";
    
    $stmt = $conn->prepare($sql_all_bookings);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    if (!empty($filtered_date)) {
        $stmt->bind_param("s", $filtered_date);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $all_bookings = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Admin</title>
    <link rel="icon" type="image/png" href="gambar/logo.jpg">
    <link rel="stylesheet" href="style.css">
    <style>
    body {
        background-color: white; 
        margin: 0;
        font-family: sans-serif; 
    }
    
    .history-section .container {
        padding: 20px 30px; 
        margin: 30px auto;  
        max-width: 1400px;  
        background-color: #fff; 
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05); 
    }
    
    .btn-edit {
        display: inline-block;
        background-color: #ffc107;
        color: #212529;
        padding: 5px 10px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 12px;
        font-weight: bold;
        border: none;
        cursor: pointer;
    }
    .btn-edit:hover {
        background-color: #e0a800;
    }

    .history-table {
        width: 100%; 
        border-collapse: collapse;
        margin-top: 20px;
    }

    .history-table th, .history-table td {
        padding: 12px 10px; 
        text-align: left;
        border: 1px solid #dee2e6;
        word-wrap: break-word; 
        overflow-wrap: break-word;
        white-space: nowrap; 
    }
    
    .history-table th {
        background-color: #f8f9fa; 
    }

    .history-table td:nth-child(2), 
    .history-table td:nth-child(8) { 
        white-space: normal;
    }

</style>

</head>
<body>
    <header>
        <div class="logo-container">
            <img src="gambar/logo.png" alt="Coga Barbershop Logo" class="logo-image">
        </div>
        <nav class="navigation">
            <a href="admin_page.php">DASHBOARD</a>
            <a href="logout.php">LOGOUT</a>
        </nav>
    </header>

    <section class="history-section">
        <div class="container">
            <h2>Daftar Booking</h2>

            <?php if (isset($_SESSION['update_status'])): ?>
                <div class="status-message-admin">
                    <?php echo htmlspecialchars($_SESSION['update_status']); ?>
                </div>
                <?php unset($_SESSION['update_status']); ?>
            <?php endif; ?>

            <form action="admin_page.php" method="GET" class="filter-form">
                <div class="form-group">
                    <label for="tanggal">Tampilkan Booking untuk Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" value="<?php echo htmlspecialchars($filtered_date); ?>">
                    <button type="submit" class="filter-btn">Filter</button>
                    <a href="admin_page.php" class="reset-btn">Tampilkan Semua</a>
                </div>
            </form>

            <table class="history-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pemesan</th>
                        <th>No. HP</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Layanan</th>
                        <th>Kapster</th>
                        <th>Metode Pembayaran</th>
                        <th>Bukti Transfer</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($all_bookings)): ?>
                        <tr>
                            <td colspan="11" style="text-align:center;">Tidak ada data booking untuk tanggal yang dipilih.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($all_bookings as $booking): ?>
                            <tr>
                                <td>#<?php echo $booking['id_booking']; ?></td>
                                <td><?php echo htmlspecialchars($booking['nama_pemesan']); ?></td>
                                <td><?php echo htmlspecialchars($booking['phone_number']); ?></td>
                                <td><?php echo date("d M Y", strtotime($booking['tanggal_booking'])); ?></td>
                                <td><?php echo date("h:i A", strtotime($booking['jam'])); ?></td>
                                <td><?php echo htmlspecialchars($booking['nama_service']); ?></td>
                                <td><?php echo htmlspecialchars($booking['nama_kapster']); ?></td>
                                <td><?php echo htmlspecialchars($booking['nama_metode'] ?? '-'); ?></td>
                                <td>
                                    <?php if (!empty($booking['bukti_transfer'])): ?>
                                        <a href="uploads/<?php echo htmlspecialchars($booking['bukti_transfer']); ?>" target="_blank">Lihat Bukti</a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $booking['status_booking'])); ?>">
                                        <?php echo htmlspecialchars($booking['status_booking']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_booking.php?id=<?php echo $booking['id_booking']; ?>" class="btn-edit">Ubah</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
    </section>
</body>
</html>