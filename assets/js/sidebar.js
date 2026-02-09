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

setInterval(updateDateTime, 1000); 
updateDateTime(); 


document.getElementById("toggleSidebar").addEventListener("click", function () {
    const sidebar = document.getElementById("mySidenav");
    const navbar = document.querySelector(".navbar");
    sidebar.classList.toggle("collapsed");

    // Menyesuaikan class pada navbar agar ikut bergeser
    if (sidebar.classList.contains("collapsed")) {
        navbar.style.left = "70px";
        navbar.style.width = "calc(100% - 70px)";
    } else {
        navbar.style.left = "220px";
        navbar.style.width = "calc(100% - 220px)";
    }
});

function checkScreenWidth() {
    const sidebar = document.getElementById("mySidenav");
    const navbar = document.querySelector(".navbar");

    if(window.innerWidth <= 767) {
        // Otomatis collapse sidebar di layar kecil
        sidebar.classList.add("collapsed");
        navbar.style.left = "70px";
        navbar.style.width = "calc(100% - 70px)";
    } else {
        // Di layar besar, sidebar tetap terbuka
        sidebar.classList.remove("collapsed");
        navbar.style.left = "220px";
        navbar.style.width = "calc(100% - 220px)";
    }
}

window.addEventListener("resize", checkScreenWidth);
window.addEventListener("load", checkScreenWidth);