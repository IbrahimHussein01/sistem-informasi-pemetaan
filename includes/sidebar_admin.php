<?php
require('../../config/koneksi.php');
?>

<link rel="stylesheet" href="../assets/css/side.css">
<script src="../assets/js/sidebar.js"></script>
<nav class="navbar">
    <button id="toggleSidebar" class="toggle-btn">
        <i class="fas fa-bars"></i>
    </button>
    <span class="navbar-brand"></span>
    <div class="ms-auto d-flex align-items-center flex-nowrap">
        <span id="currentDateTime" class="fw-bold text-primary me-3"></span>
        <div class="vr mx-3"></div>
        <div class="d-flex align-items-center">
            <span class="fw-bold text-dark"><?php echo $_SESSION['nama']; ?></span>
            <img src="../uploads/<?php echo $_SESSION['profile']; ?>" alt="Profile" style="width:30px; height:30px; object-fit:cover; border-radius:50%; margin-left:8px; margin-right:15px;">
        </div>
    </div>
</nav>
<div class="row">
    <div id="mySidenav" class="sidenav">
        <h6 class="text-center">SISTEM INFORMASI PEMETAAN SERTIFIKASI HALAL</h6>
        <hr>
        <a href="../pages/admin/index_admin.php" class="icon-a">
            <i class="fa fa-dashboard icons"></i>
            <span>Dashboard</span>
        </a>
        <a href="../pages/admin/data_lokasi.php" class="icon-a">
            <i class="fa fa-location-arrow icons"></i> 
            <span>Data Usaha</span>
        </a>
        <a href="../pages/admin/data_produk.php" class="icon-a">
            <i class="fa fa-building icons"></i> 
            <span>Data Produk</span>
        </a>
        <a href="../pages/admin/tambah_usaha.php" class="icon-a">
            <i class="fa fa-map-marker icons"></i> 
            <span>Tambah Usaha</span>
        </a>
        <a href="../pages/admin/tambah_produk.php" class="icon-a">
            <i class="fa fa-plus-square icons"></i>
            <span>Tambah Produk</span>
        </a>
        <a href="../pages/admin/peta_penyebaran.php" class="icon-a">
            <i class="fa fa-map icons"></i> 
            <span>Peta Penyebaran</span>
        </a>
        <a href="../pages/admin/akun.php" class="icon-a">
            <i class="fa fa-user-circle icons"></i>
            <span>Profile</span>
        </a>
        <a href="#" onclick="confirmLogout()" class="icon-a">
            <i class="fa fa-sign-out icons"></i> 
            <span>Log Out</span>
        </a>
        <div class="mt-auto text-center text-white py-3">
            <hr>
            <small>&copy; 2025 Ibrahim</small>
        </div>
    </div>
</div>