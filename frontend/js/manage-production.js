document.addEventListener("DOMContentLoaded", () => {
  // Main buttons and modal elements
  const addProductBtn = document.getElementById("add-product-btn");
  const productsGrid = document.getElementById("products-grid");

  // Add/Edit Product Modal
  const productModal = document.getElementById("product-modal");
  const productForm = document.getElementById("product-form");
  const cancelProductBtn = document.getElementById("cancel-product-btn");
  const closeProductModalBtn = document.getElementById("close-product-modal");
  const productModalTitle = document.getElementById("product-modal-title");

  // Image Upload Elements
  const imageUpload = document.getElementById("image_upload");
  const imagePreview = document.getElementById("image-preview");
  const previewImg = document.getElementById("preview-img");
  const clearImageBtn = document.getElementById("clear-image");

  // Record Production Modal
  const recordProductionModal = document.getElementById(
    "record-production-modal"
  );
  const recordProductionForm = document.getElementById(
    "record-production-form"
  );
  const cancelRecordBtn = document.getElementById("cancel-record-btn");
  const closeRecordModalBtn = document.getElementById("close-record-modal");
  const recordProductName = document.getElementById("record-product-name");
  const recordProductDisplay = document.getElementById(
    "record_product_display"
  );
  const productionDateInput = document.getElementById("production_date");
  const recordFeedback = document.getElementById("record-feedback");

  // API Endpoints - Use correct paths for API structure
  const API_PRODUCTS_GET = "/aquaflow/backend/api/products/get_all.php";
  const API_PRODUCTS_CREATE = "/aquaflow/backend/api/products/create.php";
  const API_PRODUCTION_RECORD =
    "/aquaflow/backend/api/production/production.php";

  // Store selected image file
  let selectedImageFile = null;

  // Generic modal handler
  const openModal = (modal) => {
    modal.classList.remove("hidden");
    modal.classList.add("flex", "items-center", "justify-center");
  };
  const closeModal = (modal) => {
    modal.classList.add("hidden");
    modal.classList.remove("flex", "items-center", "justify-center");
  };

  // ==================== IMAGE UPLOAD HANDLING ====================
  // Handle image file selection
  imageUpload.addEventListener("change", (e) => {
    const file = e.target.files[0];
    if (file && file.type.startsWith("image/")) {
      selectedImageFile = file;
      const reader = new FileReader();
      reader.onload = (event) => {
        previewImg.src = event.target.result;
        imagePreview.classList.remove("hidden");
      };
      reader.readAsDataURL(file);
    } else {
      alert("Please select a valid image file");
      imageUpload.value = "";
      selectedImageFile = null;
    }
  });

  // Handle clear image button
  clearImageBtn.addEventListener("click", () => {
    selectedImageFile = null;
    imageUpload.value = "";
    imagePreview.classList.add("hidden");
    previewImg.src = "";
  });

  // Handle drag and drop
  const uploadArea = document.querySelector(".border-dashed");
  if (uploadArea) {
    uploadArea.addEventListener("dragover", (e) => {
      e.preventDefault();
      uploadArea.classList.add("bg-blue-50", "border-blue-400");
    });

    uploadArea.addEventListener("dragleave", () => {
      uploadArea.classList.remove("bg-blue-50", "border-blue-400");
    });

    uploadArea.addEventListener("drop", (e) => {
      e.preventDefault();
      uploadArea.classList.remove("bg-blue-50", "border-blue-400");
      const files = e.dataTransfer.files;
      if (files[0] && files[0].type.startsWith("image/")) {
        imageUpload.files = files;
        imageUpload.dispatchEvent(new Event("change", { bubbles: true }));
      }
    });
  }

  // Upload image to server
  const uploadImage = async (file) => {
    const formData = new FormData();
    formData.append("image", file);

    try {
      const response = await fetch(
        "/aquaflow/backend/api/products/upload_image.php",
        {
          method: "POST",
          credentials: "same-origin",
          body: formData,
        }
      );
      const result = await response.json();
      if (result.status === "success" || result.status === 200) {
        return result.image_url || result.data;
      } else {
        throw new Error(result.message || "Failed to upload image");
      }
    } catch (error) {
      console.error("Error uploading image:", error);
      throw error;
    }
  };

  // Fetch and display all products
  const fetchProducts = async () => {
    try {
      const response = await fetch(API_PRODUCTS_GET, {
        credentials: "same-origin",
      });
      const result = await response.json();

      if (!result.data || result.data.length === 0) {
        productsGrid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <i data-lucide="package" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                        <p class="text-gray-500 text-lg">No products found</p>
                        <p class="text-gray-400 text-sm mt-1">Add your first product to get started</p>
                    </div>
                `;
        lucide.createIcons();
        return;
      }

      productsGrid.innerHTML = "";
      result.data.forEach((product) => {
        const imageUrl =
          product.image_url && product.image_url.trim()
            ? product.image_url
            : "../images/default-product.png";
        const productName = product.name || "Unnamed Product";
        const description = product.description || "No description provided";
        const price = parseFloat(product.price || 0).toLocaleString("en-NG", {
          style: "currency",
          currency: "NGN",
        });

        const card = document.createElement("div");
        card.className =
          "bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden flex flex-col group";
        card.innerHTML = `
                    <div class="relative h-48 overflow-hidden bg-gray-200">
                        <img src="${imageUrl}" alt="${productName}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </div>
                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="text-lg font-bold text-gray-800 truncate">${productName}</h3>
                        <p class="text-gray-600 text-sm line-clamp-2 flex-grow">${description}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-2xl font-bold text-blue-600">${price}</span>
                            <button class="record-production-btn bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg transition inline-flex items-center gap-1 text-sm font-medium" data-product-id="${product.id}" data-product-name="${productName}">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                Record
                            </button>
                        </div>
                    </div>
                `;
        productsGrid.appendChild(card);
      });
      lucide.createIcons();
    } catch (error) {
      console.error("Error fetching products:", error);
      productsGrid.innerHTML = `
                <div class="col-span-full">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                        <i data-lucide="alert-circle" class="w-12 h-12 text-red-500 mx-auto mb-3"></i>
                        <p class="text-red-700 font-semibold">Failed to load products</p>
                        <p class="text-red-600 text-sm mt-1">${error.message}</p>
                        <button type="button" onclick="location.reload()" class="mt-4 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                            Retry
                        </button>
                    </div>
                </div>
            `;
      lucide.createIcons();
    }
  };

  // --- Add/Edit Product Logic ---
  addProductBtn.addEventListener("click", () => {
    productForm.reset();
    document.getElementById("product_id").value = "";
    selectedImageFile = null;
    imageUpload.value = "";
    imagePreview.classList.add("hidden");
    productModalTitle.textContent = "Add New Product";
    openModal(productModal);
  });

  cancelProductBtn.addEventListener("click", () => closeModal(productModal));
  closeProductModalBtn.addEventListener("click", () =>
    closeModal(productModal)
  );

  // Close modal when clicking outside
  productModal.addEventListener("click", (e) => {
    if (e.target === productModal) closeModal(productModal);
  });

  productForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Disable submit button and show loading state
    const submitBtn = productForm.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Saving...';

    try {
      const formData = new FormData(productForm);
      const data = Object.fromEntries(formData.entries());

      let imageUrl = data.image_url || "";

      // Upload image if one was selected
      if (selectedImageFile) {
        try {
          imageUrl = await uploadImage(selectedImageFile);
        } catch (uploadError) {
          throw new Error("Image upload failed: " + uploadError.message);
        }
      }

      const response = await fetch(API_PRODUCTS_CREATE, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify({
          name: data.product_name,
          category: data.category,
          size: data.size,
          volume: data.volume,
          unit_price: parseFloat(data.unit_price),
          minimum_order_quantity: parseInt(data.min_order_qty) || 1,
          description: data.description,
          image_url: imageUrl,
        }),
      });
      const result = await response.json();

      if (
        (result.status === "success" || result.status === 200) &&
        result.data
      ) {
        // Show success message
        const successMsg = document.createElement("div");
        successMsg.className =
          "bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 mb-4 font-medium";
        successMsg.textContent =
          result.message || "Product saved successfully!";
        productForm.parentElement.insertBefore(successMsg, productForm);

        setTimeout(() => {
          selectedImageFile = null;
          closeModal(productModal);
          fetchProducts();
          successMsg.remove();
        }, 1500);
      } else {
        // Show error message
        alert("Failed to save product: " + (result.message || "Unknown error"));
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error("Error saving product:", error);
      alert("An error occurred while saving the product: " + error.message);
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  });

  // --- Record Production Logic ---
  productsGrid.addEventListener("click", (e) => {
    const recordBtn = e.target.closest(".record-production-btn");
    if (recordBtn) {
      const { productId, productName } = recordBtn.dataset;
      document.getElementById("record_product_id").value = productId;
      recordProductName.textContent = productName;
      recordProductDisplay.value = productName;

      // Set today's date by default
      const today = new Date().toISOString().split("T")[0];
      productionDateInput.value = today;

      recordProductionForm.reset();
      recordProductionForm.querySelector('input[name="product_id"]').value =
        productId;
      productionDateInput.value = today;
      recordFeedback.classList.add("hidden");
      openModal(recordProductionModal);
    }
  });

  cancelRecordBtn.addEventListener("click", () =>
    closeModal(recordProductionModal)
  );
  closeRecordModalBtn.addEventListener("click", () =>
    closeModal(recordProductionModal)
  );

  // Close modal when clicking outside
  recordProductionModal.addEventListener("click", (e) => {
    if (e.target === recordProductionModal) closeModal(recordProductionModal);
  });

  recordProductionForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Disable submit button
    const submitBtn = recordProductionForm.querySelector(
      'button[type="submit"]'
    );
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Recording...';

    const formData = new FormData(recordProductionForm);
    const data = Object.fromEntries(formData.entries());

    try {
      const response = await fetch(API_PRODUCTION_RECORD, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify(data),
      });
      const result = await response.json();

      recordFeedback.innerHTML = result.message;
      recordFeedback.classList.remove("hidden");

      if (result.status === 200 || result.status === "success") {
        recordFeedback.className =
          "mt-4 p-4 rounded-lg text-sm font-medium bg-green-100 text-green-700 border border-green-300";
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        setTimeout(() => {
          closeModal(recordProductionModal);
          fetchProducts(); // Refresh product list if needed
        }, 2000);
      } else {
        recordFeedback.className =
          "mt-4 p-4 rounded-lg text-sm font-medium bg-red-100 text-red-700 border border-red-300";
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error("Error recording production:", error);
      recordFeedback.innerHTML =
        "An unexpected error occurred. Please try again.";
      recordFeedback.className =
        "mt-4 p-4 rounded-lg text-sm font-medium bg-red-100 text-red-700 border border-red-300";
      recordFeedback.classList.remove("hidden");
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  });

  // Initial Load
  fetchProducts();
});
