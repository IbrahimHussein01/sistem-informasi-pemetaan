<?php
require('../../config/koneksi.php');
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
    exit();
}
if (isset($_GET['delete_id'])) {
    $id_usaha = intval($_GET['delete_id']);
    $query = "DELETE FROM usaha WHERE id_usaha = $id_usaha";
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='../admin/data_lokasi.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data. Silakan coba lagi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usaha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/data_usaha.css">
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
    <br>
    <div class="parent-container">
        <div class="container-up">
            <h4 class="mb-3">Data Usaha</h4>
        </div>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 control-bar">
                <div class="show-entries">
                    <label style="font-size: 14px;">Show 
                        <select id="entries" class="form-select form-select-sm d-inline-block" style="width: auto;">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">Semua</option>
                        </select> entries
                    </label>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="text" id="search" class="form-control form-control-sm" placeholder="Search...">
                    <a class="btn btn-tambah" href="../admin/tambah_usaha.php">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Usaha</th>
                            <th>Pemilik</th>
                            <th>Alamat</th>
                            <th>Kabupaten/Kota</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $data = mysqli_query($koneksi, "SELECT * FROM usaha");
                        while ($d = mysqli_fetch_array($data)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $d['nama_usaha']; ?></td>
                            <td><?php echo $d['pemilik']; ?></td>
                            <td><?php echo $d['alamat']; ?></td>
                            <td><?php echo $d['kabupaten']; ?></td>
                            <td><?php echo $d['latitude']; ?></td>
                            <td><?php echo $d['longitude']; ?></td>
                            <td class="text-center">
                                <div class="btn-group gap-1">
                                    <a href="../admin/detail_usaha.php?id=<?= $d['id_usaha'] ?>" class="btn btn-info btn-xs" title="Detail">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="../admin/edit_usaha.php?id=<?= $d['id_usaha'] ?>" class="btn btn-warning btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="../admin/data_lokasi.php?delete_id=<?= $d['id_usaha'] ?>" class="btn btn-danger btn-xs" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    // pencarian
    document.addEventListener("DOMContentLoaded", function() {
        const entriesSelect = document.getElementById("entries");
        const searchInput = document.getElementById("search");
        const tableBody = document.querySelector("#dataTable tbody");

        entriesSelect.addEventListener("change", function () {
            const value = this.value;
            const rows = tableBody.querySelectorAll("tr");

            if (value === "all") {
                rows.forEach(row => row.style.display = "");
            } else {
                const limit = parseInt(value);
                rows.forEach((row, index) => {
                    row.style.display = index < limit ? "" : "none";
                });
            }
        });

        searchInput.addEventListener("keyup", function() {
            const filter = this.value.toLowerCase();
            const rows = tableBody.querySelectorAll("tr");
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
        
        entriesSelect.dispatchEvent(new Event('change'));

    });

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

    document.getElementById("sidebarToggle").addEventListener("click", function() {
        const main2 = document.getElementById("main2");
        // Force reflow
        main2.style.display = 'none';
        main2.offsetHeight; // Trigger reflow
        main2.style.display = 'block';
        
        // Alternative: Programatically set width
        const container = document.querySelector(".container");
        container.style.width = main2.offsetWidth + "px";
    });
</script>
</html>