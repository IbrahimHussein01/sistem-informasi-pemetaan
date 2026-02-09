<?php
require('../../config/koneksi.php');
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
    exit();
}
$id_usaha = isset($_GET['id']) ? intval($_GET['id']) : 0;

$usaha = [
    'nama_usaha' => '',
    'pemilik' => '',
    'alamat' => '',
    'provinsi' => '',
    'kabupaten' => '',
    'kecamatan' => '',
    'latitude' => '',
    'longitude' => '',
    'gambar_tempat_usaha' => ''
];

if ($id_usaha > 0) {
    // Ambil data usaha
    $query = "SELECT * FROM usaha WHERE id_usaha = $id_usaha";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result)) {
        $usaha = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Data tidak ditemukan.'); window.location='../admin/data_lokasi.php';</script>";
        exit();
    }

    // Ambil data produk berdasarkan id_usaha
    $query_produk = "SELECT * FROM produk WHERE id_usaha = $id_usaha";
    $result_produk = mysqli_query($koneksi, $query_produk);
    $produk = [];
    if ($result_produk && mysqli_num_rows($result_produk) > 0) {
        while ($row = mysqli_fetch_assoc($result_produk)) {
            $produk[] = $row;
        }
    }
} else {
    echo "<script>alert('ID tidak valid.'); window.location='../admin/data_lokasi.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Produk</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/detaill_usaha.css"> 
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
        <h4 class="page-title m-0">Usaha - <?= htmlspecialchars($usaha['nama_usaha']) ?></h4>
        <a href="../admin/data_lokasi.php" class="btn btn-back">Kembali</a>
    </div>
    <div class="row">
        <div class="parent-container-de">
            <div class="container-up-de">
                <h4 class="mb-3">Detail</h4>
            </div>
            <div class="container-de">
                <div class="form-row image-container">
                    <img src="../../uploads/<?= htmlspecialchars($usaha['gambar_tempat_usaha']) ?>" alt="Gambar Tempat Usaha" class="product-image-usaha">
                </div>
                <div class="form-row">
                    <span class="label"><strong>Nama Usaha</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['nama_usaha']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Pemilik</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['pemilik']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Provinsi</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['provinsi']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Kabupaten/Kota</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['kabupaten']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Kecamatan</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars(str_replace('_', ' ', $usaha['kecamatan'])) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Latitude</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['latitude']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Longitude</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['longitude']) ?></span>
                </div>

                <div class="form-row">
                    <span class="label"><strong>Alamat</strong></span>
                    <span class="colon">:</span>
                    <span class="value"><?= htmlspecialchars($usaha['alamat']) ?></span>
                </div>
            </div>
        </div>
        <div class="parent-container-map">
            <div class="container-up-map">
                <h4 class="mb-3">Lokasi Usaha</h4>
            </div>
            <div class="container-map">
                <div id="map"></div>
                <button id="getDirection" class="btn btn-primary mt-3">Get Direction</button>
            </div>
        </div>
    </div>
    <div class="parent-container-menu">
        <div class="container-up-menu">
            <h4 class="mb-3">Menu Usaha</h4>
        </div>
        <div class="container-menu">
            <?php if (!empty($produk)) : ?>
            <div class="products-grid">
                <?php foreach ($produk as $item) : ?>
                    <div class="product-card">
                        <!-- Gambar Produk -->
                        <?php if (!empty($item['gambar_produk'])) : ?>
                            <img src="../../uploads/<?= htmlspecialchars($item['gambar_produk']) ?>" 
                                alt="<?= htmlspecialchars($item['nama_produk']) ?>" 
                                class="product-image">
                        <?php endif; ?>
                        
                        <div class="product-body">
                            <h5 class="product-title"><?= htmlspecialchars($item['nama_produk']) ?></h5>
                            
                            <p class="product-text">
                                <strong>Jenis</strong>
                                <span class="colon">:</span>
                                <span class="value"><?= htmlspecialchars($item['jenis_produk']) ?></span>
                            </p>
                            
                            <p class="product-text">
                                <strong>Produsen</strong>
                                <span class="colon">:</span>
                                <span class="value"><?= htmlspecialchars($item['produsen']) ?></span>
                            </p>
                            
                            <p class="product-text">
                                <strong>Sertifikat</strong>
                                <span class="colon">:</span>
                                <span class="value">
                                    <?php if (!empty($item['nomor_sertifikat'])) : ?>
                                        <a href="https://bpjph.halal.go.id/search/sertifikat?nama_produk=&nama_pelaku_usaha=&no_sertifikat=<?= urlencode($item['nomor_sertifikat']) ?>" 
                                        target="_blank" 
                                        class="sertifikat-link">
                                            <?= htmlspecialchars($item['nomor_sertifikat']) ?>
                                        </a>
                                    <?php else : ?>
                                        Tidak ada
                                    <?php endif; ?>
                                </span>
                            </p>
                            
                            <p class="product-text">
                                <strong>Terbit</strong>
                                <span class="colon">:</span>
                                <span class="value"><?= !empty($item['tanggal_terbit']) ? date('d-m-Y', strtotime($item['tanggal_terbit'])) : 'Belum ditentukan' ?></span>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php else : ?>
                    <p>Tidak ada produk yang tersedia.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="imageModal" class="modal-image" style="display:none;">
        <span class="close-image" onclick="closeModal()">&times;</span>
        <img class="modal-content-image" id="imgFull">
    </div>
