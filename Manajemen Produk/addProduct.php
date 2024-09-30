<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="css/add_product.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container-produk">
    <p>Informasi Produk</p>
    <p>Foto Produk <sub>*Maksimal 5 Foto</sub></p>

    <!-- Form submission handled by the same page -->
    <form id="productForm" method="POST" enctype="multipart/form-data" action="">
        <div class="card-foto" id="cardContainer">
            <!-- Box for the first upload -->
            <div class="upload-box" id="uploadBox1">
                <p id="uploadText1">Tambahkan Foto (1/5)</p>
                <input type="file" name="product_image1" accept="image/*" onchange="previewImage(event, 1)">
                <img id="imagePreview1" alt="Foto Produk">
                <button class="remove-btn" id="removeBtn1" onclick="removeImage(1)">x</button>
            </div>
        </div>

        <!-- Nama Produk Input -->
        <div class="nama-produk">
            <label for="namaproduct">Nama Produk :</label>
            <input type="text" name="namaproduct" required>
        </div>

        <!-- Kategori Produk Dropdown -->
        <div class="kategori-produk">
            <label for="kategori">Kategori :</label>
            <select name="kategori" id="kategori" required>
                <option value="">Pilih Kategori</option>
                <?php
                // Include your database configuration
                include('configDB.php');
                // Query to fetch categories from the 'kategori' table
                $query = "SELECT Nama_kategori FROM kategori";
                $result = mysqli_query($konek, $query);
                // Check if the query was successful
                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row['Nama_kategori'] . '">' . $row['Nama_kategori'] . '</option>';
                    }
                } else {
                    echo '<option value="">Kategori tidak tersedia</option>';
                }
                ?>
            </select>
        </div>

        <!-- Deskripsi Produk -->
        <div class="deskripsi-produk">
            <label for="deskripsiproduk">Deskripsi Produk :</label>
            <textarea name="deskripsiproduk" required></textarea>
        </div>

        <!-- Harga Produk -->
        <div class="harga-produk">
            <label for="hargaproduk">Harga Produk :</label>
            <input type="text" id="hargaproduk" name="hargaproduk" required oninput="formatRupiah(this)">
        </div>

        <div class="stok-produk">
            <label for="stokproduk">stok Produk :</label>
            <input type="text" id="stokproduk" name="stokproduk" required">
        </div>

        <!-- Submit Button -->
        <div class="form-actions">
            <button type="submit" id="submitProduct">Submit Produk</button>
            <button type="button" id="cancelButton">Cancel</button>
        </div>
    </form>
</div>

<script src="js/add_product.js"></script>
<?php
// Logic to process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi input
    $namaproduct = isset($_POST['namaproduct']) ? trim($_POST['namaproduct']) : null;
    $description = isset($_POST['deskripsiproduk']) ? trim($_POST['deskripsiproduk']) : null;
    $category = isset($_POST['kategori']) ? trim($_POST['kategori']) : null;
    $stok = isset($_POST['stokproduk']) ? trim($_POST['stokproduk']) : null;
    $harga = isset($_POST['hargaproduk']) ? str_replace(['Rp', '.'], '', trim($_POST['hargaproduk'])) : null;
    $harga = intval($harga);  // Convert to integer

    // Validasi gambar
    $image1 = isset($_FILES['product_image1']) && $_FILES['product_image1']['error'] == UPLOAD_ERR_OK ? $_FILES['product_image1'] : null;
    $image2 = isset($_FILES['product_image2']) && $_FILES['product_image2']['error'] == UPLOAD_ERR_OK ? $_FILES['product_image2'] : null;
    $image3 = isset($_FILES['product_image3']) && $_FILES['product_image3']['error'] == UPLOAD_ERR_OK ? $_FILES['product_image3'] : null;
    $image4 = isset($_FILES['product_image4']) && $_FILES['product_image4']['error'] == UPLOAD_ERR_OK ? $_FILES['product_image4'] : null;
    $image5 = isset($_FILES['product_image5']) && $_FILES['product_image5']['error'] == UPLOAD_ERR_OK ? $_FILES['product_image5'] : null;

    // Pengecekan apakah semua field sudah diisi
    if (!$namaproduct || !$description || !$category || !$harga || !$stok=== "" || !$image1) {
        echo "<p style='color:red;'>Semua field harus diisi!</p>";
    } else {
        // Upload directory
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Make sure the upload directory exists
        }

        // Upload gambar
        $imagePath1 = $uploadDir . basename($image1['name']);
        $imagePath2 = $image2 ? $uploadDir . basename($image2['name']) : null;
        $imagePath3 = $image3 ? $uploadDir . basename($image3['name']) : null;
        $imagePath4 = $image4 ? $uploadDir . basename($image4['name']) : null;
        $imagePath5 = $image5 ? $uploadDir . basename($image5['name']) : null;

        // Upload file gambar 1
        move_uploaded_file($image1['tmp_name'], $imagePath1);
        if ($image2) move_uploaded_file($image2['tmp_name'], $imagePath2);
        if ($image3) move_uploaded_file($image3['tmp_name'], $imagePath3);
        if ($image4) move_uploaded_file($image4['tmp_name'], $imagePath4);
        if ($image5) move_uploaded_file($image5['tmp_name'], $imagePath5);

        // Prepare insert query
        $query = "INSERT INTO product (name_product, description, category, harga, stok, product_image1, product_image2, product_image3, product_image4, product_image5)  
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($konek, $query);

        // Bind the parameters
        mysqli_stmt_bind_param($stmt, 'sssissssss', $namaproduct, $description, $category, $harga, $stok, $imagePath1, $imagePath2, $imagePath3, $imagePath4, $imagePath5);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            // Produk berhasil ditambahkan, tampilkan SweetAlert
            echo "<script>
                Swal.fire({
                title: 'Produk Berhasil Ditambahkan!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then((result) => {
        if (result.isConfirmed) {
        window.location.href = 'product.php';
        }
        });
            </script>";
        } else {
            // Show error message
            echo "<script>
                Swal.fire({
                    title: 'Gagal Menambahkan Produk!',
                    text: '" . mysqli_error($konek) . "',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
        
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($konek);
?>

<script>
    let formIsDirty = false;

    // Menandai form sebagai dirty jika ada perubahan pada input, textarea, atau select
    document.querySelectorAll('input, textarea, select').forEach(input => {
        input.addEventListener('input', () => {
            formIsDirty = true;
        });
    });

    // Konfirmasi saat pengguna ingin meninggalkan halaman
    window.addEventListener('beforeunload', function (e) {
        if (formIsDirty) {
            e.preventDefault();
            e.returnValue = ''; // Beberapa browser memerlukan returnValue meskipun tidak ditampilkan
        }
    });

    // Tombol Cancel untuk kembali ke halaman product.php dengan SweetAlert
    document.getElementById('cancelButton').addEventListener('click', function() {
        if (formIsDirty) {
            Swal.fire({
                title: 'Data belum tersimpan!',
                text: "Ingin melanjutkan tanpa menyimpan perubahan?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, lanjutkan',
                cancelButtonText: 'Tidak, batalkan'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'product.php'; // Redirect ke halaman product.php
                }
            });
        } else {
            window.location.href = 'product.php'; // Redirect tanpa peringatan jika tidak ada perubahan
        }
    });

    // Setelah form berhasil disubmit, hilangkan peringatan
    document.getElementById('productForm').addEventListener('submit', function() {
        formIsDirty = false;
    });
</script>


</body>
</html>
                                 