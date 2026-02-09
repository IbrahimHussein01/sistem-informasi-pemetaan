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
    $query = "SELECT * FROM usaha WHERE id_usaha = $id_usaha";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result)) {
        $usaha = mysqli_fetch_assoc($result);
    }
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
    $gambar_tempat_usaha = $usaha['gambar_tempat_usaha'];
    if (!empty($_FILES['gambar_tempat_usaha']['name'])) {
        $gambar_tempat_usaha = $_FILES['gambar_tempat_usaha']['name'];
        $tmp_name = $_FILES['gambar_tempat_usaha']['tmp_name'];
        $target_dir = "../../uploads//";
        $target_file = $target_dir . basename($gambar_tempat_usaha);
        move_uploaded_file($tmp_name, $target_file);
    }

    $query = "UPDATE usaha SET 
              nama_usaha = '$nama_usaha', 
              pemilik = '$pemilik', 
              alamat = '$alamat', 
              provinsi = '$provinsi', 
              kabupaten = '$kabupaten', 
              kecamatan = '$kecamatan', 
              latitude = '$latitude', 
              longitude = '$longitude', 
              gambar_tempat_usaha = '$gambar_tempat_usaha' 
              WHERE id_usaha = $id_usaha";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='../admin/data_lokasi.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data. Silakan coba lagi.');</script>";
    }
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
    <link rel="stylesheet" href="../../assets/css/edit_usaha.css">
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
            <h4 class="mb-3">Edit Data Usaha</h4>
        </div>
        <div class="container-left">
            <form method="POST" action="" enctype="multipart/form-data">
                <!-- Baris 1 -->
                <div class="form-row">
                    <label for="nama_usaha" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="nama_usaha" name="nama_usaha" value="<?= $usaha['nama_usaha'] ?>" required>
                </div>

                <!-- Baris 2 -->
                <div class="form-row">
                    <label for="pemilik" class="form-label">pemilik</label>
                    <input type="text" class="form-control" id="pemilik" name="pemilik" value="<?= $usaha['pemilik'] ?>" required>
                </div>
                
                <!-- Provinsi -->
                <div class="form-row">
                    <label for="provinsi" class="form-label">Provinsi</label>
                    <select class="form-control" id="provinsi" name="provinsi" required>
                        <option value="" disabled selected>---Pilih Provinsi---</option>
                    </select>
                </div>

                <!-- Kabupaten -->
                <div class="form-row">
                    <label for="kabupaten" class="form-label">Kabupaten/Kota</label>
                    <select class="form-control" id="kabupaten" name="kabupaten" required>
                        <option value="" disabled selected>---Pilih Kabupaten---</option>
                    </select>
                </div>

                <!-- Kecamatan -->
                <div class="form-row">
                    <label for="kecamatan" class="form-label">Kecamatan</label>
                    <select class="form-control" id="kecamatan" name="kecamatan" required>
                        <option value="" disabled selected>---Pilih Kecamatan---</option>
                    </select>
                </div>

                <!-- Baris 8 -->
                <div class="form-row">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="text" class="form-control" id="latitude" name="latitude" value="<?= $usaha['latitude'] ?>" required>
                </div>

                <!-- Baris 9 -->
                <div class="form-row">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="text" class="form-control" id="longitude" name="longitude" value="<?= $usaha['longitude'] ?>" required>
                </div>
                <div style="text-align: center; margin-bottom: 10px; width: 100%;">
                    <button type="button" class="btn btn-lokasi" onclick="searchLocation()" >Cari Lokasi</button>
                </div>
                <div class="form-row">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input type="text" class="form-control" id="alamat" name="alamat" value="<?= $usaha['alamat'] ?>" readonly>
                </div>

                <!-- Baris 10 -->
                <div class="form-row">
                    <label for="gambar_tempat_usaha" class="form-label">Tempat Usaha</label>
                    <input type="file" class="form-control" id="gambar_tempat_usaha" name="gambar_tempat_usaha" accept="image/*">
                </div>

                <!-- Tombol Simpan dan Kembali -->
                <div class="form-row" style="justify-content: center;">
                    <button type="submit" class="btn btn-custom">Simpan</button>
                    <a href="../admin/data_lokasi.php" class="btn btn-back">Kembali</a>
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
                <img id="preview" src="<?= !empty($usaha['gambar_tempat_usaha']) ? '../../uploads/' . $usaha['gambar_tempat_usaha'] : '#' ?>" 
                alt="Gambar Produk" 
                style="max-width: 100%; display: <?= !empty($usaha['gambar_tempat_usaha']) ? 'block' : 'none' ?>;">
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

    //gambar_tempat_usaha
    document.getElementById('gambar_tempat_usaha').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.src = e.target.result;
                preview.style.display = 'block'; // Pastikan gambar muncul
            };
            reader.readAsDataURL(file);
        }
    });
 
    let indonesiaData = {}; // data JSON provinsi/kabupaten/kecamatan

    // Data lama dari PHP (sudah dalam format slug, misal "kabupaten_agam")
    const selectedProvinsi  = "<?= $usaha['provinsi'] ?>";
    const selectedKabupaten = "<?= $usaha['kabupaten'] ?>";
    const selectedKecamatan = "<?= $usaha['kecamatan'] ?>";

    // Fungsi bikin slug (biar seragam)
    function slugify(str) {
        return str.toLowerCase().replace(/\s+/g, "_");
    }

    async function loadData() {
        const res = await fetch("../../action/indonesia.json"); // sesuaikan path
        indonesiaData = await res.json();

        isiProvinsi();

        // Auto-select data lama
        if (selectedProvinsi) {
            document.getElementById("provinsi").value = selectedProvinsi;
            isiKabupaten(selectedKabupaten);

            if (selectedKabupaten) {
                document.getElementById("kabupaten").value = selectedKabupaten;
                isiKecamatan(selectedKecamatan);

                if (selectedKecamatan) {
                    document.getElementById("kecamatan").value = selectedKecamatan;
                }
            }
        }
    }

    function isiProvinsi(preselectedProvinsi = null) {
        const provinsiSelect = document.getElementById("provinsi");
        provinsiSelect.innerHTML = '<option value="" disabled selected>---Pilih Provinsi---</option>';
        for (const prov in indonesiaData) {
            const opt = document.createElement("option");
            opt.value = slugify(prov);   // simpan slug ke DB
            opt.textContent = prov;      // tampilkan nama asli
            if (preselectedProvinsi && slugify(prov) === preselectedProvinsi) {
                opt.selected = true;
            }
            provinsiSelect.appendChild(opt);
        }
    }

    function isiKabupaten(preselectedKabupaten = null) {
        const provSlug = document.getElementById("provinsi").value;
        const prov = Object.keys(indonesiaData).find(p => slugify(p) === provSlug);

        const kabSelect = document.getElementById("kabupaten");
        kabSelect.innerHTML = '<option value="" disabled selected>---Pilih Kabupaten---</option>';

        if (prov && indonesiaData[prov]) {
            for (const kab in indonesiaData[prov]) {
                const opt = document.createElement("option");
                opt.value = slugify(kab);  // simpan slug
                opt.textContent = kab;
                if (preselectedKabupaten && slugify(kab) === preselectedKabupaten) {
                    opt.selected = true;
                }
                kabSelect.appendChild(opt);
            }
        }
    }

    function isiKecamatan(preselectedKecamatan = null) {
        const provSlug = document.getElementById("provinsi").value;
        const prov = Object.keys(indonesiaData).find(p => slugify(p) === provSlug);

        const kabSlug = document.getElementById("kabupaten").value;
        const kab = Object.keys(indonesiaData[prov]).find(k => slugify(k) === kabSlug);

        const kecSelect = document.getElementById("kecamatan");
        kecSelect.innerHTML = '<option value="" disabled selected>---Pilih Kecamatan---</option>';

        if (prov && kab && indonesiaData[prov][kab]) {
            indonesiaData[prov][kab].forEach(kec => {
                const opt = document.createElement("option");
                opt.value = slugify(kec);  // simpan slug
                opt.textContent = kec;
                if (preselectedKecamatan && slugify(kec) === preselectedKecamatan) {
                    opt.selected = true;
                }
                kecSelect.appendChild(opt);
            });
        }
    }

    // Event listener
    document.getElementById("provinsi").addEventListener("change", () => isiKabupaten());
    document.getElementById("kabupaten").addEventListener("change", () => isiKecamatan());

    // Jalankan saat load
    window.onload = loadData;

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