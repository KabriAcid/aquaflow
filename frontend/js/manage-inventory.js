document.addEventListener("DOMContentLoaded", function () {
  // DOM Elements
  const inventoryTableBody = document.getElementById("inventory-table-body");
  const loadingIndicator = document.getElementById("loading-indicator");
  const updateInventoryModal = document.getElementById(
    "update-inventory-modal"
  );
  const updateInventoryForm = document.getElementById("update-inventory-form");
  const cancelModalBtn = document.getElementById("cancel-modal-btn");
  const closeModalBtn = document.getElementById("close-modal-btn");

  // API Endpoint
  const API_INVENTORY = "/aquaflow/backend/api/production/inventory.php";

  // Modal handlers
  const openModal = () => {
    updateInventoryModal.classList.remove("hidden");
    updateInventoryModal.style.display = "flex";
    lucide.createIcons();
  };

  const closeModal = () => {
    updateInventoryModal.classList.add("hidden");
    updateInventoryModal.style.display = "none";
  };

  // Close modal when clicking outside
  updateInventoryModal.addEventListener("click", (e) => {
    if (e.target === updateInventoryModal) closeModal();
  });

  cancelModalBtn.addEventListener("click", closeModal);
  closeModalBtn.addEventListener("click", closeModal);

  // Function to load inventory data
  const loadInventory = async () => {
    try {
      loadingIndicator.classList.remove("hidden");
      inventoryTableBody.innerHTML = "";

      const response = await fetch(API_INVENTORY, {
        credentials: "same-origin",
      });
      const data = await response.json();

      if (!data.data || data.data.length === 0) {
        inventoryTableBody.innerHTML = `
          <tr>
            <td colspan="4" class="text-center py-8 text-gray-500">
              <i data-lucide="inbox" class="w-12 h-12 inline-block text-gray-300 mb-2"></i>
              <p class="mt-2">No inventory data available</p>
            </td>
          </tr>
        `;
        lucide.createIcons();
      } else {
        renderInventory(data.data);
      }

      loadingIndicator.classList.add("hidden");
    } catch (error) {
      console.error("Error fetching inventory:", error);
      loadingIndicator.classList.add("hidden");
      inventoryTableBody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center py-8">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 inline-block">
              <p class="text-red-700 font-semibold">Failed to load inventory</p>
              <p class="text-red-600 text-sm mt-1">${error.message}</p>
            </div>
          </td>
        </tr>
      `;
    }
  };

  // Function to render inventory table
  const renderInventory = (inventory) => {
    inventoryTableBody.innerHTML = "";

    inventory.forEach((item) => {
      const row = document.createElement("tr");
      row.className = "border-b border-gray-200 hover:bg-gray-50 transition";

      const lastUpdated = new Date(item.last_updated).toLocaleString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });

      const quantity = parseInt(item.quantity || 0).toLocaleString();

      row.innerHTML = `
        <td class="py-4 px-6 text-gray-800 font-medium">${
          item.product_name || "Unknown Product"
        }</td>
        <td class="py-4 px-6 text-right font-semibold text-lg text-blue-600">${quantity}</td>
        <td class="py-4 px-6 text-gray-600 text-sm">${lastUpdated}</td>
        <td class="py-4 px-6 text-center">
          <button class="update-stock-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition font-medium" data-product-id="${
            item.id
          }" data-product-name="${item.product_name}" data-quantity="${
        item.quantity
      }">
            Update
          </button>
        </td>
      `;

      inventoryTableBody.appendChild(row);
    });

    lucide.createIcons();
  };

  // Handle update stock button click
  inventoryTableBody.addEventListener("click", (e) => {
    const updateBtn = e.target.closest(".update-stock-btn");
    if (updateBtn) {
      const productId = updateBtn.dataset.productId;
      const productName = updateBtn.dataset.productName;
      const currentQuantity = updateBtn.dataset.quantity;

      document.getElementById("update-product-id").value = productId;
      document.getElementById("modal-product-name").textContent = productName;
      document.getElementById("update-quantity").value = currentQuantity;

      openModal();
    }
  });

  // Handle form submission
  updateInventoryForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const productId = document.getElementById("update-product-id").value;
    const newQuantity = document.getElementById("update-quantity").value;
    const submitBtn = updateInventoryForm.querySelector(
      'button[type="submit"]'
    );
    const originalText = submitBtn.innerHTML;

    try {
      submitBtn.disabled = true;
      submitBtn.innerHTML =
        '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Updating...';

      const response = await fetch(API_INVENTORY, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify({
          product_id: productId,
          quantity: parseInt(newQuantity),
        }),
      });

      const result = await response.json();

      if (result.status === 200 || result.status === "success") {
        // Show success message
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg";
        successMsg.innerHTML = `<i data-lucide="check-circle" class="w-5 h-5 inline"></i> ${
          result.message || "Stock updated successfully!"
        }`;
        document.body.appendChild(successMsg);
        lucide.createIcons();

        setTimeout(() => successMsg.remove(), 3000);

        closeModal();
        updateInventoryForm.reset();
        loadInventory();
      } else {
        alert("Failed to update stock: " + (result.message || "Unknown error"));
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error("Error updating inventory:", error);
      alert("An error occurred while updating stock: " + error.message);
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  });

  // Initial load
  loadInventory();
});
