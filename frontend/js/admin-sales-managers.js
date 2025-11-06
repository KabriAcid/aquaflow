document.addEventListener("DOMContentLoaded", () => {
  const addManagerBtn = document.getElementById("add-manager-btn");
  const managerModal = document.getElementById("manager-modal");
  const cancelBtn = document.getElementById("cancel-btn");
  const managerForm = document.getElementById("manager-form");
  const modalTitle = document.getElementById("modal-title");
  const saveBtn = document.getElementById("save-btn");

  const deleteManagerModal = document.getElementById("delete-manager-modal");
  const cancelDeleteBtn = document.getElementById("cancel-delete-btn");
  const deleteManagerForm = document.getElementById("delete-manager-form");

  const managersTbody = document.getElementById("managers-tbody");

  const API_URL = "../../backend/api/users/get_all.php";
  const CREATE_URL = "../../backend/api/users/create.php";
  const UPDATE_URL = "../../backend/api/users/update.php";
  const DELETE_URL = "../../backend/api/users/delete.php";

  const openModal = (modal) => {
    modal.classList.remove("hidden");
    modal.classList.add("flex");
  };

  const closeModal = (modal) => {
    modal.classList.add("hidden");
    modal.classList.remove("flex");
  };

  const fetchManagers = async () => {
    try {
      const response = await fetch(API_URL, { credentials: "same-origin" });
      const { data } = await response.json();

      managersTbody.innerHTML = "";
      data
        .filter((user) => user.role === "sales_manager")
        .forEach((manager) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
                    <td class="p-3 border-b">${manager.username}</td>
                    <td class="p-3 border-b">${manager.email}</td>
                    <td class="p-3 border-b">${manager.phone || "N/A"}</td>
                    <td class="p-3 border-b">${new Date(
                      manager.created_at
                    ).toLocaleDateString()}</td>
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
                                <path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"></path>
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
    }
  };

  const prepareAddForm = () => {
    managerForm.reset();
    document.getElementById("user_id").value = "";
    modalTitle.textContent = "Add New Manager";
    saveBtn.textContent = "Save Manager";
    document.getElementById("password").required = true;
    openModal(managerModal);
  };

  const prepareEditForm = async (userId) => {
    try {
      const response = await fetch(`${API_URL}?user_id=${userId}`, {
        credentials: "same-origin",
      });
      const result = await response.json();

      // Handle both single user and array response
      const data = Array.isArray(result.data)
        ? result.data.find((user) => user.user_id == userId)
        : result.data;

      if (!data) {
        alert("Manager data not found");
        return;
      }

      managerForm.reset();
      document.getElementById("user_id").value = data.user_id || "";
      document.getElementById("name").value =
        data.username || data.full_name || "";
      document.getElementById("email").value = data.email || "";
      document.getElementById("phone").value = data.phone || "";
      document.getElementById("password").required = false;

      // Set state and city values if available
      if (data.state) {
        document.getElementById("state").value = data.state;
        // Use the global populateCities function from the inline script
        if (typeof populateCities === "function") {
          populateCities(data.state);
          // Set city after a brief delay to ensure cities are populated
          setTimeout(() => {
            if (data.city) {
              document.getElementById("city").value = data.city;
            }
          }, 100);
        }
      }

      modalTitle.textContent = "Edit Manager";
      saveBtn.textContent = "Update Manager";
      openModal(managerModal);
    } catch (error) {
      console.error("Error fetching manager data:", error);
      alert("Failed to load manager data");
    }
  };

  addManagerBtn.addEventListener("click", prepareAddForm);
  cancelBtn.addEventListener("click", () => closeModal(managerModal));

  managerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(managerForm);
    const data = Object.fromEntries(formData.entries());
    const userId = data.user_id;

    if (data.password !== data.confirm_password) {
      alert("Passwords do not match.");
      return;
    }

    // Map form fields to API expected fields
    const apiData = {
      username: data.name, // API expects 'username' but form sends 'name'
      email: data.email,
      password: data.password,
      role: "sales_manager",
      state: data.state,
      lga: data.city, // API expects 'lga' but form sends 'city'
      phone: data.phone,
    };

    // Add user_id for updates
    if (userId) {
      apiData.user_id = userId;
    }

    const url = userId ? UPDATE_URL : CREATE_URL;

    try {
      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify(apiData),
      });
      const result = await response.json();

      if (result.status === "success") {
        closeModal(managerModal);
        fetchManagers();
      } else {
        console.error("Operation failed:", result.message);
        alert(result.message || "Unknown error");
      }
    } catch (error) {
      console.error("Error saving manager:", error);
      alert("Network error while saving manager");
    }
  });

  managersTbody.addEventListener("click", (e) => {
    const editBtn = e.target.closest(".edit-btn");
    const deleteBtn = e.target.closest(".delete-btn");
    if (editBtn) {
      prepareEditForm(editBtn.dataset.id);
    } else if (deleteBtn) {
      document.getElementById("delete-user-id").value = deleteBtn.dataset.id;
      openModal(deleteManagerModal);
    }
  });

  cancelDeleteBtn.addEventListener("click", () =>
    closeModal(deleteManagerModal)
  );

  deleteManagerForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const userId = document.getElementById("delete-user-id").value;

    try {
      const response = await fetch(DELETE_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify({ user_id: userId }),
      });
      const result = await response.json();

      if (result.status === "success") {
        closeModal(deleteManagerModal);
        fetchManagers();
      } else {
        console.error("Deletion failed:", result.message);
      }
    } catch (error) {
      console.error("Error deleting manager:", error);
    }
  });

  fetchManagers();
});
