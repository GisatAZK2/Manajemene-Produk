<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://fonts.googleapis.com/css2?family=SUSE:wght@400;700&display=swap" rel="stylesheet">

<style>
    .stok-low {
        color: #EED202;
    }
    .stok-out {
        color: red;
    }
</style>

<?php
include('configDB.php'); // include database connection

// Handle search query if it exists
$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
}

// Modify the SQL query to search for products by name
$sql = "SELECT product_id, name_product, harga, product_image1, stok FROM product WHERE name_product LIKE ?";
$stmt = $konek->prepare($sql);
$search_query = '%' . $search_term . '%'; // Prepare the search term for a LIKE query
$stmt->bind_param('s', $search_query);
$stmt->execute();
$result = $stmt->get_result();

// Check if the request is to delete a product
if (isset($_POST['action']) && $_POST['action'] == 'deleteProduct') {
    $product_id = $_POST['product_id'];

    if (!empty($product_id)) {
        // Execute the delete query
        $sql = "DELETE FROM product WHERE product_id = ?";
        $stmt = $konek->prepare($sql);
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            echo 'Produk berhasil dihapus.'; // Mengirim pesan sukses
        } else {
            echo 'Gagal menghapus produk.'; // Mengirim pesan error
        }

        $stmt->close();
    } else {
        echo 'ID produk tidak valid.';
    }

    exit;
}

// Handle mass delete request
if (isset($_POST['action']) && $_POST['action'] == 'massDelete') {
    $product_ids = json_decode($_POST['product_ids'], true);

    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?')); // Prepare placeholders for SQL IN clause
        $types = str_repeat('i', count($product_ids)); // Set types for each parameter (i for integer)

        // Create SQL to delete multiple products
        $sql = "DELETE FROM product WHERE product_id IN ($placeholders)";
        $stmt = $konek->prepare($sql);

        // Bind parameters dynamically
        $stmt->bind_param($types, ...$product_ids);

        if ($stmt->execute()) {
            echo 'Produk yang dipilih berhasil dihapus.'; // Mengirim pesan sukses
        } else {
            echo 'Gagal menghapus produk yang dipilih.'; // Mengirim pesan error
        }

        $stmt->close();
    } else {
        echo 'Tidak ada produk yang dipilih untuk dihapus.';
    }

    exit;
}
?>

<link rel="stylesheet" href="css/product.css">

<div id="content-area">
    <?php
    if ($result->num_rows > 0) {
        echo '<div class="product-page">';
        echo '<h1>Produk Saya</h1>';
        echo '<div class="tabs">';
        echo '<a href="#" class="active">Semua</a>';
        echo '<a href="#">Live (' . $result->num_rows . ')</a>';
        echo '</div>';

        echo '<div class="actions">';
        echo '<button class="mass-action-btn" id="mass-delete-btn">Hapus Massal</button>';
        echo '<button id="add-product-btn" class="new-product-btn" onclick="window.location.href=\'addProduct.php\';">+ Tambah Produk Baru</button>';
        echo '</div>';

        echo '<div class="search-filter">';
        echo '<input type="text" id="search-input" placeholder="Cari Nama Produk" value="' . htmlspecialchars($search_term) . '">';
        echo '<button class="search-category-btn" onclick="searchProduct()">Cari</button>';
        echo '</div>';

        echo '<h2>Total Produk: ' . $result->num_rows . '</h2>';
        echo '<div class="products-grid">';

        while ($row = $result->fetch_assoc()) {
            $stokClass = '';
            if ($row['stok'] == 0) {
                $stokClass = 'stok-out';
            } elseif ($row['stok'] < 5) {
                $stokClass = 'stok-low';
            }

            echo '<div class="card">';
            echo '<input type="checkbox" class="delete-checkbox" value="' . $row['product_id'] . '">'; // Add checkbox
            echo '<div class="img"><img src="' . htmlspecialchars($row['product_image1']) . '" alt="' . htmlspecialchars($row['name_product']) . '"></div>';
            echo '<div class="nama-produk"><p>' . htmlspecialchars($row['name_product']) . '</p></div>';
            echo '<div class="harga"><p>Rp. ' . number_format($row['harga'], 0, ',', '.') . '</p></div>';
            echo '<div class="stok ' . $stokClass . '"><p>Stok: ' . number_format($row['stok'], 0, ',', '.') . '</p></div>';
            echo '<div class="aksi">';
            echo '<button class="edit" onclick="window.location.href=\'editProduct.php?product_id=' . $row['product_id'] . '\'">Edit</button>';
            echo '<button class="delete" data-product-id="' . $row['product_id'] . '"><img src="https://img.icons8.com/ios-filled/24/000000/delete.png" alt="Delete"></button>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    } else {
        echo '<p>No products found.</p>';
        echo '<button id="add-product-btn" class="new-product-btn" onclick="window.location.href=\'addProduct.php\';">+ Tambah Produk Baru</button>';
    }

    $stmt->close();
    $konek->close();
    ?>
</div>

<script>
function searchProduct() {
    const searchInput = document.getElementById('search-input').value;
    window.location.href = `product.php?search=${encodeURIComponent(searchInput)}`;
}

function deleteProduct(productId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Produk ini akan dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus produk!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    console.log(xhr.responseText);  // Log server response
                    if (xhr.status === 200) {
                        Swal.fire('Dihapus!', xhr.responseText, 'success').then(() => {
                            location.reload('product.php');
                        });
                    } else {
                        Swal.fire('Gagal!', 'Gagal menghapus produk. Silakan coba lagi.', 'error');
                    }
                }
            };
            xhr.open('POST', 'product.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('action=deleteProduct&product_id=' + productId);
        }
    });
}

function initDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            deleteProduct(productId);
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    initDeleteButtons();
});

function deleteSelectedProducts() {
    const selectedCheckboxes = document.querySelectorAll('.delete-checkbox:checked');
    const selectedProductIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);

    if (selectedProductIds.length === 0) {
        Swal.fire('Peringatan', 'Tidak ada produk yang dipilih.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: `Anda akan menghapus ${selectedProductIds.length} produk yang dipilih!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus produk!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    console.log(xhr.responseText);  // Log server response
                    if (xhr.status === 200) {
                        Swal.fire({
                            title: 'Dihapus!',
                            text: `${selectedProductIds.length} produk berhasil dihapus.`,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal!', 'Gagal menghapus produk. Silakan coba lagi.', 'error');
                    }
                }
            };
            xhr.open('POST', 'product.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('action=massDelete&product_ids=' + JSON.stringify(selectedProductIds));
        }
    });
}

document.getElementById('mass-delete-btn').addEventListener('click', deleteSelectedProducts);
</script>
