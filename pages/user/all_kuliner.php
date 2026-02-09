<?php
require('../../config/koneksi.php');

$query = "SELECT 
            u.id_usaha, 
            u.nama_usaha, 
            u.gambar_tempat_usaha,
            u.latitude,    
            u.longitude,
            u.kabupaten as kabupaten_value,
            COUNT(p.id_produk) as jumlah_produk,
            GROUP_CONCAT(DISTINCT p.nomor_sertifikat SEPARATOR ', ') as sertifikat
          FROM usaha u
          LEFT JOIN produk p ON u.id_usaha = p.id_usaha
          GROUP BY u.id_usaha
          ORDER BY u.nama_usaha";
$result = mysqli_query($koneksi, $query);
function formatKabupaten($kabValue) {
    // 1. Ubah underscore ke spasi + kapitalisasi
    $formatted = ucwords(str_replace('_', ' ', $kabValue));
    
    // 2. Deteksi apakah ini Kota (berdasarkan pola nama)
    $isKota = false;
    $kotaKeywords = ['padang', 'bukittinggi', 'payakumbuh', 'solok', 'pariaman', 'sawah_lunto', 'padang_panjang']; // Kata kunci nama kota
    
    foreach ($kotaKeywords as $keyword) {
        if (strpos($kabValue, $keyword) !== false) {
            $isKota = true;
            break;
        }
    }
    
    return $isKota ? "Kota $formatted" : "Kabupaten $formatted";
}

