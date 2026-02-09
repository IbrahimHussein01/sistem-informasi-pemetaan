<?php
require('../../config/koneksi.php');
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
    exit();
}
// Ambil data admin dari session
$id_admin = $_SESSION['id_admin']; // pastikan kamu menyimpan id_admin di session saat login

// Ambil data admin dari database
$query = "SELECT * FROM admin WHERE id_admin = $id_admin";
$result = mysqli_query($koneksi, $query);

// Cek apakah data ditemukan
if ($result && mysqli_num_rows($result) > 0) {
    $admin = mysqli_fetch_assoc($result);
} else {
    echo "<script>alert('Data admin tidak ditemukan'); window.location='../admin/akun.php';</script>";
    exit();
}
include '../../includes/sidebar_admin.php';
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
    <link rel="stylesheet" href="../../assets/css/profile.css">
    <link rel="stylesheet" href="../assets/css/side.css?v=<?=time()?>"> <!-- untuk hindari cache -->
</head>
<body>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const entriesSelect = document.getElementById("entries");
        const searchInput = document.getElementById("search");
        const tableBody = document.querySelector("#dataTable tbody");

        entriesSelect.addEventListener("change", function() {
            const limit = parseInt(this.value);
            const rows = tableBody.querySelectorAll("tr");
            rows.forEach((row, index) => {
                row.style.display = index < limit ? "" : "none";
            });
        });

        searchInput.addEventListener("keyup", function() {
            const filter = this.value.toLowerCase();
            const rows = tableBody.querySelectorAll("tr");
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
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

</script>
</html>
