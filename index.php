<?php
    session_start();

    $login_error = $_SESSION['login_error'] ?? '';
    $register_error = $_SESSION['register_error'] ?? '';
    $register_success = $_SESSION['register_success'] ?? '';
    
    $active_form = $_SESSION['active_form'] ?? 'login';

    unset($_SESSION['login_error']);
    unset($_SESSION['register_error']);
    unset($_SESSION['register_success']);
    unset($_SESSION['active_form']);

    function showMessage($message, $isSuccess = false) {
        if (!empty($message)) {
            $class = $isSuccess ? 'success-message' : 'error-message';
            return "<p class='$class'>$message</p>";
        }
        return '';
    }
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
</head>



<body>



    <header>
        <div class="logo-container">
            <img src="gambar/logo.png" alt="Coga Barbershop Logo" class="logo-image">
        </div>
        <nav class="navigation">
            <a href="#home">HOME</a>
            <a href="#service">SERVICES</a>
            <a href="#hair-artist">HAIR ARTIST</a>
            <a href="#about">ABOUT US</a>
            <button class="btnlogin-popup">LOGIN</button>
        </nav>
    </header>



      <section class="main-content" id="home">
        <p class="opening-hours">BUKA SETIAP HARI PUKUL 11:00 AM - 11:00 PM</p>
        <a href="#" onclick="event.preventDefault(); document.querySelector('.wrapper').classList.add('active-popup'); document.body.classList.add('login-active');" class="book-now-btn">BOOK NOW</a>
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
                <h3>"Masuak Kusuik, Kalua Coga"</h3>
                <p>
                    Di Coga Barbershop, kami tidak hanya memotong rambut. Kami memegang teguh sebuah filosofi yang lahir dari kearifan lokal Padang: "Masuak Kusuik, Kalua Coga."
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
    <div class="overlay"></div>
    <div class="wrapper">
        <span class="icon-close">
            <ion-icon name="close-outline"></ion-icon>
        </span>


    <div class="form-box login">
            <h2>Sign In</h2>
            <form id="login-form" action="auth_handler.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="input-box">
                    <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                    <input type="email" name="email" required>
                    <label>Email</label>
                </div>
                <div class="input-box">
                    <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>Password</label>
                </div>
                <button type="submit" class="btn">Login</button>
                <div class="login-register">
                    <p>Don't have an account yet?<a href="#" class="register-link"> Sign Up!</a></p>
                </div>
            </form>
        </div>

    <div class="form-box register">
        <h2>Sign Up</h2>
        <form action="auth_handler.php" method="POST" id="register-form">
            <input type="hidden" name="action" value="register">

            <div id="register-message" style="text-align: center; font-weight: 500; margin-bottom: 15px;"></div>
        
            <div class="input-box">
                <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                <input type="text" name="full_name" required>
                <label>Full Name</label>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="mail-outline"></ion-icon></span>
                <input type="email" name="email" required>
                <label>Email</label>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="call-outline"></ion-icon></span>
                <input type="tel" name="phone_number" required>
                <label>Phone Number</label>
            </div>
            <div class="input-box">
                <span class="icon"><ion-icon name="lock-closed-outline"></ion-icon></span>
                <input type="password" name="password" required>
                <label>Password</label>
            </div>
            <button type="submit" class="btn">Register</button>
            <div class="login-register">
                <p>Already have an account?<a href="#" class="login-link"> Sign In!</a></p>
            </div>
        </form>
    </div>

</div>

    <script src="script.js" defer></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>
</html>    