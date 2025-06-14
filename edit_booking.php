<?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: index.php');
        exit;
    }

    require 'koneksi.php';

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header('Location: admin_page.php');
        exit;
    }
    $booking_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM bookings WHERE id_booking = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $_SESSION['update_status'] = "Error: Booking tidak ditemukan.";
        header('Location: admin_page.php');
        exit;
    }
    $booking = $result->fetch_assoc();
    $stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking #<?php echo $booking_id; ?></title>
    <link rel="icon" type="image/png" href="gambar/logo.jpg">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <img src="gambar/logo.png" alt="Coga Barbershop Logo" class="logo-image">
        </div>
    </header>

    <section class="history-section">
        <div class="container">
            <h2>Edit Booking #<?php echo htmlspecialchars($booking_id); ?></h2>

            <form action="update_booking_handler.php" method="POST" class="edit-form">
                <input type="hidden" name="id_booking" value="<?php echo htmlspecialchars($booking['id_booking']); ?>">

                <div class="form-group">
                    <label for="status_booking">Status Booking</label>
                    <select name="status_booking" id="status_booking">
                        <option value="Menunggu Konfirmasi" <?php if($booking['status_booking'] == 'Menunggu Konfirmasi') echo 'selected'; ?>>Menunggu Konfirmasi</option>
                        <option value="Dikonfirmasi" <?php if($booking['status_booking'] == 'Dikonfirmasi') echo 'selected'; ?>>Dikonfirmasi</option>
                        <option value="Selesai" <?php if($booking['status_booking'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                        <option value="Batal" <?php if($booking['status_booking'] == 'Batal') echo 'selected'; ?>>Batal</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="save-btn">Simpan Perubahan</button>
                    <a href="admin_page.php" class="cancel-btn">Batal</a>
                </div>
            </form>
        </div>
    </section>
</body>
</html>