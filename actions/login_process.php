<?php
session_start();
require_once '../includes/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT UserID, Username, PasswordHash, Role FROM pengguna WHERE Username = ?";

    if ($stmt = $koneksi->prepare($sql)) {
        $stmt->bind_param("s", $param_username);
        $param_username = $username;

        if ($stmt->execute()) {
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $username, $hashed_password, $role);

                if ($stmt->fetch()) {
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['userid'] = $id;
                        $_SESSION['username'] = $username;
                        $_SESSION['role'] = $role;

                        $_SESSION['test_success'] = "Login Berhasil! UserID Anda adalah: " . $id;

                        header("Location: ../pages/dashboard.php");
                        exit;
                    } else {
                        $_SESSION['login_error'] = "Password yang Anda masukkan salah.";
                    }
                }
            } else {
                $_SESSION['login_error'] = "Username tidak ditemukan.";
            }
        } else {
            $_SESSION['login_error'] = "Terjadi kesalahan pada sistem.";
        }
        $stmt->close();
    }

    header("Location: ../index.php");
    exit;
}
$koneksi->close();
?>