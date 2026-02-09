<?php
require('../../config/koneksi.php');
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
    exit();
}
$id_admin = isset($_GET['id_admin']) ? intval($_GET['id_admin']) : 0;

$admin = [
    'id_admin'=> '',
    'nama' => '',
    'email' => '',
    'password' => '',
    'profile' => '',
];

if ($id_admin > 0) {
    $query = "SELECT * FROM admin WHERE id_admin = $id_admin";
    $result = mysqli_query($koneksi, $query);
    if ($result && mysqli_num_rows($result)) {
        $admin = mysqli_fetch_assoc($result);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password_lama_input = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Ambil password lama dari database
    $query_password = "SELECT password FROM admin WHERE id_admin = $id_admin";
    $result_password = mysqli_query($koneksi, $query_password);
    $data_password = mysqli_fetch_assoc($result_password);
    $password_tersimpan = $data_password['password'];

    // Verifikasi password lama
    if (!password_verify($password_lama_input, $password_tersimpan)) {
        echo "<script>alert('Password lama salah!');window.location='../admin/akun.php';</script>";
        exit;
    }

    // Jika password baru diisi, pastikan konfirmasi cocok, lalu hash
    if (!empty($password_baru)) {
        if ($password_baru !== $konfirmasi_password) {
            echo "<script>alert('Konfirmasi password baru tidak cocok!');window.location='../admin/akun.php';</script>";
            exit;
        }
        $password = password_hash($password_baru, PASSWORD_DEFAULT);
    } else {
        // Tidak ubah password, tetap pakai password lama
        $password = $password_tersimpan;
    }
    // Handle upload profile baru jika ada
    if (!empty($_FILES['profile']['name'])) {
        $profile = $_FILES['profile']['name'];
        $tmp_name = $_FILES['profile']['tmp_name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile);
        move_uploaded_file($tmp_name, $target_file);
    } else {
        // Gunakan gambar profile lama
        $profile = $admin['profile'];
    }    
    // Query update
    $query = "UPDATE admin SET 
              nama = '$nama', 
              email = '$email', 
              password = '$password', 
              profile = '$profile' 
              WHERE id_admin = $id_admin";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['nama'] = $nama;
        $_SESSION['email'] = $email;
        $_SESSION['profile'] = $profile;

        echo "<script>alert('Data berhasil diupdate!'); window.location='../admin/akun.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data. Silakan coba lagi.');window.location='../admin/perbarui_akun.php';</script>";
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
    <link rel="stylesheet" href="../../assets/css/perbarui_akun.css">
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
            <h4 class="mb-3">Edit Akun</h4>
        </div>
        <div class="container-left">
            <form method="POST" action="" enctype="multipart/form-data">
                <!-- Baris 1 -->
                <div class="form-row">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= $admin['nama'] ?>" required>
                </div>

                <!-- Baris 2 -->
                <div class="form-row">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="<?= $admin['email'] ?>" required>
                </div>

                <!-- Baris Password Lama -->
                <div class="form-row">
                    <label for="password_lama" class="form-label">Password Lama</label>
                    <input type="password" class="form-control" id="password_lama" name="password_lama" required>
                </div>

                <!-- Baris Password Baru -->
                <div class="form-row">
                    <label for="password_baru" class="form-label">Password Baru</label>
                    <input type="password" class="form-control" id="password_baru" name="password_baru">
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="form-row">
                    <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password">
                </div>

                <div class="form-row">
                    <label for="profile" class="form-label">Gambar Profile</label>
                    <input type="file" class="form-control" id="profile" name="profile" accept="image/*">
                </div>

                <!-- Tombol Simpan dan Kembali -->
                <div class="form-row" style="justify-content: center;">
                    <button type="submit" class="btn btn-custom">Simpan</button>
                    <a href="../admin/akun.php" class="btn btn-back">Kembali</a>
                </div>
            </form>
        </div>
    </div>
 
    <div class="parent-container-image">
        <div class="container-up-image">
            <h4 class="mb-3">Preview Gambar Profile</h4>
        </div>
        <div class="container-preview">
            <img id="preview" src="<?= !empty($admin['profile']) ? '../../uploads/' . $admin['profile'] : '#' ?>" 
            alt="Gambar Profile" 
            style="max-width: 50%; display: <?= !empty($admin['profile']) ? 'block' : 'none' ?>;">
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>   

    //profile
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

        document.getElementById('profile').addEventListener('change', previewImage);
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