<?php
// Mulai sesi untuk menyimpan notifikasi
session_start();

// Memasukkan file konfigurasi koneksi ke database
include 'configDB.php';

// Inisialisasi variabel
$error = '';
$Nama_kategori = '';
$update = false;
$id = 0;

// Handle Tambah atau Edit Kategori
if (isset($_POST['save'])) {
    $Nama_kategori = $_POST['Nama_kategori'];

    if (empty($Nama_kategori)) {
        $error = "Nama kategori tidak boleh kosong!";
    } else {
        if (isset($_POST['id']) && $_POST['id'] != 0) {
            // Proses update kategori
            $id = $_POST['id'];
            $sql = "UPDATE kategori SET Nama_kategori = '$Nama_kategori' WHERE id_kategori = $id";
        } else {
            // Proses tambah kategori
            $sql = "INSERT INTO kategori (Nama_kategori) VALUES ('$Nama_kategori')";
        }

        if ($konek->query($sql) === TRUE) {
            $_SESSION['success'] = "Kategori berhasil disimpan!";
        } else {
            $_SESSION['error'] = "Error: " . $konek->error;
        }
        header('Location: category.php');
        exit;
    }
}

// Handle Delete Kategori
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM kategori WHERE id_kategori = $id";

    if ($konek->query($sql) === TRUE) {
        $_SESSION['success'] = "Kategori berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Error: " . $konek->error;
    }
    header('Location: category.php');
    exit;
}

// Handle Edit Kategori
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM kategori WHERE id_kategori = $id";
    $result = $konek->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $Nama_kategori = $row['Nama_kategori'];
        $update = true; // Mengatur status untuk update
    }
}

// Fetch data kategori dari database
$sql = "SELECT * FROM kategori";
$result = $konek->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Kategori</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert CSS & JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container my-4">
        <h2 class="mb-4">Manajemen Kategori</h2>

        <!-- Form Input Kategori (Tambah/Edit) -->
        <div class="mb-4">
            <form id="kategoriForm" action="category.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $id; ?>">
                <label for="inputKategori" class="form-label"><?php echo $update ? 'Edit' : 'Tambah'; ?> Kategori</label>
                <input type="text" class="form-control" id="inputKategori" name="Nama_kategori" placeholder="Masukkan nama kategori" value="<?php echo $Nama_kategori; ?>" required>
                <button type="submit" name="save" class="btn btn-<?php echo $update ? 'warning' : 'primary'; ?> mt-2">
                    <?php echo $update ? 'Update' : 'Tambah'; ?>
                </button>
            </form>
        </div>

        <!-- Tabel Kategori -->
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="kategoriTable">
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $row['Nama_kategori']; ?></td>
                            <td>
                                <a href="category.php?edit=<?php echo $row['id_kategori']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="category.php?delete=<?php echo $row['id_kategori']; ?>" class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['id_kategori']; ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">Tidak ada data kategori.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Notifikasi dengan SweetAlert -->
    <?php if (isset($_SESSION['success'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '<?php echo $_SESSION['success']; ?>',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '<?php echo $_SESSION['error']; ?>',
                timer: 3000,
                showConfirmButton: false
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Bootstrap JS dan Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Handle Delete dengan AJAX -->
    <script>
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            var id_kategori = $(this).data('id');
            var row = $(this).closest('tr');

            Swal.fire({
                title: 'Yakin ingin menghapus kategori ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'category.php?delete=' + id_kategori,
                        type: 'GET',
                        success: function(response) {
                            row.remove(); // Menghapus baris dari tabel tanpa refresh
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                text: 'Kategori berhasil dihapus.',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php $konek->close(); ?>
