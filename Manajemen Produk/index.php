<?php

session_start();

if (!isset($_SESSION['status_login'])) {
    header("Location: login.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>

    <!-- Style Css -->
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
</head>
<body>
    <!-- Admin Sidebar -->
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="bag-check-outline"></ion-icon>
                        </span>
                        <span class="title">FzA Production</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="loadPage('dashboard.php')">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="loadPage('product.php')">
                        <span class="icon">
                            <ion-icon name="bag-handle-outline"></ion-icon>
                        </span>
                        <span class="title">Products</span>
                    </a>
                </li>

                <li>
                    <a href="#" onclick="loadPage('category.php')">
                        <span class="icon">
                            <ion-icon name="newspaper-outline"></ion-icon>
                        </span>
                        <span class="title">Kategori</span>
                    </a>
                </li>

                <li>
                     <a href="#" onclick="confirmLogout()">
                    <span class="icon">
                     <ion-icon name="log-out-outline"></ion-icon>
                    </span>
                    <span class="title">Log Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <iframe id="content-frame" src="dashboard.php"></iframe>
        </div>
    </div>

    <!-- Script JavaScript -->
    <script>
        function loadPage(page) {
            document.getElementById('content-frame').src = page;
        }

        function confirmLogout() {
        // Tampilkan notifikasi SweetAlert2
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Anda akan keluar dari sesi ini!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, keluarkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika pengguna mengonfirmasi, arahkan ke logout.php
                window.location.href = 'logout.php';
            }
        });
    }

    </script>

    <!-- Icononics -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
