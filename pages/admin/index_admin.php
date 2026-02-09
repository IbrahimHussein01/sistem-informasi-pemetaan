<?php
require('../../config/koneksi.php');
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../admin/login_admin.php");
    exit();
}

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../assets/css/index_adm.css">
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
		<div class="row mb-1">
			<div class="container-box1">
				<div class="container-content">
					<h2><?php echo $count1; ?></h2>
					<i class="fas fa-industry fa-3x" style="color: #3b69da;"></i> <!-- Ikon Toko -->
				</div>
				<h4>Usaha</h4>
			</div>
			<div class="container-box2">
				<div class="container-content">
					<h2><?php echo $count2; ?></h2>
					<i class="fas fa-box fa-3x" style="color: #28a745;"></i> <!-- Ikon Toko -->
				</div>
				<h4>Produk</h4>
			</div>
			<div class="container-box3">
				<div class="container-content">
					<h2><?php echo $total_sertifikat; ?></h2>
					<i class="fas fa-award fa-3x" style="color: #dc3545;"></i> <!-- Ikon Toko -->
				</div>
				<h4>Sertifikat Halal</h4>
			</div>
		</div>
		<div class="row">
			<div class="parent-container-pie">
				<div class="container-up-pie">
					<h4 class="mb-3">Jenis Produk Self Declare</h4>
				</div>
				<div class="container-pie">
					<canvas id="pieChart"></canvas>
				</div>
			</div>
			<div class="parent-container-bar">
				<div class="container-up-bar">
					<h4 class="mb-3">Data Sertifikasi Per Kabupaten</h4>
				</div>
				<div class="container-bar">
					<canvas id="barChart"></canvas>
				</div>
			</div>
		</div>
	</div>	
</body>

<script>
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidenav = document.querySelector('.sidenav');
    const main2 = document.getElementById('main2');
    const navbar = document.querySelector('.navbar');

    toggleBtn.addEventListener('click', () => {
        sidenav.classList.toggle('collapsed');
        main2.classList.toggle('collapsed');
        navbar.classList.toggle('collapsed');
    });

    // Data dari PHP
    const dataJenisProduk = <?php echo $data_jenis_produk_json; ?>;
    const dataKabupaten = <?php echo $data_kabupaten_json; ?>;

    // Fungsi untuk memformat label dengan baris baru
    const formatLabel = (text, maxLength) => {
        const words = text.split(' ');
        let lines = [];
        let currentLine = '';

        words.forEach(word => {
            if ((currentLine + ' ' + word).trim().length > maxLength) {
                lines.push(currentLine.trim());
                currentLine = word;
            } else {
                currentLine += ' ' + word;
            }
        });

        if (currentLine.trim()) {
            lines.push(currentLine.trim());
        }

        return lines.join('\n');
    };

    // Generate warna otomatis
    const generateColors = (count) => {
        return Array.from({ length: count }, (_, i) => `hsl(${(i * 360 / count)}, 70%, 60%)`);
    };

    const formattedLabels = dataJenisProduk.map(item => formatLabel(item.jenis_produk, 20));
    const colors = generateColors(dataJenisProduk.length);
    let hoveredIndex = null;

    const pieChartCtx = document.getElementById('pieChart').getContext('2d');

    const pieChart = new Chart(pieChartCtx, {
        type: 'pie',
        data: {
            labels: formattedLabels,
            datasets: [{
                label: 'Jumlah Produk',
                data: dataJenisProduk.map(item => item.total),
                backgroundColor: colors,
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }, // Hilangkan legend
                title: { display: true, text: 'Distribusi Jenis Produk' },
                tooltip: {
                    enabled: false, // Matikan tooltip bawaan
                    external: function (context) {
                        let tooltipEl = document.getElementById('chartTooltip');

                        if (!tooltipEl) {
                            tooltipEl = document.createElement('div');
                            tooltipEl.id = 'chartTooltip';
                            tooltipEl.style.position = 'absolute';
                            tooltipEl.style.background = 'rgba(255, 255, 255, 0.9)';
                            tooltipEl.style.padding = '10px';
                            tooltipEl.style.borderRadius = '8px';
                            tooltipEl.style.boxShadow = '0px 4px 8px rgba(0,0,0,0.2)';
                            tooltipEl.style.pointerEvents = 'none';
                            tooltipEl.style.fontSize = '14px';
                            tooltipEl.style.transition = 'opacity 0.2s ease-in-out';
                            tooltipEl.style.opacity = 0;
                            document.body.appendChild(tooltipEl);
                        }

                        const tooltipModel = context.tooltip;
                        if (tooltipModel.opacity === 0) {
                            tooltipEl.style.opacity = 0;
                            return;
                        }

                        if (tooltipModel.dataPoints) {
                            const dataPoint = tooltipModel.dataPoints[0];
                            hoveredIndex = dataPoint.dataIndex;
                            tooltipEl.innerHTML = `<strong>${dataPoint.label}</strong><br>Jumlah: ${dataPoint.raw}`;

                            const canvas = context.chart.canvas;
                            const rect = canvas.getBoundingClientRect();

                            tooltipEl.style.opacity = 1;
                            tooltipEl.style.left = `${rect.left + window.pageXOffset + dataPoint.element.x + 20}px`;
                            tooltipEl.style.top = `${rect.top + window.pageYOffset + dataPoint.element.y - 20}px`;
                        }
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                intersect: false
            },
            hover: {
                mode: 'index',
                animationDuration: 200,
                onHover: (event, elements) => {
                    if (elements.length) {
                        hoveredIndex = elements[0].index;
                    } else {
                        hoveredIndex = null;
                    }
                    pieChart.update();
                }
            },
            animation: {
                duration: 500,
                onProgress: function () {
                    if (hoveredIndex !== null) {
                        pieChart.data.datasets[0].backgroundColor = colors.map((color, i) =>
                            i === hoveredIndex ? color : 'rgba(200, 200, 200, 0.4)'
                        );

                        pieChart.data.datasets[0].offset = colors.map((_, i) =>
                            i === hoveredIndex ? 10 : 0 // Naik 10px saat hover
                        );
                    } else {
                        pieChart.data.datasets[0].backgroundColor = colors;
                        pieChart.data.datasets[0].offset = colors.map(() => 0);
                    }
                }
            }
        }
    });

    // Bar Chart
    const barChartCtx = document.getElementById('barChart').getContext('2d');

    // Buat gradasi warna untuk setiap bar
    const gradient = barChartCtx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, '#0083B0');  // Biru Tua (atas)
    gradient.addColorStop(0.33, '#00B4DB'); // Biru Muda
    gradient.addColorStop(0.66, '#00E3E3'); // Aqua
    gradient.addColorStop(1, '#00C9A7');  // Hijau Muda (bawah)
    
    const barChart = new Chart(barChartCtx, {
        type: 'bar',
        data: {
            labels: dataKabupaten.map(item => item.kabupaten.replace(/_/g, ' ')),
            datasets: [{
                label: 'Jumlah Usaha',
                data: dataKabupaten.map(item => item.total),
                backgroundColor: gradient,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: true,
                    text: 'Distribusi Sertifikat per Kabupaten'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Logout
    function confirmLogout() {
        if (confirm("Apakah Anda yakin ingin keluar?")) {
            logout();
        }
    }
    function logout() {
        sessionStorage.removeItem('userSession');
        window.location.href = '../admin/login_admin.php';
    }

    // Update Tanggal dan Waktu
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