let currentPage = 1;

function fetchProducts(page = 1) {
  fetch(`/path/to/your/php/file?page=${page}`)
    .then(response => response.json())
    .then(data => {
      const productList = document.getElementById("productList");
      productList.innerHTML = ""; // Clear existing content
      
      data.forEach(product => {
        const productCard = document.createElement("div");
        productCard.classList.add("product-card");
        
        productCard.innerHTML = `
          <img src="${product.product_image1}" alt="${product.name_product}">
          <div class="name">${product.name_product}</div>
          <div class="price">Rp. ${product.harga}</div>
          <div class="aksi">
            <button class="edit">✏️ Edit</button>
            <button class="delete">🗑️ Delete</button>
          </div>
        `;
        
        productList.appendChild(productCard);
      });
      
      document.getElementById("totalProducts").textContent = data.length; // Update total
    });
}

// Event listeners for pagination
document.getElementById("prevBtn").addEventListener("click", () => {
  if (currentPage > 1) {
    currentPage--;
    fetchProducts(currentPage);
  }
});

document.getElementById("nextBtn").addEventListener("click", () => {
  currentPage++;
  fetchProducts(currentPage);
});

// Initial fetch
fetchProducts(currentPage);
