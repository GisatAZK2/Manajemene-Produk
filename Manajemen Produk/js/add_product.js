let imageCount = 0; // Track how many images are uploaded
const maxImages = 5; // Maximum number of allowed images
let emptySlots = []; // Track which slots are empty

function previewImage(event, index) {
    const image = document.getElementById('imagePreview' + index);
    const uploadText = document.getElementById('uploadText' + index);
    const removeBtn = document.getElementById('removeBtn' + index);
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function() {
        image.src = reader.result;
        image.style.display = 'block';
        uploadText.style.display = 'none';
        removeBtn.style.display = 'block';

        // Remove the slot from the emptySlots array since it is now filled
        emptySlots = emptySlots.filter(slot => slot !== index);

        imageCount++; // Increase the image count

        // Add a new upload box only if there are fewer than 5 images and no empty slots
        if (imageCount < maxImages && emptySlots.length === 0) {
            addNewUploadBox();
        }
    };

    if (file) {
        reader.readAsDataURL(file);
    }
}

function addNewUploadBox() {
    const cardContainer = document.getElementById('cardContainer');
    const newIndex = imageCount + 1; // Calculate next box index

    if (imageCount < maxImages) {
        const newBox = document.createElement('div');
        newBox.classList.add('upload-box');
        newBox.id = 'uploadBox' + newIndex;
        newBox.innerHTML = `
            <p id="uploadText${newIndex}">Tambahkan Foto (${imageCount + 1}/5)</p>
            <input type="file" name="product_image${newIndex}" accept="image/*" onchange="previewImage(event, ${newIndex})">
            <img id="imagePreview${newIndex}" alt="Foto Produk" style="display:none;">
            <button class="remove-btn" id="removeBtn${newIndex}" onclick="removeImage(${newIndex})" style="display:none;">x</button>
        `;
        cardContainer.appendChild(newBox);
    }
}

function removeImage(index) {
    const image = document.getElementById('imagePreview' + index);
    const uploadText = document.getElementById('uploadText' + index);
    const removeBtn = document.getElementById('removeBtn' + index);
    const fileInput = document.querySelector('#uploadBox' + index + ' input[type="file"]');

    // Reset the image preview
    image.src = '';
    image.style.display = 'none';
    uploadText.style.display = 'block';
    removeBtn.style.display = 'none';

    // Reset the file input
    fileInput.value = '';

    imageCount--; // Decrease the image count

    // Add this index back to emptySlots so it can be reused
    emptySlots.push(index);

    // Remove last empty upload box if needed
    const uploadBoxes = document.querySelectorAll('.upload-box');
    if (uploadBoxes.length > maxImages && !uploadBoxes[uploadBoxes.length - 1].querySelector('img').src) {
        uploadBoxes[uploadBoxes.length - 1].remove();
    }

    // Reset the interface if no images are left
    if (imageCount === 0) {
        resetUploadInterface();
    }
}

function resetUploadInterface() {
    // Reset image count and empty slots
    imageCount = 0;
    emptySlots = [];
    // Clear the upload container
    const cardContainer = document.getElementById('cardContainer');
    cardContainer.innerHTML = '';

    // Add the initial upload box
    addNewUploadBox();
}

// Add the first upload box on page load
document.addEventListener('DOMContentLoaded', resetUploadInterface);
