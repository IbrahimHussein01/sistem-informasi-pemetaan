<?php
require('../../config/koneksi.php');
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
    exit();
}

$id_admin = $_SESSION['id_admin']; 

// Ambil data admin dari database
$query = "SELECT * FROM admin WHERE id_admin = $id_admin";
$result = mysqli_query($koneksi, $query);

// Cek apakah data ditemukan
if ($result && mysqli_num_rows($result) > 0) {
    $admin = mysqli_fetch_assoc($result);
} else {
    echo "<script>alert('Data admin tidak ditemukan'); window.location='../admin/akun.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat/dist/leaflet-heat.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <link rel="stylesheet" href="../../assets/css/akun.css">
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
            <h4 class="page-title m-0">Profile - <?= htmlspecialchars($admin['nama']) ?></h4>
        </div>
        
        <div class="row">
            <div class="parent-container-de">
                <div class="container-up-de">
                    <h4 class="mb-3">Detail Data</h4>
                </div>
                <div class="container-de">
                    <div class="form-row">
                        <span class="label"><strong>Nama</strong></span>
                        <span class="colon">:</span>
                        <span class="value"><?= htmlspecialchars($admin['nama']) ?></span>
                    </div>

                    <div class="form-row">
                        <span class="label"><strong>Email</strong></span>
                        <span class="colon">:</span>
                        <span class="value"><?= htmlspecialchars($admin['email']) ?></span>
                    </div>
                    <div style="text-align: center; margin-top: 20px;">
                        <a href="../admin/perbarui_akun.php?id_admin=<?= $admin['id_admin'] ?>" class="btn-custom">Perbarui Akun</a>
                    </div>
                </div>
            </div>
            <div class="parent-container-image">
                <div class="container-up-image">
                    <h4 class="mb-3">Profile</h4>
                </div>
                <div class="container-preview">
                    <img src="../../uploads/<?= htmlspecialchars($admin['profile']) ?>" alt="Gambar Produk" class="product-image">
                </div>
                <!-- Modal Gambar -->
                <div id="imageModal" class="modal-image" style="display:none;">
                    <span class="close-image" onclick="closeModal()">&times;</span>
                    <img class="modal-content-image" id="imgFull">
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    document.querySelector(".product-image").addEventListener("click", function() {
        var modal = document.getElementById("imageModal");
        var modalImg = document.getElementById("imgFull");
        modal.style.display = "block";
        modalImg.src = this.src;
    });

    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        const entriesSelect = document.getElementById("entries");
        const searchInput = document.getElementById("search");
        const tableBody = document.querySelector("#dataTable tbody");

        entriesSelect.addEventListener("change", function() {
            const limit = parseInt(this.value);
            const rows = tableBody.querySelectorAll("tr");
            rows.forEach((row, index) => {
                row.style.display = index < limit ? "" : "none";
            });
        });

        searchInput.addEventListener("keyup", function() {
            const filter = this.value.toLowerCase();
            const rows = tableBody.querySelectorAll("tr");
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    });
    // Fungsi logout
    function confirmLogout() {
        if (confirm("Apakah Anda yakin ingin keluar?")) {
            logout();
        }
    }
    function logout() {
        sessionStorage.removeItem('userSession');
        window.location.href = '../admin/login_admin.php';
    }

    // tanggal
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const date = now.toLocaleDateString('id-ID', options);
        const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        document.getElementById('currentDateTime').textContent = `${date}, ${time}`;
    }

    setInterval(updateDateTime, 1000); 
    updateDateTime(); 

    // sidebar
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
</html>