</div>

<script>
    // Ambil semua gambar yang ingin diberi modal
    const allImages = document.querySelectorAll('.product-image, .product-image-usaha');

    allImages.forEach(function(img) {
        img.addEventListener("click", function () {
            var modal = document.getElementById("imageModal");
            var modalImg = document.getElementById("imgFull");
            modal.style.display = "block";
            modalImg.src = this.src;
        });
    });

    // Fungsi menutup modal
    function closeModal() {
        document.getElementById("imageModal").style.display = "none";
    }

    // (Opsional) tutup modal jika klik di luar gambar
    window.onclick = function(event) {
        const modal = document.getElementById("imageModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Inisialisasi peta
    var map = L.map('map').setView([<?= $usaha['latitude'] ?>, <?= $usaha['longitude'] ?>], 15);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([<?= $usaha['latitude'] ?>, <?= $usaha['longitude'] ?>]).addTo(map);

    // Fungsi untuk logout
    function confirmLogout() {
        if (confirm("Apakah Anda yakin ingin keluar?")) {
            logout();
        }
    }

    // direct ke goggle map
    document.getElementById('getDirection').addEventListener('click', function() {
        if (navigator.geolocation) {
            // Dapatkan lokasi pengguna
            navigator.geolocation.getCurrentPosition(function(position) {
                var userLat = position.coords.latitude; // Latitude pengguna
                var userLng = position.coords.longitude; // Longitude pengguna
                var destinationLat = <?= $usaha['latitude'] ?>; // Latitude tujuan
                var destinationLng = <?= $usaha['longitude'] ?>; // Longitude tujuan

                // Buat URL Google Maps dengan rute dari lokasi pengguna ke tujuan
                var url = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${destinationLat},${destinationLng}&travelmode=driving`;
                
                // Buka Google Maps di tab baru
                window.open(url, '_blank');
            }, function(error) {
                // Handle error jika lokasi tidak bisa didapatkan
                alert("Tidak dapat mendapatkan lokasi Anda. Pastikan GPS aktif dan izinkan akses lokasi.");
                console.error("Error getting location:", error);
            });
        } else {
            alert("Browser Anda tidak mendukung Geolocation.");
        }
    });
    
    // logout & time
    function logout() {
        sessionStorage.removeItem('userSession');
        window.location.href = '../admin/login_admin.php';
    }

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