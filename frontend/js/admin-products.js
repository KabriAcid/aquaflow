document.addEventListener("DOMContentLoaded", () => {
  // DOM Elements
  const addProductBtn = document.getElementById("add-product-btn");
  const productModal = document.getElementById("product-modal");
  const closeModalBtn = document.getElementById("close-modal-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const productForm = document.getElementById("product-form");
  const modalTitle = document.getElementById("modal-title");
  const saveBtn = document.getElementById("save-btn");
  const deleteProductModal = document.getElementById("delete-product-modal");
  const cancelDeleteBtn = document.getElementById("cancel-delete-btn");
  const deleteProductForm = document.getElementById("delete-product-form");
  const productsTbody = document.getElementById("products-tbody");
  const loadingIndicator = document.getElementById("loading-indicator");
  const noProductsMessage = document.getElementById("no-products-message");
  const searchFilter = document.getElementById("searchFilter");
  const categoryFilter = document.getElementById("categoryFilter");
  const productImageInput = document.getElementById("product-image");
  const previewImg = document.getElementById("preview-img");
  const previewIcon = document.getElementById("preview-icon");

  // API Endpoints
  const API_URL = "/aquaflow/backend/api/products/get_all.php";
  const CREATE_URL = "/aquaflow/backend/api/products/create.php";
  const UPDATE_URL = "/aquaflow/backend/api/products/update.php";
  const DELETE_URL = "/aquaflow/backend/api/products/delete.php";

  let allProducts = [];

  // Modal helpers
  const openModal = (modal) => {
    modal.classList.remove("hidden");
    modal.style.display = "flex";
    lucide.createIcons();
  };

  const closeModal = (modal) => {
    modal.classList.add("hidden");
    modal.style.display = "none";
  };

  // Image preview handler
  productImageInput.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = (event) => {
        previewImg.src = event.target.result;
        previewImg.classList.remove("hidden");
        previewIcon.classList.add("hidden");
      };
      reader.readAsDataURL(file);
    }
  });

  // Add product button
  addProductBtn.addEventListener("click", () => {
    modalTitle.textContent = "Add New Product";
    saveBtn.textContent = "Create Product";
    productForm.reset();
    previewImg.classList.add("hidden");
    previewIcon.classList.remove("hidden");
    document.getElementById("product-id").value = "";
    openModal(productModal);
  });

  // Close modal buttons
  closeModalBtn.addEventListener("click", () => closeModal(productModal));
  cancelBtn.addEventListener("click", () => closeModal(productModal));
  cancelDeleteBtn.addEventListener("click", () =>
    closeModal(deleteProductModal)
  );

  // Click outside modal to close
  productModal.addEventListener("click", (e) => {
    if (e.target === productModal) closeModal(productModal);
  });

  deleteProductModal.addEventListener("click", (e) => {
    if (e.target === deleteProductModal) closeModal(deleteProductModal);
  });

  // Fetch and render products
  const fetchProducts = async () => {
    try {
      loadingIndicator.classList.remove("hidden");
      productsTbody.innerHTML = "";

      const response = await fetch(API_URL, { credentials: "same-origin" });
      const data = await response.json();

      if (!data.data || data.data.length === 0) {
        noProductsMessage.classList.remove("hidden");
        loadingIndicator.classList.add("hidden");
        return;
      }

      allProducts = data.data;
      renderProducts(allProducts);
      loadingIndicator.classList.add("hidden");
    } catch (error) {
      console.error("Error fetching products:", error);
      loadingIndicator.classList.add("hidden");
      productsTbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-8 text-red-600">
                        <p>Failed to load products</p>
                    </td>
                </tr>
            `;
    }
  };

  const renderProducts = (products) => {
    productsTbody.innerHTML = "";
    noProductsMessage.classList.add("hidden");

    if (!products || products.length === 0) {
      noProductsMessage.classList.remove("hidden");
      return;
    }

    products.forEach((product) => {
      const row = document.createElement("tr");
      row.className = "border-b border-gray-200 hover:bg-gray-50 transition";

      const imageUrl =
        product.image_url === "default.png"
          ? "../../assets/images/default.png"
          : product.image_url || "../../assets/images/default.png";

      const statusBadge =
        product.status === "active"
          ? '<span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Active</span>'
          : '<span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">Inactive</span>';

      row.innerHTML = `
                <td class="py-4 px-6">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                        <img src="${imageUrl}" alt="${
        product.name
      }" class="w-full h-full object-cover" onerror="this.src='../../assets/images/default.png'">
                    </div>
                </td>
                <td class="py-4 px-6 text-gray-800 font-medium">${
                  product.name
                }</td>
                <td class="py-4 px-6 text-gray-600 text-sm">${
                  product.category
                }</td>
                <td class="py-4 px-6 text-gray-600 text-sm">${product.size} / ${
        product.volume
      }</td>
                <td class="py-4 px-6 text-right font-semibold text-blue-600">₦${parseFloat(
                  product.unit_price
                ).toFixed(2)}</td>
                <td class="py-4 px-6 text-center">${statusBadge}</td>
                <td class="py-4 px-6 text-center">
                    <div class="flex justify-center gap-2">
                        <button class="edit-btn bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded transition text-sm font-medium" data-id="${
                          product.id
                        }">
                            Edit
                        </button>
                        <button class="delete-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition text-sm font-medium" data-id="${
                          product.id
                        }">
                            Delete
                        </button>
                    </div>
                </td>
            `;

      productsTbody.appendChild(row);
    });

    lucide.createIcons();
  };

  // Filter and search
  const filterAndRender = () => {
    let filtered = allProducts;
    const searchTerm = searchFilter.value.toLowerCase();
    const category = categoryFilter.value;

    if (searchTerm) {
      filtered = filtered.filter((p) =>
        p.name.toLowerCase().includes(searchTerm)
      );
    }

    if (category) {
      filtered = filtered.filter((p) => p.category === category);
    }

    renderProducts(filtered);
  };

  searchFilter.addEventListener("input", filterAndRender);
  categoryFilter.addEventListener("change", filterAndRender);

  // Handle form submission
  productForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const productId = document.getElementById("product-id").value;
    const formData = new FormData(productForm);
    const data = {
      name: formData.get("name"),
      category: formData.get("category"),
      product_type: formData.get("product_type"),
      size: formData.get("size"),
      volume: formData.get("volume"),
      unit_price: formData.get("unit_price"),
      minimum_order_quantity: formData.get("minimum_order_quantity"),
      description: formData.get("description"),
      image_url: formData.get("image_url"),
    };

    if (productId) {
      data.id = productId;
    }

    const url = productId ? UPDATE_URL : CREATE_URL;
    const submitBtn = saveBtn;
    const originalText = submitBtn.innerHTML;

    try {
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Saving...';

      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (result.status === 200 || result.status === "success") {
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg";
        successMsg.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5 inline"></i> ${
          result.message || "Product saved successfully!"
        }`;
        document.body.appendChild(successMsg);
        lucide.createIcons();

        setTimeout(() => successMsg.remove(), 3000);

        closeModal(productModal);
        fetchProducts();
      } else {
        alert("Error: " + (result.message || "Failed to save product"));
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error("Error saving product:", error);
      alert("Error saving product: " + error.message);
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  });

  // Handle edit button click
  productsTbody.addEventListener("click", async (e) => {
    if (e.target.closest(".edit-btn")) {
      const productId = e.target.closest(".edit-btn").dataset.id;
      try {
        const product = allProducts.find((p) => p.id == productId);
        if (product) {
          document.getElementById("product-id").value = product.id;
          document.getElementById("product-name").value = product.name;
          document.getElementById("product-category").value = product.category;
          document.getElementById("product-type").value = product.product_type;
          document.getElementById("product-size").value = product.size;
          document.getElementById("product-volume").value = product.volume;
          document.getElementById("product-price").value = product.unit_price;
          document.getElementById("product-min-order").value =
            product.minimum_order_quantity;
          document.getElementById("product-description").value =
            product.description || "";

          if (product.image_url && product.image_url !== "default.png") {
            previewImg.src = product.image_url;
            previewImg.classList.remove("hidden");
            previewIcon.classList.add("hidden");
          } else {
            previewImg.classList.add("hidden");
            previewIcon.classList.remove("hidden");
          }

          modalTitle.textContent = "Edit Product";
          saveBtn.textContent = "Update Product";
          openModal(productModal);
        }
      } catch (error) {
        console.error("Error loading product:", error);
        alert("Failed to load product details");
      }
    } else if (e.target.closest(".delete-btn")) {
      const productId = e.target.closest(".delete-btn").dataset.id;
      document.getElementById("delete-product-id").value = productId;
      openModal(deleteProductModal);
    }
  });

  // Handle delete form submission
  deleteProductForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const productId = document.getElementById("delete-product-id").value;
    const submitBtn = deleteProductForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;

    try {
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Deleting...';

      const response = await fetch(DELETE_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify({ id: productId }),
      });

      const result = await response.json();

      if (result.status === 200 || result.status === "success") {
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg";
        successMsg.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5 inline"></i> Product deleted successfully!`;
        document.body.appendChild(successMsg);
        lucide.createIcons();

        setTimeout(() => successMsg.remove(), 3000);

        closeModal(deleteProductModal);
        fetchProducts();
      } else {
        alert("Error: " + (result.message || "Failed to delete product"));
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error("Error deleting product:", error);
      alert("Error deleting product: " + error.message);
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  });

  // Initial load
  fetchProducts();
});
