<?php
require('../../config/koneksi.php');

$id_usaha = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data usaha
$query = "SELECT * FROM usaha WHERE id_usaha = $id_usaha";
$result = mysqli_query($koneksi, $query);
$usaha = [];
if ($result && mysqli_num_rows($result) > 0) {
    $usaha = mysqli_fetch_assoc($result);
} else {
    header("Location: ../user/all_kuliner.php");
    exit();
}

// Ambil produk dari usaha ini
$query_produk = "SELECT * FROM produk WHERE id_usaha = $id_usaha";
$result_produk = mysqli_query($koneksi, $query_produk);
$produk = [];
if ($result_produk && mysqli_num_rows($result_produk) > 0) {
    while ($row = mysqli_fetch_assoc($result_produk)) {
        $produk[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Usaha - <?= htmlspecialchars($usaha['nama_usaha']) ?></title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/detail.css"> 
</head>
<body>
    <div class="hover-zone-top"></div>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="../../assets/images/logo UIN IB.png" alt="Logo" width="40" height="auto">
                </a>
                <div class="navbar-toggle" id="navbarToggle">
                    <i class="bi bi-list"></i>
                </div>
                <div class="navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == '../../index.php' ? 'active' : ''; ?>" href="../../index.php">
                                <i class="bi bi-house-door"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == '../user/maps.php' ? 'active' : ''; ?>" href="../user/maps.php">
                                <i class="bi bi-geo-alt"></i> Map
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == '../user/all_kuliner.php' ? 'active' : ''; ?>" href="../user/all_kuliner.php">
                                <i class="bi bi-shop"></i> Store
                            </a>
                        </li>
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="../admin/login_admin.php">
                                <i class="bi bi-person-circle"></i> Login
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="../admin/login_admin.php" class="btn-login">
                    <i class="bi bi-person-circle"></i>
                </a>
            </div>
        </nav>
    </header>
    <div class="container-main">
        <div class="page-header">
            <h2 class="page-title"><?= htmlspecialchars($usaha['nama_usaha']) ?></h2>
            <a href="../user/all_kuliner.php" class="btn btn-secondary">Kembali</a>
        </div>
        
        <div class="detail-container">
            <div class="detail-card">
                <?php if (!empty($usaha['gambar_tempat_usaha'])) : ?>
                    <img src="../../uploads/<?= htmlspecialchars($usaha['gambar_tempat_usaha']) ?>" 
                         class="detail-image">
                <?php endif; ?>
                <div class="detail-info">
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
            <div class="container-map">
                <div id="map"></div>
                <button id="getDirection" class="btn btn-primary mt-3">Get Direction</button>
            </div>
        </div>
        <div class="products-container">
            <h3 class="section-title">Produk</h3>
            <?php if (!empty($produk)) : ?>
                <div class="products-grid">
                    <?php foreach ($produk as $item) : ?>
                        <div class="product-card">
                            <?php if (!empty($item['gambar_produk'])) : ?>
                                <img src="../../uploads/<?= htmlspecialchars($item['gambar_produk']) ?>" 
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
                </div>
            <?php else : ?>
                <div class="no-products">
                    Belum ada produk untuk usaha ini.
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div id="imageModal" class="modal-image" style="display:none;">
        <span class="close-image" onclick="closeModal()">&times;</span>
        <img class="modal-content-image" id="imgFull">
    </div>
</body>
<script>
    // Ambil semua gambar yang ingin diberi modal
    const allImages = document.querySelectorAll('.detail-image, .product-image');

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

    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('navbarToggle');
        const navMenu = document.getElementById('navbarNav');

        toggle.addEventListener('click', function () {
            navMenu.classList.toggle('show');
        });
    });

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

    const navbar = document.querySelector(".navbar");
        let lastScroll = 0;

        window.addEventListener("scroll", () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > lastScroll && currentScroll > 100) {
            // Scroll down
            navbar.classList.add("hide");
            } else {
            // Scroll up
            navbar.classList.remove("hide");
            }

            lastScroll = currentScroll;
        });

        // Deteksi mouse posisi: jika mouse dekat atas, munculkan navbar
        document.addEventListener("mousemove", function (e) {
            if (e.clientY < 80) {
            navbar.classList.remove("hide");
            }
        });
</script>
</html>