// Proses data
$usaha = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $row['kabupaten_display'] = formatKabupaten($row['kabupaten_value']);
        $usaha[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telusuri Kuliner Halal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/store.css">
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

    <div class="container-search">
        <h3 class="mt-3 mb-3">Telusuri Sertifikat Halal</h3>
        <div class="row g-2 gx-0 align-items-center mb-3">
            <div class="col-12 col-md-6">
                <div class="search-box">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Masukkan nama usaha...">
                        <button class="btn btn-sm" type="button" id="searchButton" style="background-color:#ddd">Search</button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="row g-2">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <select class="form-control form-control-sm" id="provinsiSelect">
                                <option value="" selected>Pilih Provinsi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <select class="form-control form-control-sm" id="kabupatenSelect" disabled>
                                <option value="" selected>Pilih Kabupaten</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Daftar Usaha</h4>
            <div class="d-flex align-items-center">
                <label for="showCount" class="me-2 mb-0 small">Show:</label>
                <select id="showCount" class="form-select form-select-sm" style="width: auto;">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="100">100</option>
                    <option value="all">Semua</option>
                </select>
            </div>
        </div>

        <div class="row" id="usahaContainer">
            <?php if (!empty($usaha)) : ?>
                <?php foreach ($usaha as $item) : ?>
                    <div class="col-md-3 mb-4 usaha-item" data-kabupaten="<?= htmlspecialchars($item['kabupaten_display']) ?>" 
                    data-kabupaten-value="<?= htmlspecialchars($item['kabupaten_value']) ?>">
                        <div class="card h-100">
                            <div class="card-body p-0 d-flex flex-column">
                                <?php if (!empty($item['gambar_tempat_usaha'])) : ?>
                                    <img src="../../uploads/<?= htmlspecialchars($item['gambar_tempat_usaha']) ?>" 
                                        class="card-img-top w-100" style="height: 180px; object-fit: cover; border-radius: 8px 8px 0 0;">
                                <?php endif; ?>
                                
                                <div class="p-3 flex-grow-1">
                                    <h5 class="card-title mb-2" style="font-size: 1.1rem; font-weight: 600;"><?= htmlspecialchars($item['nama_usaha']) ?></h5>
                                    
                                    <div class="mb-2">
                                        <p class="card-text mb-1 small text-muted"><i class="fas fa-box-open me-1"></i> <?= $item['jumlah_produk'] ?> produk</p>
                                        
                                        <div class="mt-2">
                                            <p class="small text-muted mb-1"><i class="fas fa-certificate me-1"></i> Sertifikat Halal:</p>
                                            <div class="sertifikat-list small">
                                                <?php 
                                                    if (!empty($item['sertifikat'])) {
                                                        $sertifikat_list = explode(', ', $item['sertifikat']);
                                                        foreach ($sertifikat_list as $sertifikat) {
                                                            echo '<div class="mb-1">';
                                                            echo '<a href="https://bpjph.halal.go.id/search/sertifikat?nama_produk=&nama_pelaku_usaha=&no_sertifikat=' . urlencode($sertifikat) . '&page=1" target="_blank">'
                                                                . htmlspecialchars($sertifikat) .
                                                                '</a>';
                                                            echo '</div>';
                                                        }
                                                    } else {
                                                        echo '<span class="text-muted">Belum ada sertifikat</span>';
                                                    }
                                                ?>    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2">
                                <a href="../user/detail.php?id=<?= $item['id_usaha'] ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                <a href="#" class="direction-link" data-lat="<?= $item['latitude'] ?>" data-lng="<?= $item['longitude'] ?>" title="Arahkan ke Lokasi">
                                    <div class="direction-icon">
                                        <i class="fas fa-directions" style="font-size: 18px;"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-12">
                    <div class="alert alert-info">Tidak ada usaha kuliner yang tersedia.</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('navbarToggle');
        const navMenu = document.getElementById('navbarNav');

        toggle.addEventListener('click', function () {
            navMenu.classList.toggle('show');
        });
    });

    $(document).ready(function () {
        let indonesiaData = {};

        const showCount = document.getElementById("showCount");
        const searchInput = document.getElementById("searchInput");
        const searchButton = document.getElementById("searchButton");
        const kabupatenSelect = document.getElementById("kabupatenSelect");
        const provinsiSelect = document.getElementById("provinsiSelect");
        const usahaContainer = document.getElementById("usahaContainer");
        const allUsahaItems = usahaContainer.querySelectorAll(".usaha-item");

        // Fungsi bikin slug sesuai database (contoh: "Kabupaten Agam" -> "agam")
        function slugify(text) {
            text = text.toString().toLowerCase().trim().replace(/\s+/g, "_");
            if (text.startsWith("kabupaten_") || text.startsWith("kota_")) {
                return text; // biarkan sama seperti database
            }
            // tambahkan prefix sesuai kata di nama
            if (text.includes("kabupaten")) return "kabupaten_" + text.replace("kabupaten_", "");
            if (text.includes("kota")) return "kota_" + text.replace("kota_", "");
            return text;
        }

        // Load data dari JSON
        $.getJSON("../../action/indonesia.json", function (data) {
            indonesiaData = data;

            // Isi dropdown provinsi
            Object.keys(indonesiaData).forEach(function (prov) {
                $(provinsiSelect).append(
                    `<option value="${slugify(prov)}">${prov}</option>`
                );
            });

            // Event change provinsi
            provinsiSelect.addEventListener("change", function () {
                const selectedProvSlug = this.value;
                const selectedProvName = Object.keys(indonesiaData).find(
                    prov => slugify(prov) === selectedProvSlug
                );

                kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten</option>';

                if (selectedProvName && indonesiaData[selectedProvName]) {
                    Object.keys(indonesiaData[selectedProvName]).forEach(function (kab) {
                        const option = document.createElement("option");
                        option.value = slugify(kab); // value sesuai database
                        option.textContent = kab; // tampil di dropdown
                        kabupatenSelect.appendChild(option);
                    });
                    kabupatenSelect.disabled = false;
                } else {
                    kabupatenSelect.disabled = true;
                }

                refreshDisplay();
            });
        }).fail(function() {
            console.error("Gagal load indonesia.json. Cek path atau server.");
        });

        // Fungsi refresh display
        function refreshDisplay() {
            const searchTerm = searchInput.value.toLowerCase();
            const kabupatenValue = kabupatenSelect.value;
            const countValue = showCount.value;
            let visibleCount = 0;

            allUsahaItems.forEach((item) => {
                const cardText = item.querySelector(".card-title").textContent.toLowerCase();
                const itemKabValue = item.dataset.kabupatenValue;

                const searchMatch = searchTerm === '' || cardText.includes(searchTerm);
                const kabMatch = kabupatenValue === '' || kabupatenValue === itemKabValue;
                const shouldShow = searchMatch && kabMatch;

                if (shouldShow) {
                    if (countValue === 'all' || visibleCount < parseInt(countValue)) {
                        item.style.display = "";
                        visibleCount++;
                    } else {
                        item.style.display = "none";
                    }
                } else {
                    item.style.display = "none";
                }
            });

            updateNoResultsMessage();
        }

        // Event listener untuk input search
        searchInput.addEventListener("input", function() {
            // Jika input dikosongkan, langsung refresh
            if (this.value === '') {
                refreshDisplay();
            }
        });

        // Event listener untuk tombol search (tetap ada)
        searchButton.addEventListener("click", refreshDisplay);

        // Event untuk provinsi dan kabupaten (Vanilla JS)
        provinsiSelect.addEventListener("change", function() {
            const provinsi = this.value;
            kabupatenSelect.innerHTML = '<option value="" selected>Pilih Kabupaten</option>';
            
            if (provinsi && kabupatenData[provinsi]) {
                kabupatenData[provinsi].forEach(function(kab) {
                    const option = document.createElement("option");
                    option.value = kab.value;
                    option.textContent = kab.display;
                    kabupatenSelect.appendChild(option);
                });
                kabupatenSelect.disabled = false;
            } else {
                kabupatenSelect.disabled = true;
            }
            
            refreshDisplay();
        });

        // Event listeners
        showCount.addEventListener("change", refreshDisplay);
        kabupatenSelect.addEventListener("change", refreshDisplay);

        // Biarkan hanya ini untuk pencarian:
        searchButton.addEventListener("click", function() {
            refreshDisplay();
        });
        
        // Opsional: Tambahkan ini jika ingin pencarian juga bekerja dengan tombol Enter
        searchInput.addEventListener("keypress", function(e) {
            if (e.key === 'Enter') {
                refreshDisplay();
            }
        });

        // Fungsi pesan tidak ada hasil yang lebih baik
        function updateNoResultsMessage() {
            const visibleItems = Array.from(allUsahaItems).filter(item => 
                item.style.display !== "none"
            );
            
            const noResultsMsg = document.getElementById("noResultsMessage");
            
            if (visibleItems.length === 0) {
                if (!noResultsMsg) {
                    const messageDiv = document.createElement("div");
                    messageDiv.className = "col-12";
                    messageDiv.id = "noResultsMessage";
                    messageDiv.innerHTML = `
                        <div class="alert alert-info">
                            Tidak ada usaha yang sesuai dengan kriteria pencarian.
                        </div>
                    `;
                    usahaContainer.appendChild(messageDiv);
                }
            } else if (noResultsMsg) {
                noResultsMsg.remove();
            }
        }

        // Inisialisasi awal
        refreshDisplay();
    });

    document.addEventListener('click', function(event) {
        const link = event.target.closest('.direction-link');
        if (!link) return;

        event.preventDefault();
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var userLat = position.coords.latitude;
                var userLng = position.coords.longitude;
                var destinationLat = link.getAttribute('data-lat');
                var destinationLng = link.getAttribute('data-lng');

                var url = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${destinationLat},${destinationLng}&travelmode=driving`;
                window.open(url, '_blank');
            }, function(error) {
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