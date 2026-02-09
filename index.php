<?php
require('config/koneksi.php');

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

$query_usaha_bulanan = "SELECT 
    MONTH(created_at) AS bulan,
    COUNT(*) AS total
    FROM usaha
    GROUP BY MONTH(created_at)
    ORDER BY bulan";

$result_usaha_bulanan = mysqli_query($koneksi, $query_usaha_bulanan);

$bulan_labels = [];
$jumlah_usaha_per_bulan = [];

$nama_bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

while ($row = mysqli_fetch_assoc($result_usaha_bulanan)) {
    $bulan_labels[] = $nama_bulan[$row['bulan'] - 1]; // konversi ke nama bulan
    $jumlah_usaha_per_bulan[] = $row['total'];
}

// Produk per bulan
$query_produk_bulanan = "SELECT MONTH(created_at) AS bulan, COUNT(*) AS total FROM produk GROUP BY MONTH(created_at) ORDER BY bulan";
$result_produk_bulanan = mysqli_query($koneksi, $query_produk_bulanan);

$bulan_produk = [];
$jumlah_produk_per_bulan = [];

while ($row = mysqli_fetch_assoc($result_produk_bulanan)) {
    $bulan_produk[] = $nama_bulan[$row['bulan'] - 1];
    $jumlah_produk_per_bulan[] = $row['total'];
}

// Sertifikat unik per bulan
$query_sertifikat_bulanan = "SELECT MONTH(created_at) AS bulan, COUNT(DISTINCT nomor_sertifikat) AS total FROM produk GROUP BY MONTH(created_at) ORDER BY bulan";
$result_sertifikat_bulanan = mysqli_query($koneksi, $query_sertifikat_bulanan);

$bulan_sertifikat = [];
$jumlah_sertifikat_per_bulan = [];

