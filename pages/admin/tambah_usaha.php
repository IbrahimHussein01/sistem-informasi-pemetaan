<?php
require('../../config/koneksi.php');
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_usaha = $_POST['nama_usaha'];
    $pemilik = $_POST['pemilik'];
    $alamat = $_POST['alamat'];
    $provinsi = $_POST['provinsi'];
    $kabupaten = $_POST['kabupaten'];
    $kecamatan = $_POST['kecamatan'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $gambar_tempat_usaha = $_FILES['gambar_tempat_usaha']['name'];
    $tmp_name = $_FILES['gambar_tempat_usaha']['tmp_name'];
    $target_dir = "../../uploads/";
    $target_file = $target_dir . basename($gambar_tempat_usaha);

    // Cek apakah ada file yang diupload
    if (!empty($gambar_tempat_usaha)) {
        // Pastikan folder "uploads" ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Pindahkan file ke folder "uploads"
        if (move_uploaded_file($tmp_name, $target_file)) {
            echo "File berhasil diupload.";
        } else {
            echo "Gagal mengupload file.";
        }
    }

    $query = "INSERT INTO usaha (nama_usaha, pemilik, alamat,  provinsi, kabupaten, kecamatan,  latitude, longitude, gambar_tempat_usaha) 
              VALUES ('$nama_usaha', '$pemilik', '$alamat', '$provinsi', '$kabupaten', '$kecamatan', '$latitude', '$longitude', '$gambar_tempat_usaha')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='../admin/data_lokasi.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data. Silakan coba lagi.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usaha</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/tambah_usaha.css">
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
            <h4 class="mb-3">Tambah Data Usaha</h4>
        </div>
        <div class="container-left">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-row">
                    <label for="nama_usaha" class="form-label">Nama Usaha</label>
                    <input type="text" class="form-control" id="nama_usaha" name="nama_usaha" required>
                </div>
                <div class="form-row">
                    <label for="pemilik" class="form-label">Pemilik</label>
                    <input type="text" class="form-control" id="pemilik" name="pemilik" required>
                </div>
                <div class="form-row">
                    <label for="provinsi" class="form-label">Provinsi</label>
                    <select class="form-control" id="provinsi" name="provinsi" required>
                        <option value="" disabled selected>---Pilih Provinsi---</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="kabupaten" class="form-label">Kabupaten/Kota</label>
                    <select class="form-control" id="kabupaten" name="kabupaten" required>
                        <option value="" disabled selected>---Pilih Kabupaten---</option>
                    </select>
                </div>

                <div class="form-row">
                    <label for="kecamatan" class="form-label">Kecamatan</label>
                    <select class="form-control" id="kecamatan" name="kecamatan" required>
                        <option value="" disabled selected>---Pilih Kecamatan---</option>
                    </select>
                </div>

                <!-- Baris 8 -->
                <div class="form-row">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="text" class="form-control" id="latitude" name="latitude" required>
                </div>

                <!-- Baris 9 -->
                <div class="form-row">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="text" class="form-control" id="longitude" name="longitude" required>
                </div>
                <div style="text-align: center;">
                    <button type="button" class="btn btn-custom" onclick="searchLocation()" >Cari Lokasi</button>
                </div>
                <div class="form-row mt-2">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="alamat" name="alamat" readonly>
                </div>

                <!-- Baris 10 -->
                <div class="form-row">
                    <label for="gambar_tempat_usaha" class="form-label">Gambar Tempat Usaha</label>
                    <input type="file" class="form-control" id="gambar_tempat_usaha" name="gambar_tempat_usaha" accept="image/*" required>
                </div>
                <div class="form-row">
                    <button type="submit" class="btn btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="column">
        <div class="parent-container-map">
            <div class="container-up-map">
                <h4 class="mb-3">Lokasi Usaha</h4>
            </div>
            <div class="container-right">
                <div id="map"></div>
            </div>
        </div>
        <div class="parent-container-image">
            <div class="container-up-image">
                <h4 class="mb-3">Preview Gambar Tempat Usaha</h4>
            </div>
            <div class="container-preview">
                <img id="preview" src="#" alt="Gambar Tempat Usaha">
            </div>
        </div>
    </div>
</div>
<script>
    // peta
    var map = L.map('map').setView([-0.5827529, 100.6133379], 12);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([-0.789275, 113.921327], { draggable: true }).addTo(map);

    function updateLatLng(lat, lng) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
    }

    function searchLocation() {
        var lat = parseFloat(document.getElementById('latitude').value);
        var lng = parseFloat(document.getElementById('longitude').value);

        if (isNaN(lat) || isNaN(lng)) {
            alert('Koordinat tidak valid.');
            return;
        }

        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);

        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then(response => response.json())
            .then(data => {
                if (data.address) {
                    document.getElementById('alamat').value = data.display_name || '';
                }
            })
            .catch(() => {
                alert('Gagal mengambil alamat.');
            });
    }

    marker.on('dragend', function () {
        var lat = marker.getLatLng().lat;
        var lng = marker.getLatLng().lng;
        updateLatLng(lat, lng);
        searchLocation();
    });

    map.on('dblclick', function (event) {
        var lat = event.latlng.lat;
        var lng = event.latlng.lng;
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 15);
        updateLatLng(lat, lng);
        searchLocation();
    });
 
    let indonesiaData = null;

    // Fungsi normalisasi nama jadi slug (biar konsisten ke database)
    function slugify(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, "_")
            .replace(/[^a-z0-9_]/g, "");
    }

    // Load data JSON Indonesia
    fetch("../../action/indonesia.json")
        .then(res => res.json())
        .then(data => {
            indonesiaData = data;
            isiProvinsi();
        });

    // Isi dropdown provinsi
    function isiProvinsi() {
        const provinsiSelect = document.getElementById("provinsi");
        provinsiSelect.innerHTML = '<option value="" disabled selected>---Pilih Provinsi---</option>';

        Object.keys(indonesiaData).forEach(prov => {
            const option = document.createElement("option");
            option.value = slugify(prov);  // yang masuk ke DB slug
            option.textContent = prov;    // user lihat nama asli
            provinsiSelect.appendChild(option);
        });
    }

    // Isi dropdown kabupaten/kota sesuai provinsi
    function isiKabupaten(provSlug) {
        const kabupatenSelect = document.getElementById("kabupaten");
        kabupatenSelect.innerHTML = '<option value="" disabled selected>---Pilih Kabupaten---</option>';

        const provName = Object.keys(indonesiaData).find(p => slugify(p) === provSlug);
        if (!provName) return;

        Object.keys(indonesiaData[provName]).forEach(kab => {
            const option = document.createElement("option");
            option.value = slugify(kab);  // simpan slug
            option.textContent = kab;
            kabupatenSelect.appendChild(option);
        });
    }

    // Isi dropdown kecamatan sesuai kabupaten
    function isiKecamatan(provSlug, kabSlug) {
        const kecamatanSelect = document.getElementById("kecamatan");
        kecamatanSelect.innerHTML = '<option value="" disabled selected>---Pilih Kecamatan---</option>';

        const provName = Object.keys(indonesiaData).find(p => slugify(p) === provSlug);
        if (!provName) return;

        const kabName = Object.keys(indonesiaData[provName]).find(k => slugify(k) === kabSlug);
        if (!kabName) return;

        indonesiaData[provName][kabName].forEach(kec => {
            const option = document.createElement("option");
            option.value = slugify(kec);  // simpan slug
            option.textContent = kec;
            kecamatanSelect.appendChild(option);
        });
    }

    // Event listener (dipasang sekali saja)
    document.getElementById("provinsi").addEventListener("change", function () {
        isiKabupaten(this.value);
    });
    document.getElementById("kabupaten").addEventListener("change", function () {
        isiKecamatan(document.getElementById("provinsi").value, this.value);
    });
    //gambar_tempat_usaha
    function previewImage(event) {
            var input = event.target;
            var reader = new FileReader();
            reader.onload = function () {
                var imgElement = document.getElementById("preview");
                imgElement.src = reader.result;
                imgElement.style.display = "block";
            };
            reader.readAsDataURL(input.files[0]);
        }

        document.getElementById('gambar_tempat_usaha').addEventListener('change', previewImage);
    // logout
    function confirmLogout() {
        if (confirm("Apakah Anda yakin ingin keluar?")) {
            logout();
        }
    }
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