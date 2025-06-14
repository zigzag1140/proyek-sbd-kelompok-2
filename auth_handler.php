<?php
session_start();

require 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    $action = $_POST['action'];

    if ($action == 'register') {
        $fullName = $_POST['full_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone_number'];
        $password = $_POST['password'];

        if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
            $_SESSION['register_error'] = "Semua kolom wajib diisi!";
            $_SESSION['active_form'] = 'register';
            header("Location: index.php"); 
            exit();
        }

        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['register_error'] = "Email sudah terdaftar. Silakan gunakan email lain.";
            $_SESSION['active_form'] = 'register';
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt_insert = $conn->prepare("INSERT INTO users (full_name, email, phone_number, password) VALUES (?, ?, ?, ?)");
            $stmt_insert->bind_param("ssss", $fullName, $email, $phone, $hashedPassword);

            if ($stmt_insert->execute()) {
                $_SESSION['register_success'] = "Registrasi berhasil! Silakan login.";
                $_SESSION['active_form'] = 'login';
            } else {
                $_SESSION['register_error'] = "Terjadi kesalahan. Silakan coba lagi.";
                $_SESSION['active_form'] = 'register';
            }
            $stmt_insert->close();
        }
        $stmt->close();
        header("Location: index.php"); 
        exit();
    }

    if ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = "Email dan password wajib diisi!";
            $_SESSION['active_form'] = 'login';
            header("Location: index.php");
            exit();
        }

        $query = "SELECT user_id, full_name, password, role FROM users WHERE email = ?";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true); 
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] == 'admin') {
                    header("Location: admin_page.php"); 
                } else {
                    header("Location: user_page.php"); 
                }
                exit();

            } else {
                $_SESSION['login_error'] = "Email atau password salah.";
                $_SESSION['active_form'] = 'login';
            }
        } else {
            $_SESSION['login_error'] = "Email atau password salah.";
            $_SESSION['active_form'] = 'login';
        }
        
        $stmt->close();
        header("Location: index.php"); 
        exit();
    }
}

header("Location: index.php");
exit();

?>