while ($row = mysqli_fetch_assoc($result_sertifikat_bulanan)) {
    $bulan_sertifikat[] = $nama_bulan[$row['bulan'] - 1];
    $jumlah_sertifikat_per_bulan[] = $row['total'];
}

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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/indexx.css">
</head>
<body>
    <div class="hover-zone-top"></div>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="assets/images/logo UIN IB.png" alt="Logo" width="40" height="auto">
                </a>
                <div class="navbar-toggle" id="navbarToggle">
                    <i class="bi bi-list"></i>
                </div>
                <div class="navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                                <i class="bi bi-house-door"></i> Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pages/user/maps.php' ? 'active' : ''; ?>" href="pages/user/maps.php">
                                <i class="bi bi-geo-alt"></i> Map
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'pages/user/all_kuliner.php' ? 'active' : ''; ?>" href="pages/user/all_kuliner.php">
                                <i class="bi bi-shop"></i> Store
                            </a>
                        </li>
                        <li class="nav-item d-lg-none">
                            <a class="nav-link" href="pages/admin/login_admin.php">
                                <i class="bi bi-person-circle"></i> Login
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="pages/admin/login_admin.php" class="btn-login">
                    <i class="bi bi-person-circle"></i>
                </a>
            </div>
        </nav>
    </header>
    <section id="judul">
        <div class="logo-container">
            <img src="assets/images/lg.png" alt="Logo 1" class="logo">
            <img src="assets/images/hll.png" alt="Logo 2" class="logo">
            <img src="assets/images/lgsi.png" alt="Logo 3" class="logo">
        </div>
            <h1>SISTEM INFORMASI PEMETAAN <span class="sertifikasi">SERTIFIKASI HALAL</span></h1>
        <div class="container-r">
            <a>Sistem informasi pemetaan sertifikasi halal yang membantu anda menemukan produk halal dengan cepat dan mudah. Dengan data real-time dan peta interaktif, memastikan transparansi dan kemudahan akses bagi  pengguna.</a>
            <button class="booking-btn" onclick="window.location.href='pages/user/all_kuliner.php'">
                Cari Sertifikat Halal <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
        <div class="container-s">
            <a href="#statistik" class="view">
                <span>Selengkapnya...</span>
                <i class="fas fa-arrow-down"></i> <!-- Ikon panah ke bawah -->
            </a>
        </div>
    </section>

    <section id="statistik">
            <div class="section-header">
                <div class="section-line"></div>
                <h2>STATISTIK</h2>
                <div class="section-line"></div>
            </div>

            <div class="circle-stats-container">
                <div class="circle" id="circle-usaha">
                    <div class="circle-stat green">
                        <div class="circle-icon"><i class="fas fa-store"></i></div>
                    </div>
                    <div class="circle-stat white">
                        <div class="circle-content">
                            <h4>Usaha</h4>
                            <div class="circle-value"><?php echo $count1; ?></div>
                        </div>
                    </div>
                </div>
                <div class="circle" id="circle-produk">
                    <div class="circle-stat blue">
                        <div class="circle-icon"><i class="fas fa-boxes"></i></div>
                    </div>
                    <div class="circle-stat white">
                        <div class="circle-content">
                            <h4>Produk</h4>
                            <div class="circle-value"><?php echo $count2; ?></div>
                        </div>
                    </div>
                </div>
                <div class="circle" id="circle-sertifikat">
                    <div class="circle-stat orange">
                        <div class="circle-icon"><i class="fas fa-certificate"></i></div>
                    </div>
                    <div class="circle-stat white">
                        <div class="circle-content">
                            <h4>Sertifikat</h4>
                            <div class="circle-value"><?php echo $total_sertifikat; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="chart-container">
                <!-- Pie Chart -->
                <div class="chart-box" id="pieChartBox">
                    <h3>Jenis Produk Berdasarkan Skema Self Declare</h3>
                    <canvas id="pieChart"></canvas>
                </div>
                <!-- Bar Chart -->
                <div class="chart-box">
                    <h3>Distribusi Sertifikat per Kabupaten</h3>
                    <canvas id="barChart"></canvas>
                </div>
            </div>
    </section>

    <div id="usahaModal" class="modal-graph">
        <div class="modal-content-graph">
            <span class="close-graph" id="closeGraph">&times;</span>
            <h3>Pertumbuhan Usaha per Bulan</h3>
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    <!-- Modal Produk -->
    <div id="produkModal" class="modal-graph">
        <div class="modal-content-graph">
            <span class="close-graph" id="closeProduk">&times;</span>
            <h3>Pertumbuhan Produk per Bulan</h3>
            <canvas id="produkChart"></canvas>
        </div>
    </div>

    <!-- Modal Sertifikat -->
    <div id="sertifikatModal" class="modal-graph">
        <div class="modal-content-graph">
            <span class="close-graph" id="closeSertifikat">&times;</span>
            <h3>Pertumbuhan Sertifikat per Bulan</h3>
            <canvas id="sertifikatChart"></canvas>
        </div>
    </div>


    <!-- Section Peta -->
    <section id="peta">
        <div class="section-header">
            <div class="section-line"></div>
            <h2>MAP</h2>
            <div class="section-line"></div>
        </div>
        <div class="map-box">
            <div id="map"></div>
        </div>
    </section>
