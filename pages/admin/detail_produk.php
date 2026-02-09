<?php
require('../../config/koneksi.php');
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
    exit();
}
$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;

$usaha = [
    'nama_produk' => '',
    'produsen' => '',
    'jenis_produk' => '',
    'provinsi' => '',
    'kabupaten' => '',
    'kecamatan' => '',
    'alamat' => '',
    'latitude' => '',
    'longitude' => '',
    'nomor_sertifikat' => '',
    'tanggal_terbit' => '',
    'gambar_produk' => ''
];

if ($id_produk > 0) {
    $query = "SELECT * FROM produk WHERE id_produk = $id_produk";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result)) {
        $usaha = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan.'); window.location='../admin/usaha.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('ID tidak valid.'); window.location='../admin/usaha.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/detaill_produk.css"> 
</head>
<body>
<nav class="navbar">
    <button id="sidebarToggle" class="btn btn-sm btn-outline-secondary ms-2" style="position: absolute; left: 10px; top: 5px; z-index: 99999;">
        <i class="fas fa-bars"></i>
    </button>
    <span class="navbar-brand"></span>
    <div class="ms-auto d-flex align-items-center">
        <span id="currentDateTime" class="fw-bold text-primary me-3"></span>
        <div class="vr mx-3"></div>
        <div class="d-flex align-items-center">
            <span class="fw-bold text-dark"><?php echo $_SESSION['nama']; ?></span>
            <img src="../../uploads/<?php echo $_SESSION['profile']; ?>" alt="Profile" style="width:30px; height:30px; object-fit:cover; border-radius:50%; margin-left:8px; margin-right:15px;">
        </div>
    </div>
</nav>
<div class="row">
    <div id="mySidenav" class="sidenav">
        <h6 class="text-center">SISTEM INFORMASI PEMETAAN SERTIFIKASI HALAL</h6>
        <hr>
        <a href="../admin/index_admin.php" class="icon-a">
            <i class="fa fa-dashboard icons"></i> <span class="menu-label">Dashboard</span>
        </a>
        <a href="../admin/data_lokasi.php" class="icon-a">
            <i class="fa fa-location-arrow icons"></i> <span class="menu-label">Data Usaha</span>
        </a>
        <a href="../admin/data_produk.php" class="icon-a">
            <i class="fa fa-building icons"></i> <span class="menu-label">Data Produk</span>
        </a>
        <a href="../admin/tambah_usaha.php" class="icon-a">
            <i class="fa fa-map-marker icons"></i> <span class="menu-label">Tambah Usaha</span>
        </a>
        <a href="../admin/tambah_produk.php" class="icon-a">
            <i class="fa fa-plus-square icons"></i> <span class="menu-label">Tambah Produk</span>
        </a>
        <a href="../admin/peta_penyebaran.php" class="icon-a">
            <i class="fa fa-map icons"></i> <span class="menu-label">Peta</span>
        </a>
        <a href="../admin/akun.php" class="icon-a">
            <i class="fa fa-user-circle icons"></i> <span class="menu-label">Profile</span>
        </a>
        <a href="#" onclick="confirmLogout()" class="icon-a">
            <i class="fa fa-sign-out icons"></i> <span class="menu-label">Log Out</span>
        </a>
        <div class="mt-auto text-center text-white py-3">
            <hr>
            <small class="menu-label">&copy; 2025 Ibrahim</small>
        </div>
    </div>
</div>
<div id="main2">
    <div class="row-button d-flex justify-content-between align-items-center mb-3">
        <h4 class="page-title m-0">Produk - <?= htmlspecialchars($usaha['nama_produk']) ?></h4>
        <a href="../admin/data_produk.php" class="btn btn-back">Kembali</a>
    </div>
    
    <div class="row">
        <div class="parent-container-de">
            <div class="container-up-de">
                <h4 class="mb-3">Detail Data</h4>
            </div>
            <div class="container-de">
                <div class="form-row">
                    <span class="label"><strong>Nama Produk</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['nama_produk']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Produsen</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['produsen']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Jenis Produk</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['jenis_produk']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Nomor Sertifikat</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['nomor_sertifikat']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Tanggal Terbit</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['tanggal_terbit']) ?></span>
                </div>
            </div>
        </div>
        <div class="parent-container-image">
            <div class="container-up-image">
                <h4 class="mb-3">Preview Gambar Produk</h4>
            </div>
            <div class="container-preview">
                <img src="../../uploads/<?= htmlspecialchars($usaha['gambar_produk']) ?>" alt="Gambar Produk" class="product-image">
            </div>
            <div id="imageModal" class="modal-image" style="display:none;">
                <span class="close-image" onclick="closeModal()">&times;</span>
                <img class="modal-content-image" id="imgFull">
            </div>
        </div>
    </div>
</div>
<script>
    // Saat gambar diklik, tampilkan modal
    document.querySelector(".product-image").addEventListener("click", function() {
        var modal = document.getElementById("imageModal");
        var modalImg = document.getElementById("imgFull");
        modal.style.display = "block";
        modalImg.src = this.src;
    });

    // Fungsi menutup modal
    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }

    // logout
    function logout() {
        sessionStorage.removeItem('userSession');
        window.location.href = '../admin/login_admin.php';
    }

    // waktu
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const date = now.toLocaleDateString('id-ID', options);
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('currentDateTime').textContent = `${date}, ${time}`;
    }

    setInterval(updateDateTime, 1000); // Perbarui setiap detik
    updateDateTime(); // Inisialisasi langsung saat halaman dimuat

    const toggleBtn = document.getElementById('sidebarToggle');
    const sidenav = document.querySelector('.sidenav');
    const main2 = document.getElementById('main2');
    const navbar = document.querySelector('.navbar');

    toggleBtn.addEventListener('click', () => {
        sidenav.classList.toggle('collapsed');
        main2.classList.toggle('collapsed');
        navbar.classList.toggle('collapsed');
    });
</script>
</body>
</html>