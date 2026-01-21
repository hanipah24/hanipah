<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../index.php");
    exit;
}

require_once '../includes/koneksi.php';

if ($_SESSION['role'] !== 'Administrator') {
    $_SESSION['status_registrasi'] = "Error: Anda tidak memiliki hak akses untuk mendaftarkan pengguna.";
    header("Location: ../pages/registrasi.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $status = '';

    if (empty($username) || empty($password) || empty($role)) {
        $status = "Error: Semua kolom harus diisi.";
    } elseif (strlen($password) < 6) {
        $status = "Error: Password minimal 6 karakter.";
    } elseif ($role !== 'Administrator' && $role !== 'Petugas') {
        $status = "Error: Peran yang dipilih tidak valid.";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $check_sql = "SELECT UserID FROM pengguna WHERE Username = ?";
            $check_stmt = $koneksi->prepare($check_sql);
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                $status = "Error: Username '{$username}' sudah digunakan.";
            } else {
                $insert_sql = "INSERT INTO pengguna (Username, PasswordHash, Role) VALUES (?, ?, ?)";
                $insert_stmt = $koneksi->prepare($insert_sql);
                $insert_stmt->bind_param("sss", $username, $hashed_password, $role);

                if ($insert_stmt->execute()) {
                    $status = "Sukses: Pengguna '{$username}' sebagai '{$role}' berhasil didaftarkan!";
                } else {
                    throw new Exception("Gagal menyimpan data ke database.");
                }

                $insert_stmt->close();
            }

            $check_stmt->close();
        } catch (Exception $e) {
            $status = "Error: " . $e->getMessage();
        }
    }

    $_SESSION['status_registrasi'] = $status;
    $koneksi->close();
    header("Location: ../pages/registrasi.php");
    exit;
} else {
    header("Location: ../pages/registrasi.php");
    exit;
}
?>