<?php
require('../config/koneksi.php'); // Pastikan koneksi database sudah benar

// Ambil data dari tabel usaha
$query = "SELECT id_usaha, nama_usaha, pemilik, alamat, provinsi, kabupaten, kecamatan, latitude, longitude, gambar_tempat_usaha FROM usaha";
$result = mysqli_query($koneksi, $query);

$data = [];

while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Mengatur header agar mengembalikan JSON
header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);


?>
