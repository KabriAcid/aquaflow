document.addEventListener("DOMContentLoaded", () => {
  // DOM Elements
  const addUserBtn = document.getElementById("add-user-btn");
  const userModal = document.getElementById("user-modal");
  const closeModalBtn = document.getElementById("close-modal-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const userForm = document.getElementById("user-form");
  const modalTitle = document.getElementById("modal-title");
  const saveBtn = document.getElementById("save-btn");
  const deleteUserModal = document.getElementById("delete-user-modal");
  const cancelDeleteBtn = document.getElementById("cancel-delete-btn");
  const deleteUserForm = document.getElementById("delete-user-form");
  const usersTbody = document.getElementById("users-tbody");
  const loadingIndicator = document.getElementById("loading-indicator");
  const noUsersMessage = document.getElementById("no-users-message");
  const passwordField = document.getElementById("password");
  const passwordRequired = document.getElementById("password-required");

  // API Endpoints
  const API_URL = "/aquaflow/backend/api/users/get_all.php";
  const CREATE_URL = "/aquaflow/backend/api/users/create.php";
  const UPDATE_URL = "/aquaflow/backend/api/users/update.php";
  const DELETE_URL = "/aquaflow/backend/api/users/delete.php";

  let allUsers = [];
  let statesData = {};

  // Modal helpers
  const openModal = (modal) => {
    modal.classList.remove("hidden");
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
    document.body.style.overflow = "hidden";
    lucide.createIcons();
  };

  const closeModal = (modal) => {
    modal.classList.add("hidden");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  };

  // Load states data
  const loadStatesData = async () => {
    try {
      const response = await fetch("/aquaflow/backend/data/states_cities.json");
      statesData = await response.json();
      populateStates();
    } catch (error) {
      console.error("Error loading states data:", error);
    }
  };

  // Populate states dropdown
  const populateStates = () => {
    const stateSelect = document.getElementById("state");
    stateSelect.innerHTML = '<option value="">Select State</option>';

    for (const [stateId, stateInfo] of Object.entries(statesData)) {
      const option = document.createElement("option");
      option.value = stateId;
      option.textContent = stateInfo.name;
      stateSelect.appendChild(option);
    }
  };

  // Populate cities based on selected state
  const populateCities = (selectedState) => {
    const citySelect = document.getElementById("city");
    citySelect.innerHTML = '<option value="">Select City</option>';

    if (selectedState && statesData[selectedState]) {
      const cities = statesData[selectedState].cities || [];
      cities.forEach((city) => {
        const option = document.createElement("option");
        option.value = city.id;
        option.textContent = city.name;
        citySelect.appendChild(option);
      });
    }
  };

  // Add user button
  addUserBtn.addEventListener("click", () => {
    modalTitle.textContent = "Add New User";
    saveBtn.textContent = "Create User";
    saveBtn.classList.remove("bg-amber-600", "hover:bg-amber-700");
    saveBtn.classList.add("bg-blue-600", "hover:bg-blue-700");
    passwordRequired.textContent = "*";
    passwordField.required = true;
    userForm.reset();
    document.getElementById("user-id").value = "";
    openModal(userModal);
  });

  // Close modal buttons
  closeModalBtn.addEventListener("click", () => closeModal(userModal));
  cancelBtn.addEventListener("click", () => closeModal(userModal));
  cancelDeleteBtn.addEventListener("click", () => closeModal(deleteUserModal));

  // Click outside modal to close
  userModal.addEventListener("click", (e) => {
    if (e.target === userModal) closeModal(userModal);
  });

  deleteUserModal.addEventListener("click", (e) => {
    if (e.target === deleteUserModal) closeModal(deleteUserModal);
  });

  // State change listener
  document.getElementById("state").addEventListener("change", function () {
    populateCities(this.value);
  });

  // Fetch and render users
  const fetchUsers = async () => {
    try {
      loadingIndicator.classList.remove("hidden");
      usersTbody.innerHTML = "";
      noUsersMessage.classList.add("hidden");

      const response = await fetch(API_URL, { credentials: "same-origin" });
      const data = await response.json();

      console.log("API Response:", data, "Status:", response.status);

      if (!response.ok) {
        throw new Error(
          data.message || `HTTP ${response.status}: Failed to fetch users`
        );
      }

      if (!data.data || !Array.isArray(data.data)) {
        noUsersMessage.classList.remove("hidden");
        loadingIndicator.classList.add("hidden");
        return;
      }

      if (data.data.length === 0) {
        noUsersMessage.classList.remove("hidden");
        loadingIndicator.classList.add("hidden");
        return;
      }

      allUsers = data.data;
      renderUsers(allUsers);
      loadingIndicator.classList.add("hidden");
    } catch (error) {
      console.error("Error fetching users:", error);
      loadingIndicator.classList.add("hidden");
      usersTbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-8">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 inline-block">
                            <p class="text-red-700 font-semibold">Failed to load users</p>
                            <p class="text-red-600 text-sm mt-1">${error.message}</p>
                        </div>
                    </td>
                </tr>
            `;
    }
  };

  const renderUsers = (users) => {
    usersTbody.innerHTML = "";
    noUsersMessage.classList.add("hidden");

    if (!users || users.length === 0) {
      noUsersMessage.classList.remove("hidden");
      return;
    }

    users.forEach((user, index) => {
      const row = document.createElement("tr");
      row.className = "border-b border-gray-200 hover:bg-gray-50 transition";

      const statusColors = {
        active: "bg-green-100 text-green-800",
        inactive: "bg-gray-100 text-gray-800",
        suspended: "bg-red-100 text-red-800",
      };

      const statusBadge = `<span class="${
        statusColors[user.status] || "bg-gray-100 text-gray-800"
      } px-3 py-1 rounded-full text-xs font-semibold">${
        user.status
          ? user.status.charAt(0).toUpperCase() + user.status.slice(1)
          : "Unknown"
      }</span>`;

      row.innerHTML = `
                <td class="py-4 px-6 text-center text-gray-600 font-semibold text-sm">${
                  index + 1
                }</td>
                <td class="py-4 px-6 text-gray-800 font-medium">${
                  user.full_name || "N/A"
                }</td>
                <td class="py-4 px-6 text-gray-600 text-sm">${
                  user.email || "N/A"
                }</td>
                <td class="py-4 px-6 text-gray-600 text-sm">${
                  user.phone || "N/A"
                }</td>
                <td class="py-4 px-6 text-gray-600 text-sm">${
                  user.state || "N/A"
                }</td>
                <td class="py-4 px-6 text-gray-600 text-sm">${
                  user.city || "N/A"
                }</td>
                <td class="py-4 px-6 text-center">${statusBadge}</td>
                <td class="py-4 px-6 text-center">
                    <div class="flex justify-center gap-3">
                        <button class="edit-btn text-amber-500 hover:text-amber-700 transition p-1 hover:bg-amber-50 rounded" title="Edit user" data-id="${
                          user.id
                        }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-4l7-7m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                        </button>
                        <button class="delete-btn text-red-500 hover:text-red-700 transition p-1 hover:bg-red-50 rounded" title="Delete user" data-id="${
                          user.id
                        }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            `;

      usersTbody.appendChild(row);
    });

    lucide.createIcons();
  };

  // Handle form submission
  saveBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    const userId = document.getElementById("user-id").value;
    const formData = new FormData(userForm);
    const data = {
      full_name: formData.get("name"),
      email: formData.get("email"),
      phone: formData.get("phone") || null,
      state: formData.get("state") || null,
      city: formData.get("city") || null,
      status: formData.get("status"),
    };

    // Only include password if provided
    const password = formData.get("password");
    if (password) {
      data.password = password;
    }

    if (userId) {
      data.id = userId;
    }

    const url = userId ? UPDATE_URL : CREATE_URL;
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

      if (result.success === true) {
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg z-50";
        successMsg.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ${
          result.message || "User saved successfully!"
        }`;
        document.body.appendChild(successMsg);

        setTimeout(() => successMsg.remove(), 3000);

        closeModal(userModal);
        fetchUsers();
      } else {
        alert("Error: " + (result.message || "Failed to save user"));
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
      }
    } catch (error) {
      console.error("Error saving user:", error);
      alert("Error saving user: " + error.message);
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    }
  });

  // Handle edit button click
  usersTbody.addEventListener("click", async (e) => {
    if (e.target.closest(".edit-btn")) {
      const userId = e.target.closest(".edit-btn").dataset.id;
      try {
        const user = allUsers.find((u) => u.id == userId);
        if (user) {
          document.getElementById("user-id").value = user.id || "";
          document.getElementById("name").value = user.full_name || "";
          document.getElementById("email").value = user.email || "";
          document.getElementById("phone").value = user.phone || "";
          document.getElementById("status").value = user.status || "";

          // Set state and city
          if (user.state) {
            document.getElementById("state").value = user.state;
            populateCities(user.state);
            setTimeout(() => {
              if (user.city) {
                document.getElementById("city").value = user.city;
              }
            }, 100);
          }

          modalTitle.textContent = "Edit User";
          saveBtn.textContent = "Update User";
          saveBtn.classList.remove("bg-blue-600", "hover:bg-blue-700");
          saveBtn.classList.add("bg-amber-600", "hover:bg-amber-700");
          passwordRequired.textContent = "";
          passwordField.required = false;
          passwordField.value = "";
          console.log("Opening modal for user:", user);
          openModal(userModal);
        } else {
          console.warn("User not found in allUsers:", userId);
        }
      } catch (error) {
        console.error("Error loading user:", error);
        alert("Failed to load user details");
      }
    } else if (e.target.closest(".delete-btn")) {
      const userId = e.target.closest(".delete-btn").dataset.id;
      const user = allUsers.find((u) => u.id == userId);

      if (
        confirm(
          `Are you sure you want to delete "${
            user?.full_name || "this user"
          }"? This action cannot be undone.`
        )
      ) {
        try {
          const response = await fetch(DELETE_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            credentials: "same-origin",
            body: JSON.stringify({ id: userId }),
          });

          const result = await response.json();

          if (result.success === true) {
            const successMsg = document.createElement("div");
            successMsg.className =
              "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg z-50";
            successMsg.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ${
              user?.full_name || "User"
            } deleted successfully!`;
            document.body.appendChild(successMsg);

            setTimeout(() => successMsg.remove(), 3000);
            fetchUsers();
          } else {
            alert("Error: " + (result.message || "Failed to delete user"));
          }
        } catch (error) {
          console.error("Error deleting user:", error);
          alert("Error deleting user: " + error.message);
        }
      }
    }
  });

  // Initial load
  loadStatesData();
  fetchUsers();
});
