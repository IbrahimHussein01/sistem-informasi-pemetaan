<?php
require('../../config/koneksi.php');

// Query untuk mengambil data jenis_produk
$query_jenis_produk = "SELECT jenis_produk, COUNT(*) as total FROM produk GROUP BY jenis_produk";
$result_jenis_produk = mysqli_query($koneksi, $query_jenis_produk);
$data_jenis_produk = [];
while ($row = mysqli_fetch_assoc($result_jenis_produk)) {
    $data_jenis_produk[] = $row;
}

// Query untuk mengambil data kabupaten
$query_kabupaten = "SELECT kabupaten, COUNT(*) as total FROM usaha GROUP BY kabupaten";
$result_kabupaten = mysqli_query($koneksi, $query_kabupaten);
$data_kabupaten = [];
while ($row = mysqli_fetch_assoc($result_kabupaten)) {
    $data_kabupaten[] = $row;
}

// Konversi data ke JSON
$data_jenis_produk_json = json_encode($data_jenis_produk);
$data_kabupaten_json = json_encode($data_kabupaten);

$get1 = mysqli_query($koneksi, "SELECT * FROM usaha");
$count1 = mysqli_num_rows($get1);

$get2 = mysqli_query($koneksi, "SELECT * FROM produk");
$count2 = mysqli_num_rows($get2);

