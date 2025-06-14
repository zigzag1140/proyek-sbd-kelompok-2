<?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['user_role'] !== 'user') {
        header('Location: index.php');
        exit;
    }
    require 'koneksi.php';
    $userName = htmlspecialchars($_SESSION['user_name']);
    $booking_status_message = $_SESSION['booking_status'] ?? '';
    unset($_SESSION['booking_status']);
    $services_result = $conn->query("SELECT id_service, nama_service, harga FROM services ORDER BY nama_service");
    $services = $services_result->fetch_all(MYSQLI_ASSOC);
    $kapsters_result = $conn->query("SELECT id_kapster, nama_kapster FROM kapsters ORDER BY nama_kapster");
    $kapsters = $kapsters_result->fetch_all(MYSQLI_ASSOC);
    $slots_result = $conn->query("SELECT id_slot, jam FROM slot_waktu ORDER BY jam");
    $slots = $slots_result->fetch_all(MYSQLI_ASSOC);
    $sql_metode = "SELECT id_metode, nama_metode FROM metode_pembayaran WHERE is_active = 1";
    $metode_result = $conn->query($sql_metode);
    if ($metode_result === FALSE) {
        die("Error saat mengambil data metode pembayaran: " . $conn->error);
    }
    $metode_pembayaran = $metode_result->fetch_all(MYSQLI_ASSOC);
    $service_prices = array_column($services, 'harga', 'id_service');
    $current_user_id = $_SESSION['user_id']; 

$current_user_id = $_SESSION['user_id']; 

$sql_history = "SELECT 
                    b.tanggal_booking,
                    b.total_harga,
                    b.status_booking,
                    b.bukti_transfer,
                    s.nama_service,
                    k.nama_kapster,
                    sw.jam,
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coga Barbershop</title>
    <link rel="icon" type="image/png" href="gambar/logo.jpg">
    <link rel="stylesheet" href="style.css">
    
    <style>

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); 
            z-index: 19;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .overlay.active-popup {
            opacity: 1;
            visibility: visible;
        }

        .wrapper {
            position: fixed;
            width: 400px;
            height: auto; 
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden; 
            backdrop-filter: blur(15px); 
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform .5s ease;
            z-index: 20;
        }
        .wrapper.active-popup {
            transform: translate(-50%, -50%) scale(1);
        }

        .form-box.booking {
            width: 100%;
            padding: 40px;
            box-sizing: border-box;
        }

        .form-box h2 {
            font-size: 2em;
            color: #fff;
            text-align: center;
            margin-bottom: 25px;
        }
   
        .input-box {
            position: relative;
            width: 100%;
            height: 50px;
            border-bottom: 2px solid #fff;
            margin: 30px 0;
        }

        .input-box .icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2em;
            color: #fff;
        }
        
        .input-box select,
        .input-box input[type="date"],
        .input-box input[type="file"] {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            outline: none;
            font-size: 1em;
            color: #fff;
            padding-left: 5px;
            -webkit-appearance: none; 
            -moz-appearance: none;
            appearance: none;
        }
        
        .input-box input[type="file"]::file-selector-button {
            color: #162938;
            background-color: #fff;
            border: none;
            padding: .2em .4em;
            border-radius: .2em;
            font-size: 0.8em;
            cursor: pointer;
            margin-top: 5px;
        }

        .input-box input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        .input-box input[type="date"]:required:invalid::-webkit-datetime-edit {
            color: rgba(255, 255, 255, 0.7); 
        }

        .input-box select option {
            background-color: #333;
            color: #fff;
        }

        .btn {
            width: 100%;
            height: 45px;
            background: #fff;
            border: none;
            outline: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            color: #162938;
            font-weight: 600;
            margin-top: 15px;
        }
        
        .icon-close {
            position: absolute;
            top: 0;
            right: 0;
            width: 45px;
            height: 45px;
            background: #fff;
            font-size: 2em;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom-left-radius: 20px;
            cursor: pointer;
            z-index: 1;
        }

    </style>
</head>
<body>

