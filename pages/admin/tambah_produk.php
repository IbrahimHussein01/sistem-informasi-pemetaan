<?php
require('../../config/koneksi.php');
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_usaha = $_POST['id_usaha']; // Ambil ID usaha dari form
    $nama_produk = $_POST['nama_produk'];
    $produsen = $_POST['produsen'];
    $jenis_produk = $_POST['jenis_produk'];
    $nomor_sertifikat = $_POST['nomor_sertifikat'];
    $tanggal_terbit = $_POST['tanggal_terbit'];

    $gambar_produk = $_FILES['gambar_produk']['name'];
    $tmp_name = $_FILES['gambar_produk']['tmp_name'];
    $target_dir = "../../uploads/";
    $target_file = $target_dir . basename($gambar_produk);

    // Cek apakah ada file yang diupload
    if (!empty($gambar_produk)) {
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

    $query = "INSERT INTO produk (id_usaha, nama_produk, produsen, jenis_produk, nomor_sertifikat, tanggal_terbit, gambar_produk) 
        VALUES ('$id_usaha', '$nama_produk', '$produsen', '$jenis_produk', '$nomor_sertifikat', '$tanggal_terbit', '$gambar_produk')";

    if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Data produk berhasil ditambahkan!'); window.location='../admin/data_produk.php';</script>";
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../../assets/css/tambah_produk.css">
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
            <h4 class="mb-3">Tambah Data Produk</h4>
        </div>
        <div class="container-left">
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-row">
                    <label for="nama_produk" class="form-label">Nama Produk</label>
                    <input type="text" class="form-control" id="nama_produk" name="nama_produk" required>
                </div>
                <div class="form-row">
                    <label for="produsen" class="form-label">Produsen</label>
                    <input type="text" class="form-control" id="produsen" name="produsen" required>
                </div>
                <div class="form-row">
                    <label for="id_usaha" class="form-label">Pilih Usaha</label>
                    <select id="id_usaha" class="form-control select2" name="id_usaha" required>
                        <option value="" disabled selected>---Pilih Usaha---</option>
                        <?php
                        include '../../config/koneksi.php';
                        $query = mysqli_query($koneksi, "SELECT id_usaha, nama_usaha, pemilik FROM usaha");
                        while ($data = mysqli_fetch_array($query)) {
                            echo "<option value='".$data['id_usaha']."'>".$data['nama_usaha']." - ".$data['pemilik']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-row">
                    <label for="jenis_produk" class="form-label">Jenis Produk</label>
                    <select class="form-control" id="jenis_produk" name="jenis_produk" required>
                        <option value="" disabled selected>---Pilih Jenis Produk---</option>
                        <option value="susu dan analognya">Susu dan analognya</option>
                        <option value="lemak, minyak dan emulsi minyak">Lemak, minyak, dan emulsi minyak</option>
                        <option value="es untuk dimakan">Es untuk dimakan</option>
                        <option value="buah dan sayur">Buah dan sayur</option>
                        <option value="kembang gula/permen dan coklat">Kembang gula/permen dan cokelat</option>
                        <option value="serealia dan produk serealia">Serealia dan produk serealia</option>
                        <option value="produk bakeri">Produk bakeri</option>
                        <option value="ikan dan produk perikanan">Ikan dan produk perikanan</option>
                        <option value="telur olahan dan produk-produk telur hasil olahan">Telur olahan dan produk-produk hasil olahan</option>
                        <option value="gula dan pemanis termasuk madu">Gula dan pemanis termasuk madu</option>
                        <option value="garam, rempah, sup, saus, salad, serta produk protein">Garam, rempah, sup, saus, salad, serta produk protein</option>
                        <option value="makanan ringan siap santap">Makanan ringan siap santap</option>
                        <option value="makanan dan minuman dengan pengolahan">Makanan dan minuman dengan pengolahan</option>
                        <option value="minuman dengan pengolahan">Minuman dengan pengolahan</option>
                    </select>
                </div>
                <div class="form-row">
                    <label for="nomor_sertifikat" class="form-label">Nomor Sertifikat</label>
                    <input type="text" class="form-control" id="nomor_sertifikat" name="nomor_sertifikat" required>
                </div>

                <div class="form-row">
                    <label for="tanggal_terbit" class="form-label">Tanggal Terbit</label>
                    <input type="date" class="form-control" id="tanggal_terbit" name="tanggal_terbit" required>
                </div>
                <div class="form-row">
                    <label for="gambar_produk" class="form-label">Gambar Produk</label>
                    <input type="file" class="form-control" id="gambar_produk" name="gambar_produk" accept="image/*" required>
                </div>
                <div class="form-row" style="align-items: center;">
                    <button type="submit" class="btn btn-save">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <div class="parent-container-image">
        <div class="container-up-image">
            <h4 class="mb-3">Preview Gambar Produk</h4>
        </div>
        <div class="container-preview">
            <img id="preview" src="#" alt="Gambar Produk">
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>   
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "---Pilih Usaha---",
            width: '100%',
            minimumResultsForSearch: 3
        });
    });

    //gambar_produk
    function previewImage(event) {
        console.log("Preview image function called");
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function () {
            var imgElement = document.getElementById("preview");
            imgElement.src = reader.result;
            imgElement.style.display = "block";
        };
        reader.readAsDataURL(input.files[0]);
    }

        document.getElementById('gambar_produk').addEventListener('change', previewImage);
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