$get3 = mysqli_query($koneksi, "SELECT COUNT(DISTINCT nomor_sertifikat) AS total_sertifikat FROM produk");
$count3 = mysqli_fetch_assoc($get3);
$total_sertifikat = $count3['total_sertifikat'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Pemetaan Sertifikasi Halal</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/leaflet.heat/dist/leaflet-heat.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
    <link rel="stylesheet" href="../../assets/css/map.css">
</head>
<body>
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
    <section id="peta">
        <h3 class="mt-3 mb-1">Telusuri Sertifikat</h3>
        <div class="d-flex justify-content-between mb-2 ">
            <div class="search-container">
                <input type="text" id="search" class="form-control form-control-sm" placeholder="Masukkan nama usaha...">
            </div>
            <!-- <div class="dropdown">
                <select id="kabupatenFilter" class="dropdown-select">
                    <option value="" disabled selected>---Pilih Kabupaten/Kota---</option>
                    <option value="all">Semua Kabupaten/Kota</option>
                    <option value="kabupaten_agam">Kabupaten Agam</option>
                    <option value="kabupaten_dhamasraya">Kabupaten Dhamasraya</option>
                    <option value="kabupaten_kepulauan_mentawai">Kabupaten Kepulauan Mentawai</option>
                    <option value="kabupaten_lima_puluh_kota">Kabupaten Lima Puluh Kota</option>
                    <option value="kabupaten_padang_pariaman">Kabupaten Padang Pariaman</option>
                    <option value="kabupaten_pasaman">Kabupaten Pasaman</option>
                    <option value="kabupaten_pasaman_barat">Kabupaten Pasaman Barat</option>
                    <option value="kabupaten_pesisir_selatan">Kabupaten Pesisir Selatan</option>
                    <option value="kabupaten_sijunjung">Kabupaten Sijunjung</option>
                    <option value="kabupaten_solok">Kabupaten Solok</option>
                    <option value="kabupaten_solok_selatan">Kabupaten Solok Selatan</option>
                    <option value="kabupaten_tanah_datar">Kabupaten Tanah Datar</option>
                    <option value="kota_bukittinggi">Kota Bukittinggi</option>
                    <option value="kota_padang">Kota Padang</option>
                    <option value="kota_padang_panjang">Kota Padang Panjang</option>
                    <option value="kota_pariaman">Kota Pariaman</option>
                    <option value="kota_payakumbuh">Kota Payakumbuh</option>
                    <option value="kota_sawah_lunto">Kota Sawah Lunto</option>
                    <option value="kota_solok">Kota Solok</option>
                </select>
            </div> -->
            <div class="dropdown d-flex gap-2">
                <!-- Dropdown Provinsi -->
                <select id="provinsiFilter" class="dropdown-select">
                    <option value="" disabled selected>---Pilih Provinsi---</option>
                    <option value="all">Semua Provinsi</option>
                </select>

                <!-- Dropdown Kabupaten/Kota -->
                <select id="kabupatenFilter" class="dropdown-select" disabled>
                    <option value="" disabled selected>---Pilih Kabupaten/Kota---</option>
                    <option value="all">Semua Kabupaten/Kota</option>
                </select>
            </div>
        </div>
        <div id="map"></div>
    </section>
</body>

<script>
    // atur toggle
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('navbarToggle');
        const navMenu = document.getElementById('navbarNav');

        toggle.addEventListener('click', function () {
            navMenu.classList.toggle('show');
        });
    });

    // Fungsi slugify -> samain dengan database
    function slugify(text) {
        text = text.toString().toLowerCase().trim().replace(/\s+/g, "_");
        if (text.startsWith("kabupaten_") || text.startsWith("kota_")) {
            return text;
        }
        if (text.includes("kabupaten")) return "kabupaten_" + text.replace("kabupaten_", "").replace("kabupaten", "").trim();
        if (text.includes("kota")) return "kota_" + text.replace("kota_", "").replace("kota", "").trim();
        return text;
    }

    // Inisialisasi Peta
    var map = L.map('map').setView([-0.9272612, 100.3679965], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var customIcon = L.icon({
        iconUrl: '../../assets/images/Ha.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    var heatmapPoints = [];
    let dataUsaha = [];
    var clusterGroups = {};
    var heatmapLayer = null;
    var provinsiData = {};

    // 🔹 Ambil data provinsi/kabupaten dari JSON
    fetch('../../action/indonesia.json')
        .then(res => res.json())
        .then(json => {
            provinsiData = json;
            const provinsiSelect = document.getElementById("provinsiFilter");
            Object.keys(provinsiData).forEach(prov => {
                const opt = document.createElement("option");
                opt.value = prov;
                opt.textContent = prov;
                provinsiSelect.appendChild(opt);
            });
        });

    // 🔹 Ambil data usaha (marker)
    fetch('../../action/data_usaha.php')
        .then(response => response.json())
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                console.warn('Data kosong atau tidak valid!');
                return;
            }
            dataUsaha = data;

            // Inisialisasi cluster per kabupaten
            dataUsaha.forEach(item => {
                if (!clusterGroups[item.kabupaten]) {
                    clusterGroups[item.kabupaten] = L.markerClusterGroup();
                }
            });

            // Tambahkan marker
            dataUsaha.forEach(item => {
                if (item.latitude && item.longitude) {
                    var marker = L.marker([parseFloat(item.latitude), parseFloat(item.longitude)], { icon: customIcon })
                        .bindPopup(`
                            <div class="custom-popup">
                                <div class="image-container">
                                    ${item.gambar_tempat_usaha ? `<img src="../../uploads/${item.gambar_tempat_usaha}" class="popup-image">` : ''}
                                </div>
                                <b>${item.nama_usaha}</b>
                                <span>Latitude: ${item.latitude}</span>
                                <span>Longitude: ${item.longitude}</span>
                                <a href="#" class="direction-link" data-lat="${item.latitude}" data-lng="${item.longitude}">
                                    <div class="direction-icon">
                                        <i class="bi bi-sign-turn-slight-right" style="font-size: 20px;"></i>
                                    </div>
                                </a>
                                <a href="../user/detail.php?id=${item.id_usaha}" class="detail-link">Lihat Detail ></a>
                            </div>
                        `, { maxWidth: 200, minWidth: 150 })
                        .bindTooltip(item.nama_usaha, { permanent: false, direction: "right" });

                    clusterGroups[item.kabupaten].addLayer(marker);
                    heatmapPoints.push([parseFloat(item.latitude), parseFloat(item.longitude), 1]);
                }
            });

            Object.values(clusterGroups).forEach(group => map.addLayer(group));

            if (heatmapPoints.length > 0) {
                heatmapLayer = L.heatLayer(heatmapPoints, { radius: 25, blur: 15, maxZoom: 17 }).addTo(map);
            }
        })
        .catch(error => console.error('Gagal mengambil data:', error));

    // 🔹 Event pilih Provinsi
    document.getElementById("provinsiFilter").addEventListener("change", function () {
        const selectedProv = this.value;
        const kabupatenSelect = document.getElementById("kabupatenFilter");

        kabupatenSelect.innerHTML = `
            <option value="" disabled selected>---Pilih Kabupaten/Kota---</option>
            <option value="all">Semua Kabupaten/Kota</option>`;
        kabupatenSelect.disabled = false;

        if (selectedProv === "all") {
            updateMarkers("all");
            return;
        }

        // Isi dropdown kabupaten sesuai provinsi
        Object.keys(provinsiData[selectedProv]).forEach(kab => {
            const opt = document.createElement("option");
            opt.value = kab.toLowerCase().replace(/\s+/g, "_");
            opt.textContent = kab;
            kabupatenSelect.appendChild(opt);
        });

        // 🔹 Tampilkan semua marker dalam provinsi & zoom
        updateMarkersByProvinsi(selectedProv);
    });

    // 🔹 Event pilih Kabupaten
    document.getElementById("kabupatenFilter").addEventListener("change", function () {
        const selectedKabupaten = this.value;
        const selectedProvinsi = document.getElementById("provinsiFilter").value;

        if (selectedKabupaten === "all" && selectedProvinsi !== "all") {
            // tampilkan hanya marker kabupaten di provinsi yg dipilih
            updateMarkersByProvinsi(selectedProvinsi);
        } else {
            updateMarkers(selectedKabupaten);
        }
    });

    // 🔹 Update marker kabupaten
    function updateMarkers(selectedKabupaten) {
        Object.values(clusterGroups).forEach(g => map.removeLayer(g));

        if (selectedKabupaten === "all") {
            // Semua kabupaten dari semua provinsi
            Object.values(clusterGroups).forEach(g => map.addLayer(g));
            zoomToAllMarkers();
        } else if (clusterGroups[selectedKabupaten]) {
            map.addLayer(clusterGroups[selectedKabupaten]);
            zoomToCluster(clusterGroups[selectedKabupaten]);
        } else {
            map.setView([-0.9272612, 100.3679965], 12);
        }
    }

    // 🔹 Update marker provinsi (hanya kabupaten yg masuk provinsi itu)
    function updateMarkersByProvinsi(provinsiName) {
        Object.values(clusterGroups).forEach(g => map.removeLayer(g));

        let kabupatenList = Object.keys(provinsiData[provinsiName])
            .map(kab => slugify(kab));

        let markers = [];
        kabupatenList.forEach(kabKey => {
            if (clusterGroups[kabKey]) {
                map.addLayer(clusterGroups[kabKey]);
                markers = markers.concat(clusterGroups[kabKey].getLayers());
            }
        });

        if (markers.length > 0) {
            let group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.3));
        } else {
            map.setView([-0.9272612, 100.3679965], 12);
        }
    }


    // 🔹 Helper: zoom ke cluster
    function zoomToCluster(cluster) {
        const markers = cluster.getLayers();
        if (markers.length > 0) {
            var group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.3));
        }
    }

    // 🔹 Helper: zoom ke semua marker
    function zoomToAllMarkers() {
        let allMarkers = Object.values(clusterGroups).flatMap(group => group.getLayers());
        if (allMarkers.length > 0) {
            var group = L.featureGroup(allMarkers);
            map.fitBounds(group.getBounds().pad(0.3));
        }
    }

    // Event listener untuk search bar
    document.getElementById('search').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        let foundMarker = null;

        // Cari marker yang sesuai dengan nama usaha
        Object.values(clusterGroups).forEach(group => {
            group.getLayers().forEach(marker => {
                const markerName = marker.getPopup().getContent().toLowerCase();
                if (markerName.includes(searchTerm)) {
                    foundMarker = marker;
                }
            });
        });

        // Jika marker ditemukan, arahkan peta ke marker tersebut
        if (foundMarker) {
            map.setView(foundMarker.getLatLng(), 15);
            foundMarker.openPopup();
        }
    });

    // Tambahkan event listener untuk mengarahkan ke Google Maps
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
    </script>
</html>