<?php
        if (isset($_SESSION['booking_status'])) {
            $is_error = stripos($_SESSION['booking_status'], 'error') !== false || stripos($_SESSION['booking_status'], 'gagal') !== false;
            $bg_color = $is_error ? '#f8d7da' : '#d4edda';
            $text_color = $is_error ? '#721c24' : '#155724';

            echo '<div style="background-color: '.$bg_color.'; color: '.$text_color.'; padding: 15px; border-radius: 5px; margin: 20px; text-align:center; font-weight: bold;">';
            echo htmlspecialchars($_SESSION['booking_status']);
            echo '</div>';
            
            unset($_SESSION['booking_status']);
        }
    ?>

    <header>
        <div class="logo-container">
            <img src="gambar/logo.png" alt="Coga Barbershop Logo" class="logo-image">
        </div>
        <nav class="navigation">
            <a href="#home">HOME</a>
            <a href="#service">SERVICES</a>
            <a href="#hair-artist">HAIR ARTIST</a>
            <a href="#about">ABOUT US</a>
            <a href="logout.php" class="btnlogin-popup" style="width: 100px; text-align: center; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">LOGOUT</a>
        </nav>
    </header>

    <section class="main-content" id="home">
        <?php if ($booking_status_message): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; max-width: 500px; text-align:center; margin-left:auto; margin-right:auto;">
                <?php echo $booking_status_message; ?>
            </div>
        <?php endif; ?>
        <p class="opening-hours">BUKA SETIAP HARI PUKUL 11:00 AM - 11:00 PM</p>
        <a href="#" class="book-now-btn" id="openBookingForm">BOOK NOW</a>
        <a href="riwayat_booking.php" class="history-link-btn">Lihat Riwayat Booking</a>
    </section>
    

    <section id="service" class="service">
        <div class="service-judul">
            <h2>SERVICES</h2>
        </div>
        <div class="service-container">
            <div class="service-item">
                <img src="gambar/haircut.jpg" alt="Haircut service">
                <p class="caption">HAIRCUT</p>
            </div>
            <div class="service-item">
                <img src="gambar/creeambath.jpg" alt="Creambath service">
                <p class="caption">CREAMBATH</p>
            </div>
            <div class="service-item">
                <img src="gambar/message.jpg" alt="Message service">
                <p class="caption">MESSAGE</p>
            </div>
            <div class="service-item">
                <img src="gambar/coloring.jpg" alt="Coloring service">
                <p class="caption">COLORING</p>
            </div>
        </div>
    </section>



    <section id="hair-artist" class="artist">
        <div class="hair-judul">
            <h2>HAIR ARTIST</h2>
        </div>
        <div class="artist-container">
            <div class="artist-item">
                <img src="gambar/pp1.jpeg" alt="Hair Artist Niko">
                <p class="caption">NIKO</p>
            </div>
            <div class="artist-item">
                <img src="gambar/pp2.jpg" alt="Hair Artist Vemas">
                <p class="caption">VEMAS</p>
            </div>
            <div class="artist-item">
                <img src="gambar/pp3.png" alt="Hair Artist Adit">
                <p class="caption">ADIT</p>
            </div>
        </div>
    </section>



    <section id="about" class="abt">
        <div class="abt-judul">
            <h2>ABOUT US</h2>
        </div>
        <div class="abt-container">
            <div class="abt-image-column">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3989.295829997733!2d100.4291391!3d-0.9272673!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd4b96cbb81ffad%3A0x4630b299d97276ad!2sCOGA%20BARBERSHOP!5e0!3m2!1sid!2sid!4v1749211517070!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <p class="image-caption">LOKASI KAMI</p>
            </div>
            <div class="abt-text-column">
                <h3>"Masuik Kusuik, Kalua Coga"</h3>
                <p>
                    Di Coga Barbershop, kami tidak hanya memotong rambut. Kami memegang teguh sebuah filosofi yang lahir dari kearifan lokal Padang: "Masuik Kusuik, Kalua Coga."
                </p>
                <p>
                    Motto ini adalah jantung dari semua yang kami lakukan. Kami percaya bahwa potongan rambut lebih dari sekadar rutinitas; ini adalah tentang transformasi. 'Kusuik' bukan hanya soal rambut yang berantakan, tapi juga bisa mewakili hari yang berat dan pikiran yang penat. Sementara 'Coga' adalah simbol penampilan yang rapi, tajam, dan penuh percaya diri.
                </p>
                <p>
                    Berdiri sejak tahun 2022, Coga Barbershop lahir dari keinginan untuk menciptakan sebuah ruang bagi pria di Padang untuk tidak hanya mendapatkan potongan rambut terbaik, tetapi juga untuk rehat sejenak dan menyegarkan kembali semangat mereka. Berlokasi strategis di Jalan Dr. Moh. Hatta, kami hadir untuk menjadi solusi penampilan Anda.
                </p>
                <div class="info-line">
                    <i data-feather="instagram"></i>
                    <a href="https://www.instagram.com/cogabarbershop/" target="_blank" rel="noopener noreferrer">@cogabarbershop</a>
                </div>
            </div> 
        </div> 
    </section> 

    <div class="overlay" id="bookingOverlay"></div>

    <div class="wrapper" id="bookingWrapper">
        <span class="icon-close" id="closeBookingForm">
            <ion-icon name="close-outline"></ion-icon>
        </span>
        <div class="form-box booking">
    <h2>Form Booking</h2>
    <form action="booking_handler.php" method="POST" enctype="multipart/form-data">
        <div class="input-box">
            <span class="icon"><ion-icon name="reader-outline"></ion-icon></span>
            <select name="id_service" id="serviceSelect" required> 
                <option value="" disabled selected>Pilih Layanan</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?php echo $service['id_service']; ?>"><?php echo htmlspecialchars($service['nama_service']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
                <div class="input-box">
                    <span class="icon"><ion-icon name="cut-outline"></ion-icon></span>
                    <select name="id_kapster" required>
                        <option value="" disabled selected>Pilih Kapster</option>
                        <?php foreach ($kapsters as $kapster): ?>
                            <option value="<?php echo $kapster['id_kapster']; ?>"><?php echo htmlspecialchars($kapster['nama_kapster']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="calendar-outline"></ion-icon></span>
                    <input type="date" name="tanggal_booking" required style="padding-right: 5px;">
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="time-outline"></ion-icon></span>
                    <select name="id_slot" required>
                        <option value="" disabled selected>Pilih Waktu</option>
                        <?php foreach ($slots as $slot): ?>
                            <option value="<?php echo $slot['id_slot']; ?>"><?php echo date("h:i A", strtotime($slot['jam'])); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                 <div class="input-box">
                    <span class="icon"><ion-icon name="cash-outline"></ion-icon></span>
                    <div class="price-display" style="color: #fff; font-size: 1em; height: 100%; display: flex; align-items: center; padding-left: 5px;">
                        <span id="totalHargaDisplay">Rp 0</span>
                    </div>
                </div>
               <div class="input-box">
                    <span class="icon"><ion-icon name="wallet-outline"></ion-icon></span>
                    <select name="id_metode" required>
                        <option value="" disabled selected>Pilih Metode Pembayaran</option>
                        <?php 
                        foreach ($metode_pembayaran as $metode): 
                        ?>
                            <option value="<?php echo $metode['id_metode']; ?>">
                                <?php echo htmlspecialchars($metode['nama_metode']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="cloud-upload-outline"></ion-icon></span>
                    <input type="file" name="bukti_transfer" accept="image/*" style="padding-top: 10px;">
                </div>
                <button type="submit" class="btn">Booking Sekarang</button>
            </form>
        </div>
    </div>

    <script src="script.js" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <script>
        const bookingWrapper = document.getElementById('bookingWrapper');
        const bookingOverlay = document.getElementById('bookingOverlay'); 
        const openBookingBtn = document.getElementById('openBookingForm');
        const closeBookingBtn = document.getElementById('closeBookingForm');

        openBookingBtn.addEventListener('click', (e) => {
            e.preventDefault();
            bookingWrapper.classList.add('active-popup');
            bookingOverlay.classList.add('active-popup'); 
        });

        const closeForm = () => {
            bookingWrapper.classList.remove('active-popup');
            bookingOverlay.classList.remove('active-popup'); 
        };

        closeBookingBtn.addEventListener('click', closeForm);
        bookingOverlay.addEventListener('click', closeForm); 

        document.querySelector('input[name="tanggal_booking"]').min = new Date().toISOString().split("T")[0];
        const servicePrices = <?php echo json_encode($service_prices); ?>;

        const serviceSelect = document.getElementById('serviceSelect');
        const totalHargaDisplay = document.getElementById('totalHargaDisplay');

        serviceSelect.addEventListener('change', function() {
            const selectedServiceId = this.value;

            const harga = servicePrices[selectedServiceId] || 0;

            const formattedHarga = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0 
            }).format(harga);

            totalHargaDisplay.textContent = formattedHarga;
        });
    </script>
</body>
</html>
