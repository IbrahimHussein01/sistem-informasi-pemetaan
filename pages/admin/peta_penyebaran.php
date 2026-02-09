<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
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
    <link rel="stylesheet" href="../../assets/css/peta_penyebaran.css">
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
        <div class="parent-container">
            <div class="container-up">
                <h4 class="mb-3">Peta Penyebaran</h4>
            </div>
            <div class="container-right">
                <div class="d-flex justify-content-between mb-2">
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
            </div>
        </div>
    </div>
</body>
<script>
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

    // Fungsi untuk memperbarui tanggal dan waktu
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
</html>
