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
  addManagerBtn.addEventListener("click", () => {
    // Reset form for add mode
    addManagerForm.reset();
    document.getElementById("add-user-id").value = "";
    document.getElementById("add-password").setAttribute("required", "");
    const title = document.querySelector("#add-manager-modal h2");
    const submitBtn = addManagerForm.querySelector('button[type="submit"]');
    const passwordLabel = document.querySelector('label[for="add-password"]');
    if (title) title.textContent = "Add New Manager";
    if (submitBtn) submitBtn.textContent = "Save Manager";
    if (passwordLabel) passwordLabel.textContent = "Password";
    openModal(addManagerModal);
  });
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
                        <button class="edit-btn text-blue-500 hover:text-blue-700 p-1 rounded" data-id="${
                          manager.user_id
                        }" title="Edit Manager">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="m18.5 2.5 a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button class="delete-btn text-red-500 hover:text-red-700 ml-2 p-1 rounded" data-id="${
                          manager.user_id
                        }" title="Delete Manager">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3,6 5,6 21,6"></polyline>
                                <path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,2h4a2,2 0 0,1 2,2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </button>
                    </td>
                `;
        managersTbody.appendChild(tr);
      });
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
        // Clear password field and make it optional for edit
        document.getElementById("add-password").value = "";
        document.getElementById("add-password").removeAttribute("required");
        // change modal title & submit text and password label
        const title = document.querySelector("#add-manager-modal h2");
        const submitBtn = addManagerForm.querySelector('button[type="submit"]');
        const passwordLabel = document.querySelector(
          'label[for="add-password"]'
        );
        if (title) title.textContent = "Edit Manager";
        if (submitBtn) submitBtn.textContent = "Update Manager";
        if (passwordLabel)
          passwordLabel.textContent = "New Password (optional)";
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
