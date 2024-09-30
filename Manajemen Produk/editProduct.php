<?php
include('configDB.php'); // Include your database connection

// Get the product ID from the URL parameter
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch product details including five images
    $sql = "SELECT name_product, harga, stok, description, product_image1, product_image2, product_image3, product_image4, product_image5 FROM product WHERE product_id = ?";
    $stmt = $konek->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($name_product, $harga, $stok, $description, $product_image1, $product_image2, $product_image3, $product_image4, $product_image5);
    $stmt->fetch();
    $stmt->close();

    // If form is submitted, update product details
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name_product = $_POST['name_product'];
        $harga = $_POST['harga'];
        $stok = $_POST['stok'];
        $description = $_POST['description'];

        // Existing images from the database
        $uploaded_images = [$product_image1, $product_image2, $product_image3, $product_image4, $product_image5]; 

        // Handle deleted images from the frontend
        if (!empty($_POST['deleted_images'])) {
            $deleted_images = explode(',', $_POST['deleted_images']);
            foreach ($deleted_images as $index) {
                if (isset($uploaded_images[$index])) {
                    // Hapus gambar dari direktori server jika ada
                    if (!empty($uploaded_images[$index]) && file_exists($uploaded_images[$index])) {
                        unlink($uploaded_images[$index]); // Delete file from server
                    }
                    $uploaded_images[$index] = ''; // Kosongkan slot gambar di database
                }
            }
        }

        // Process each uploaded file, limit to 5 images max
        if (!empty($_FILES['product_image']['name'][0])) {
            $total_existing_images = count(array_filter($uploaded_images)); // Count existing images

            for ($i = 0; $i < count($_FILES['product_image']['name']); $i++) {
                if ($total_existing_images + $i >= 5) {
                    break; // Limit to 5 images
                }

                if (!empty($_FILES['product_image']['name'][$i])) {
                    $target_dir = "uploads/";
                    $image_name = $target_dir . time() . "_" . basename($_FILES['product_image']['name'][$i]);

                    // Check if file upload is successful
                    if (move_uploaded_file($_FILES['product_image']['tmp_name'][$i], $image_name)) {
                        // Replace existing empty image slot or add a new one
                        $empty_index = array_search('', $uploaded_images); // Find the first empty slot
                        if ($empty_index !== false) {
                            $uploaded_images[$empty_index] = $image_name; // Fill empty slot
                        } else {
                            $uploaded_images[] = $image_name; // Add new image if no empty slot
                        }
                    } else {
                        echo "<script>alert('Gagal mengupload gambar: " . $_FILES['product_image']['name'][$i] . "');</script>";
                    }
                }
            }
        }

        // Update sorted image order from the hidden input
        $image_order = explode(',', $_POST['image_order']);
        $sorted_images = [];

        foreach ($image_order as $index) {
            if (isset($uploaded_images[$index])) {
                $sorted_images[] = $uploaded_images[$index]; // Sort images based on the user-provided order
            } else {
                $sorted_images[] = ""; // Add empty string for any missing images
            }
        }

        // Ensure that all images are variables and not expressions
        $product_image1 = $sorted_images[0] ?? '';
        $product_image2 = $sorted_images[1] ?? '';
        $product_image3 = $sorted_images[2] ?? '';
        $product_image4 = $sorted_images[3] ?? '';
        $product_image5 = $sorted_images[4] ?? '';

        // Update product details in the database with all five images
        $sql = "UPDATE product SET name_product = ?, harga = ?, stok = ?, description = ?, product_image1 = ?, product_image2 = ?, product_image3 = ?, product_image4 = ?, product_image5 = ? WHERE product_id = ?";
        $stmt = $konek->prepare($sql);
        $stmt->bind_param("siissssssi", $name_product, $harga, $stok, $description, $product_image1, $product_image2, $product_image3, $product_image4, $product_image5, $product_id);

        if ($stmt->execute()) {
            echo "<script>alert('Produk berhasil diperbarui!'); window.location.href = 'product.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui produk.');</script>";
        }

        $stmt->close();
    }
} else {
    echo "<p>Produk tidak ditemukan.</p>";
    exit;
}

