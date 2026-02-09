<?php 
session_start();
include '../config/koneksi.php';

$email    = $_POST['email'];
$password = $_POST['password'];

// Ambil data admin berdasarkan email
$user = mysqli_query($koneksi, "SELECT * FROM admin WHERE email='$email'");
$data = mysqli_fetch_assoc($user);

if ($data && password_verify($password, $data['password'])) {
    // Simpan data ke session
    $_SESSION['id_admin'] = $data['id_admin'];
    $_SESSION['email']    = $data['email'];
    $_SESSION['nama']     = $data['nama'];
    $_SESSION['profile']  = $data['profile'];
    $_SESSION['login']    = true;

    header("Location: ../pages/admin/index_admin.php");
    exit();
} else {
    echo "<script>alert('Email atau Password salah!'); history.go(-1);</script>";
}
?>
