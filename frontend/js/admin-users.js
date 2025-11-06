document.addEventListener("DOMContentLoaded", () => {
  const addUserBtn = document.getElementById("add-user-btn");
  const userModal = document.getElementById("user-modal");
  const cancelBtn = document.getElementById("cancel-btn");
  const userForm = document.getElementById("user-form");
  const modalTitle = document.getElementById("modal-title");
  const saveBtn = document.getElementById("save-btn");

  const deleteUserModal = document.getElementById("delete-user-modal");
  const cancelDeleteBtn = document.getElementById("cancel-delete-btn");
  const deleteUserForm = document.getElementById("delete-user-form");

  const usersTbody = document.getElementById("users-tbody");

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

  const prepareAddForm = () => {
    userForm.reset();
    document.getElementById("user-id").value = "";
    modalTitle.textContent = "Add New User";
    saveBtn.textContent = "Save User";
    document.getElementById("password").required = true;
    openModal(userModal);
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
        alert("User data not found");
        return;
      }

      userForm.reset();
      document.getElementById("user-id").value = data.user_id || "";
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
          setTimeout(() => {
            if (data.city) {
              document.getElementById("city").value = data.city;
            }
          }, 100);
        }
      }

      modalTitle.textContent = "Edit User";
      saveBtn.textContent = "Update User";
      openModal(userModal);
    } catch (error) {
      console.error("Error fetching user data:", error);
      alert("Failed to load user data");
    }
  };

  addUserBtn.addEventListener("click", prepareAddForm);
  cancelBtn.addEventListener("click", () => closeModal(userModal));
  cancelDeleteBtn.addEventListener("click", () => closeModal(deleteUserModal));

  const fetchUsers = async () => {
    try {
      const response = await fetch(API_URL, { credentials: "same-origin" });
      const { data } = await response.json();

      usersTbody.innerHTML = "";
      data
        .filter((user) => user.role === "customer")
        .forEach((user) => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
                    <td class="p-3 border-b">${
                      user.username || user.full_name || ""
                    }</td>
                    <td class="p-3 border-b">${user.email}</td>
                    <td class="p-3 border-b">${user.phone || "N/A"}</td>
                    <td class="p-3 border-b">${new Date(
                      user.created_at
                    ).toLocaleDateString()}</td>
                    <td class="p-3 border-b">
                        <button class="edit-btn text-blue-500 hover:text-blue-700 p-1 rounded" data-id="${
                          user.user_id
                        }" title="Edit User">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="m18.5 2.5 a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                        </button>
                        <button class="delete-btn text-red-500 hover:text-red-700 ml-2 p-1 rounded" data-id="${
                          user.user_id
                        }" title="Delete User">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3,6 5,6 21,6"></polyline>
                                <path d="m19,6v14a2,2 0 0,1 -2,2H7a2,2 0 0,1 -2,-2V6m3,0V4a2,2 0 0,1 2,-2h4a2,2 0 0,1 2,2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </button>
                    </td>
                `;
          usersTbody.appendChild(tr);
        });
    } catch (error) {
      console.error("Error fetching users:", error);
    }
  };

  userForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(userForm);
    const data = Object.fromEntries(formData.entries());
    const userId = data.user_id;

    // Map form fields to API expected fields
    const apiData = {
      username: data.name, // API expects 'username' but form sends 'name'
      email: data.email,
      password: data.password,
      role: "customer",
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
        closeModal(userModal);
        fetchUsers();
        userForm.reset();
      } else {
        console.error("Operation failed:", result.message);
        alert("Operation failed: " + (result.message || "Unknown error"));
      }
    } catch (error) {
      console.error("Error saving user:", error);
      alert("Network error while saving user");
    }
  });

  usersTbody.addEventListener("click", (e) => {
    const editBtn = e.target.closest(".edit-btn");
    const deleteBtn = e.target.closest(".delete-btn");
    if (editBtn) {
      prepareEditForm(editBtn.dataset.id);
    } else if (deleteBtn) {
      document.getElementById("delete-user-id").value = deleteBtn.dataset.id;
      openModal(deleteUserModal);
    }
  });

  deleteUserForm.addEventListener("submit", async (e) => {
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
        closeModal(deleteUserModal);
        fetchUsers();
      } else {
        console.error("Deletion failed:", result.message);
        alert("Failed to delete user: " + (result.message || "Unknown error"));
      }
    } catch (error) {
      console.error("Error deleting user:", error);
      alert("Network error while deleting user");
    }
  });

  fetchUsers();
});