$konek->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f0f0;
        }

        form {
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            text-align: center;
        }

        .image-preview-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .image-preview-container .delete-btn {
            position: absolute;
            top: 0;
            right: 0;
            background: red;
            color: white;
            border: none;
            cursor: pointer;
            padding: 5px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="my-4">Edit Produk</h1>

        <form method="POST" enctype="multipart/form-data" class="w-100">
            <!-- Hidden input for storing the image order -->
            <input type="hidden" name="image_order" id="image-order" value="0,1,2,3,4">
            <input type="hidden" name="deleted_images" id="deleted-images" value="">

            <!-- Image Upload Section -->
            <div class="mb-3">
                <label for="file-upload" class="form-label">Upload Gambar Produk (Max 5):</label>
                <input type="file" class="form-control" id="file-upload" name="product_image[]" accept="image/*" multiple>
            </div>

            <!-- Preview Section -->
            <div class="image-preview-container d-flex flex-wrap mb-3">
                <?php 
                $images = [$product_image1, $product_image2, $product_image3, $product_image4, $product_image5];
                foreach ($images as $index => $image): 
                    if (!empty($image)): ?>
                        <div class="image-preview position-relative" data-index="<?php echo $index; ?>">
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="Product Image">
                            <button type="button" class="delete-btn" onclick="deleteImage(this, <?php echo $index; ?>)">X</button>
                        </div>
                    <?php endif;
                endforeach; ?>
            </div>

            <!-- Other Product Details -->
            <div class="mb-3">
                <label for="name_product" class="form-label">Nama Produk:</label>
                <input type="text" class="form-control" id="name_product" name="name_product" value="<?php echo htmlspecialchars($name_product); ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi Produk:</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="harga" class="form-label">Harga:</label>
                <input type="number" class="form-control" id="harga" name="harga" value="<?php echo htmlspecialchars($harga); ?>" required>
            </div>

            <div class="mb-3">
                <label for="stok" class="form-label">Stok:</label>
                <input type="number" class="form-control" id="stok" name="stok" value="<?php echo htmlspecialchars($stok); ?>" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-danger w-100">Simpan Perubahan</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        const fileInput = document.getElementById('file-upload');
        const imagePreviewContainer = document.querySelector('.image-preview-container');
        const imageOrderInput = document.getElementById('image-order');
        const deletedImagesInput = document.getElementById('deleted-images');

        let currentImages = <?php echo count(array_filter($images)); ?>;
        let deletedImages = [];

        // Handle image preview after selecting files
        fileInput.addEventListener('change', function(event) {
            const files = event.target.files;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';

                    const imagePreview = document.createElement('div');
                    imagePreview.classList.add('image-preview', 'position-relative');
                    imagePreview.dataset.index = currentImages + i; // Assign new index for new images
                    imagePreview.appendChild(img);

                    const deleteBtn = document.createElement('button');
                    deleteBtn.classList.add('delete-btn', 'btn', 'btn-danger', 'position-absolute', 'top-0', 'end-0');
                    deleteBtn.textContent = 'X';
                    deleteBtn.addEventListener('click', function() {
                        imagePreview.remove();
                    });

                    imagePreview.appendChild(deleteBtn);
                    imagePreviewContainer.appendChild(imagePreview);
                }
                reader.readAsDataURL(file);
            }
        });

        // Initialize SortableJS for image preview container
        const sortable = new Sortable(imagePreviewContainer, {
            animation: 150,
            onEnd: function(evt) {
                // Update the image order in hidden input
                const sortedIndices = Array.from(imagePreviewContainer.children).map(child => child.dataset.index);
                imageOrderInput.value = sortedIndices.join(',');
            }
        });

        function deleteImage(button, index) {
            const imagePreview = button.closest('.image-preview');
            deletedImages.push(index); // Track deleted images
            deletedImagesInput.value = deletedImages.join(',');
            imagePreview.remove();

            const sortedIndices = Array.from(imagePreviewContainer.children).map(child => child.dataset.index);
            imageOrderInput.value = sortedIndices.join(',');
        }
    </script>

</body>
</html>