</body>
<script>
    const navbar = document.querySelector(".navbar");
    let lastScroll = 0;

    window.addEventListener("scroll", () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > lastScroll && currentScroll > 100) {
        // Scroll down
        navbar.classList.add("hide");
        } else {
        // Scroll up
        navbar.classList.remove("hide");
        }

        lastScroll = currentScroll;
    });

    // Deteksi mouse posisi: jika mouse dekat atas, munculkan navbar
    document.addEventListener("mousemove", function (e) {
        if (e.clientY < 80) {
        navbar.classList.remove("hide");
        }
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('navbarToggle');
        const navMenu = document.getElementById('navbarNav');

        toggle.addEventListener('click', function () {
            navMenu.classList.toggle('show');
        });
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
            maintainAspectRatio: false,
            plugins: {
                legend:{ display: false },
                //     display: window.innerWidth > 768,
                //     position: 'right', // Legend di samping kanan
                //     labels: {
                //         usePointStyle: true,
                //         boxWidth: 10,
                //     },
                //     maxHeight: 200, // Maksimal tinggi legend agar bisa scroll
                //     maxWidth: 400, // Lebar maksimal agar tidak terlalu besar
                // },
                title: {
                    display: true,
                    text: 'Distribusi Jenis Produk'
                },
                tooltip: {
                    enabled: false,
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
                            i === hoveredIndex ? 10 : 0
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

    // Inisialisasi Peta
    var map = L.map('map').setView([-0.9272612, 100.3679965], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var customIcon = L.icon({
        iconUrl: 'assets/images/Ha.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    var heatmapPoints = [];
    let dataUsaha = [];
    var markers = [];

    fetch('action/data_usaha.php')
        .then(response => response.json())
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                console.warn('Data kosong atau tidak valid!');
                return;
            }
            dataUsaha = data;

            data.forEach(item => {
                if (item.latitude && item.longitude) {
                    heatmapPoints.push([parseFloat(item.latitude), parseFloat(item.longitude), 1]);

                    var marker = L.marker([parseFloat(item.latitude), parseFloat(item.longitude)], { icon: customIcon })
                        .addTo(map)
                        .bindPopup(`
                            <div class="custom-popup">
                                <div class="image-container">
                                    ${item.gambar_tempat_usaha ? `<img src="uploads/${item.gambar_tempat_usaha}" class="popup-image">` : ''}
                                </div>
                                <b>${item.nama_usaha}</b>
                                <span>Latitude: ${item.latitude}</span>
                                <span>Longitude: ${item.longitude}</span>
                                <a href="#" class="direction-link " data-lat="${item.latitude}" data-lng="${item.longitude}" title="Arahkan ke Lokasi">
                                    <div class="direction-icon">
                                        <i class="bi bi-sign-turn-slight-right" style="font-size: 18px;"></i>
                                    </div>
                                </a>
                                <a href="pages/user/detail.php?id=${item.id_usaha}" class="detail-link">Lihat Detail ></a>
                            </div>
                        `, { maxWidth: 200, minWidth: 150 })
                        .bindTooltip(item.nama_usaha, { permanent: false, direction: "right" });

                    markers.push(marker);
                }
            });

            if (heatmapPoints.length > 0) {
                L.heatLayer(heatmapPoints, {
                    radius: 25,
                    blur: 15,
                    maxZoom: 17
                }).addTo(map);
            }
        })
        .catch(error => console.error('Gagal mengambil data:', error));

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

    document.getElementById('circle-usaha').addEventListener('click', function () {
        const modal = document.getElementById('usahaModal');
        modal.style.display = 'flex';

        // Gambar grafik jika belum pernah dibuat
        if (!window.lineChartUsaha) {
            const ctx = document.getElementById('lineChart').getContext('2d');

            const labels = <?= json_encode($bulan_labels); ?>;
            const data = <?= json_encode($jumlah_usaha_per_bulan); ?>;

            window.lineChartUsaha = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Usaha',
                        data: data,
                        borderColor: 'green',
                        backgroundColor: 'rgba(0,128,0,0.2)',
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    });

    // Tombol untuk menutup popup
    document.getElementById('closeGraph').addEventListener('click', function () {
        document.getElementById('usahaModal').style.display = 'none';
    });

        
    document.querySelectorAll('.circle')[1].addEventListener('click', function () {
        const modal = document.getElementById('produkModal');
        modal.style.display = 'flex';

        if (!window.lineChartProduk) {
            const ctx = document.getElementById('produkChart').getContext('2d');
            const labels = <?= json_encode($bulan_produk); ?>;
            const data = <?= json_encode($jumlah_produk_per_bulan); ?>;

            window.lineChartProduk = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Produk',
                        data: data,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0,0,255,0.2)',
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    });

    document.querySelectorAll('.circle')[2].addEventListener('click', function () {
        const modal = document.getElementById('sertifikatModal');
        modal.style.display = 'flex';

        if (!window.lineChartSertifikat) {
            const ctx = document.getElementById('sertifikatChart').getContext('2d');
            const labels = <?= json_encode($bulan_sertifikat); ?>;
            const data = <?= json_encode($jumlah_sertifikat_per_bulan); ?>;

            window.lineChartSertifikat = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Sertifikat',
                        data: data,
                        borderColor: 'orange',
                        backgroundColor: 'rgba(255,165,0,0.2)',
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    });

    document.getElementById('closeProduk').addEventListener('click', function () {
        document.getElementById('produkModal').style.display = 'none';
    });

    document.getElementById('closeSertifikat').addEventListener('click', function () {
        document.getElementById('sertifikatModal').style.display = 'none';
    });
</script>
</html>