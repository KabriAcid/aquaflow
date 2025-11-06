document.addEventListener("DOMContentLoaded", () => {
  const addManagerBtn = document.getElementById("add-manager-btn");
  const addManagerModal = document.getElementById("add-manager-modal");
  const cancelAddBtn = document.getElementById("cancel-add-btn");
  const addManagerForm = document.getElementById("add-manager-form");

  const deleteManagerModal = document.getElementById("delete-manager-modal");
  const cancelDeleteBtn = document.getElementById("cancel-delete-btn");
  const deleteManagerForm = document.getElementById("delete-manager-form");

  const managersTbody = document.getElementById("managers-tbody");

  const API_URL = "../../backend/api/users/production_managers_get.php";
  const CREATE_URL = "../../backend/api/users/production_managers_create.php";
  const UPDATE_URL = "../../backend/api/users/production_managers_update.php";
  const DELETE_URL = "../../backend/api/users/production_managers_delete.php";

  // Function to open modal
  const openModal = (modal) => {
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  };

  // Function to close modal
  const closeModal = (modal) => {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  };

  // Event listeners for modals
  addManagerBtn.addEventListener("click", () => openModal(addManagerModal));
  cancelAddBtn.addEventListener("click", () => closeModal(addManagerModal));
  cancelDeleteBtn.addEventListener("click", () =>
    closeModal(deleteManagerModal)
  );

  // Fetch and display managers
  const fetchManagers = async () => {
    try {
      const response = await fetch(API_URL, { credentials: "same-origin" });
      const res = await response.json();
      if (!res || !res.success) {
        console.error("Failed to fetch managers", res && res.message);
        managersTbody.innerHTML =
          '<tr><td colspan="5" class="p-6 text-center text-red-600">Failed to load production managers</td></tr>';
        return;
      }

      const data = res.data || [];
      managersTbody.innerHTML = "";
      data.forEach((manager) => {
        const tr = document.createElement("tr");
        const created = manager.created_at
          ? new Date(manager.created_at).toLocaleDateString()
          : "";
        tr.innerHTML = `
                    <td class="p-3 border-b">${manager.name}</td>
                    <td class="p-3 border-b">${manager.email}</td>
                    <td class="p-3 border-b">${manager.phone || "N/A"}</td>
                    <td class="p-3 border-b">${created}</td>
                    <td class="p-3 border-b">
                        <button class="edit-btn text-blue-500 hover:text-blue-700" data-id="${
                          manager.user_id
                        }" aria-label="Edit"><i data-lucide="edit-2" class="w-4 h-4" aria-hidden="true"></i></button>
                        <button class="delete-btn text-red-500 hover:text-red-700 ml-2" data-id="${
                          manager.user_id
                        }" aria-label="Delete"><i data-lucide="trash-2" class="w-4 h-4" aria-hidden="true"></i></button>
                    </td>
                `;
        managersTbody.appendChild(tr);
      });
      // initialize lucide icons inside newly added rows
      if (window.lucide && typeof window.lucide.replace === "function") {
        window.lucide.replace();
      }
    } catch (error) {
      console.error("Error fetching managers:", error);
      managersTbody.innerHTML =
        '<tr><td colspan="5" class="p-6 text-center text-red-600">Network error</td></tr>';
    }
  };

  // Add or Update manager (reuse add modal for edits)
  addManagerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(addManagerForm);
    formData.append("role", "production_manager");

    try {
      const payload = Object.fromEntries(formData.entries());

      // normalize keys
      if (payload.name && !payload.username) payload.username = payload.name;
      if (payload.lga && !payload.city) payload.city = payload.lga;

      const isEdit = payload.user_id && payload.user_id.length > 0;
      const url = isEdit ? UPDATE_URL : CREATE_URL;

      const response = await fetch(url, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
      });
      const result = await response.json();
      if (result && result.success) {
        closeModal(addManagerModal);
        fetchManagers();
        addManagerForm.reset();
        // reset modal title/button
        const title = document.querySelector("#add-manager-modal h2");
        const submitBtn = addManagerForm.querySelector('button[type="submit"]');
        if (title) title.textContent = "Add New Manager";
        if (submitBtn) submitBtn.textContent = "Save Manager";
        document.getElementById("add-user-id").value = "";
      } else {
        console.error(
          isEdit ? "Error updating manager:" : "Error adding manager:",
          result && result.message
        );
        alert(
          (isEdit ? "Failed to update manager: " : "Failed to add manager: ") +
            (result && result.message ? result.message : "Unknown error")
        );
      }
    } catch (error) {
      console.error("Error saving manager:", error);
      alert("Network error while saving manager");
    }
  });

  // Edit manager: reuse the add modal
  managersTbody.addEventListener("click", async (e) => {
    if (e.target.closest(".edit-btn")) {
      const userId = e.target.closest(".edit-btn").dataset.id;
      try {
        const response = await fetch(`${API_URL}?user_id=${userId}`, {
          credentials: "same-origin",
        });
        const res = await response.json();
        if (!res || !res.success) {
          console.error("Failed to fetch manager data", res && res.message);
          alert("Failed to load manager details");
          return;
        }
        // Handle both array and object responses
        const data = Array.isArray(res.data) ? res.data[0] : res.data;
        if (!data) {
          alert("Manager data not found");
          return;
        }
        // populate add form fields
        document.getElementById("add-user-id").value = data.user_id || "";
        document.getElementById("add-name").value = data.name || "";
        document.getElementById("add-email").value = data.email || "";
        document.getElementById("add-phone").value = data.phone || "";
        document.getElementById("add-lga").value = data.city || "";
        document.getElementById("add-state").value = data.state || "";
        // change modal title & submit text
        const title = document.querySelector("#add-manager-modal h2");
        const submitBtn = addManagerForm.querySelector('button[type="submit"]');
        if (title) title.textContent = "Edit Manager";
        if (submitBtn) submitBtn.textContent = "Update Manager";
        openModal(addManagerModal);
      } catch (error) {
        console.error("Error fetching manager data:", error);
        alert("Network error while loading manager");
      }
    }
  });

  // Delete manager
  managersTbody.addEventListener("click", (e) => {
    if (e.target.closest(".delete-btn")) {
      const userId = e.target.closest(".delete-btn").dataset.id;
      document.getElementById("delete-user-id").value = userId;
      openModal(deleteManagerModal);
    }
  });

  deleteManagerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const userId = document.getElementById("delete-user-id").value;

    try {
      const response = await fetch(DELETE_URL, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ user_id: userId }),
      });
      const result = await response.json();

      if (result && result.success) {
        closeModal(deleteManagerModal);
        fetchManagers();
      } else {
        console.error("Deletion failed:", result && result.message);
        alert(
          "Failed to delete manager: " +
            (result && result.message ? result.message : "Unknown error")
        );
      }
    } catch (error) {
      console.error("Error deleting manager:", error);
    }
  });

  fetchManagers();
});
