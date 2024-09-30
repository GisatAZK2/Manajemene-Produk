<?php 
session_start(); // Memulai sesi

include('configDB.php'); // Sambungkan ke database

// Cek apakah pengguna sudah login, jika ya arahkan ke halaman index.php
if (isset($_SESSION['status_login'])) {
    header("Location: index.php"); // Ganti 'index.php' dengan halaman yang diinginkan
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="css/login.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Manajemen Produk</title>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="" method="post">
                <h1>Buat Akun</h1>
                <span>Gunakan Email Dan Password Untuk Mendaftar</span>
                <input type="text" placeholder="Name" name="nama" required>
                <input type="email" placeholder="Email" name="email" required>
                <input type="password" placeholder="Password" name="password" required>
                <button type="submit" name="proses">Sign UP</button>
            </form>
        </div>

        <div class="form-container sign-in">
            <form action="" method="post">
                <h1>Log In</h1>
                <span>Gunakan Email Dan Password</span>
                <input type="email" placeholder="Email" name="Email" required>
                <input type="password" placeholder="Password" name="Password" required>
                <button type="submit" name="Login">Log In</button>
            </form>

            <?php
            // Proses Pendaftaran
            if (isset($_POST['proses'])) {
                $nama = mysqli_real_escape_string($konek, $_POST['nama']);
                $email = mysqli_real_escape_string($konek, $_POST['email']);
                $password = mysqli_real_escape_string($konek, $_POST['password']);

                // Menyimpan data ke database
                $query = "INSERT INTO user (username, sandi, email) VALUES ('$nama', '$password', '$email')";
                $proseskonek = mysqli_query($konek, $query);

                if ($proseskonek) {
                    echo "<script>  
                            Swal.fire({
                              icon: 'success',
                              title: 'Akun berhasil dibuat!',
                              text: 'Selamat, akun Anda telah berhasil dibuat.',
                              confirmButtonText: 'OK'
                            });
                          </script>";
                } else {
                    echo "<script>
                            Swal.fire({
                              icon: 'error',
                              title: 'Oops...',
                              text: 'Terjadi kesalahan saat membuat akun.',
                              confirmButtonText: 'OK'
                            });
                          </script>";
                }
            }

            // Proses Login
            if (isset($_POST['Login'])) {
                $gmail = mysqli_real_escape_string($konek, $_POST['Email']);
                $Password = mysqli_real_escape_string($konek, $_POST['Password']);

                // Cek kecocokan email dan password
                $cek = mysqli_query($konek, "SELECT * FROM user WHERE email='$gmail' AND sandi='$Password'");
                if (mysqli_num_rows($cek) > 0) {
                    $d = mysqli_fetch_object($cek);
                    $_SESSION['status_login'] = true;
                    $_SESSION['a_global'] = $d;
                    $_SESSION['id'] = $d->id_user;
                    echo "<script>
                            Swal.fire({
                              icon: 'success',
                              title: 'Login Berhasil',
                              text: 'Anda akan segera diarahkan ke dashboard.',
                              confirmButtonText: 'OK'
                            });
                            setTimeout(function() {
                                window.location.href = 'index.php';
                            }, 500); // Delay 0,5 detik
                          </script>";
                } else {
                    echo "<script>
                            Swal.fire({
                              icon: 'error',
                              title: 'Oops...',
                              text: 'Email Dan Password Salah.',
                              confirmButtonText: 'OK'
                            });
                          </script>";
                }
            }
            ?>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Selamat Datang Kembali!</h1>
                    <p>Daftarkan Akun Untuk Mengakses Seluruh fitur Website</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Halo, Teman!</h1>
                    <p>Masuk Dengan Akun Yang Di Daftarkan Untuk Mengakses Seluruh fitur Website</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/login.js"></script>
</body>

</html>
