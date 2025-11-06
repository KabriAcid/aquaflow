document.addEventListener("DOMContentLoaded", () => {
  const addManagerBtn = document.getElementById("add-manager-btn");
  const addManagerModal = document.getElementById("add-manager-modal");
  const cancelAddBtn = document.getElementById("cancel-add-btn");
  const addManagerForm = document.getElementById("add-manager-form");

  const editManagerModal = document.getElementById("edit-manager-modal");
  const cancelEditBtn = document.getElementById("cancel-edit-btn");
  const editManagerForm = document.getElementById("edit-manager-form");

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
  cancelEditBtn.addEventListener("click", () => closeModal(editManagerModal));
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
                        }"><i class="fas fa-edit"></i></button>
                        <button class="delete-btn text-red-500 hover:text-red-700 ml-2" data-id="${
                          manager.user_id
                        }"><i class="fas fa-trash"></i></button>
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

  // Add new manager
  addManagerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(addManagerForm);
    formData.append("role", "production_manager");

    try {
      const payload = Object.fromEntries(formData.entries());
      const response = await fetch(CREATE_URL, {
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
      } else {
        console.error("Error adding manager:", result && result.message);
        alert(
          "Failed to add manager: " +
            (result && result.message ? result.message : "Unknown error")
        );
      }
    } catch (error) {
      console.error("Error adding manager:", error);
      alert("Network error while adding manager");
    }
  });

  // Edit manager
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
        const data = res.data;
        document.getElementById("edit-user-id").value = data.user_id;
        document.getElementById("edit-name").value = data.name;
        document.getElementById("edit-email").value = data.email;
        document.getElementById("edit-phone").value = data.phone;
        openModal(editManagerModal);
      } catch (error) {
        console.error("Error fetching manager data:", error);
        alert("Network error while loading manager");
      }
    }
  });

  editManagerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(editManagerForm);
    const data = Object.fromEntries(formData.entries());

    try {
      const response = await fetch(UPDATE_URL, {
        method: "POST",
        credentials: "same-origin",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });
      const result = await response.json();

      if (result && result.success) {
        closeModal(editManagerModal);
        fetchManagers();
      } else {
        console.error("Update failed:", result && result.message);
        alert(
          "Failed to update manager: " +
            (result && result.message ? result.message : "Unknown error")
        );
      }
    } catch (error) {
      console.error("Error updating manager:", error);
      alert("Network error while updating manager");
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
