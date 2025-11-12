document.addEventListener("DOMContentLoaded", () => {
  // DOM Elements
  const generalSettingsForm = document.getElementById("general-settings-form");
  const settingsNav = document.querySelectorAll(".settings-nav");
  const settingsContent = document.querySelectorAll(".settings-content");
  const addSalesBtn = document.getElementById("add-sales-mgr-btn");
  const addProdBtn = document.getElementById("add-prod-mgr-btn");
  const managerModal = document.getElementById("manager-modal");
  const managerForm = document.getElementById("manager-form");
  const closeManagerBtn = document.getElementById("close-manager-modal-btn");
  const cancelManagerBtn = document.getElementById("cancel-manager-btn");
  const saveManagerBtn = document.getElementById("save-manager-btn");
  const managerModalTitle = document.getElementById("manager-modal-title");
  const passwordRequiredSpan = document.getElementById("password-required-mgr");

  // API Endpoints
  const SETTINGS_GET_URL = "/aquaflow/backend/api/settings/get.php";
  const SETTINGS_UPDATE_URL = "/aquaflow/backend/api/settings/update.php";
  const USERS_BY_ROLE_URL = "/aquaflow/backend/api/users/get_by_role.php";
  const USERS_CREATE_URL = "/aquaflow/backend/api/users/create.php";
  const USERS_UPDATE_URL = "/aquaflow/backend/api/users/update.php";
  const USERS_DELETE_URL = "/aquaflow/backend/api/users/delete.php";

  let currentManagerType = null;
  let allManagers = {};

  // Modal helpers
  const openModal = (modal) => {
    modal.classList.remove("hidden");
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
    document.body.style.overflow = "hidden";
  };

  const closeModal = (modal) => {
    modal.classList.add("hidden");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
  };

  // Tab navigation
  settingsNav.forEach((button) => {
    button.addEventListener("click", () => {
      const tabName = button.dataset.tab;

      // Remove active from all buttons
      settingsNav.forEach((b) =>
        b.classList.remove("active", "bg-blue-50", "text-blue-600")
      );
      // Add active to clicked button
      button.classList.add("active", "bg-blue-50", "text-blue-600");

      // Hide all content
      settingsContent.forEach((content) => content.classList.add("hidden"));
      // Show selected content
      document
        .querySelector(`.settings-content[data-tab="${tabName}"]`)
        .classList.remove("hidden");

      // Load data if needed
      if (tabName === "sales" || tabName === "production") {
        fetchManagersByRole(
          tabName === "sales" ? "sales_manager" : "production_manager"
        );
      }
    });
  });

  // Load general settings
  const loadGeneralSettings = async () => {
    try {
      const response = await fetch(SETTINGS_GET_URL, {
        credentials: "same-origin",
      });
      const result = await response.json();

      if (result.success && result.data) {
        const settings = result.data;
        document.getElementById("company_name").value =
          settings.company_name || "";
        document.getElementById("company_email").value =
          settings.company_email || "";
        document.getElementById("company_phone").value =
          settings.company_phone || "";
        document.getElementById("company_address").value =
          settings.company_address || "";
        document.getElementById("delivery_fee").value =
          settings.delivery_fee || "";
        document.getElementById("minimum_order").value =
          settings.minimum_order || "";
      }
    } catch (error) {
      console.error("Error loading settings:", error);
    }
  };

  // Handle general settings form
  generalSettingsForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const data = {
      company_name: document.getElementById("company_name").value,
      company_email: document.getElementById("company_email").value,
      company_phone: document.getElementById("company_phone").value,
      company_address: document.getElementById("company_address").value,
      delivery_fee: document.getElementById("delivery_fee").value,
      minimum_order: document.getElementById("minimum_order").value,
    };

    try {
      const response = await fetch(SETTINGS_UPDATE_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify(data),
      });
      const result = await response.json();

      if (result.success) {
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg z-50";
        successMsg.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Settings saved successfully!`;
        document.body.appendChild(successMsg);
        setTimeout(() => successMsg.remove(), 3000);
      }
    } catch (error) {
      console.error("Error saving settings:", error);
      alert("Error saving settings");
    }
  });

  // Fetch managers by role
  const fetchManagersByRole = async (role) => {
    try {
      const response = await fetch(`${USERS_BY_ROLE_URL}?role=${role}`, {
        credentials: "same-origin",
      });
      const result = await response.json();

      if (result.success) {
        allManagers[role] = result.data || [];
        renderManagers(role);
      }
    } catch (error) {
      console.error("Error fetching managers:", error);
    }
  };

  // Render managers in table
  const renderManagers = (role) => {
    const tbody =
      role === "sales_manager"
        ? document.getElementById("sales-mgr-tbody")
        : document.getElementById("prod-mgr-tbody");
    const noMsg =
      role === "sales_manager"
        ? document.getElementById("no-sales-msg")
        : document.getElementById("no-prod-msg");
    const managers = allManagers[role] || [];

    tbody.innerHTML = "";
    noMsg.classList.add("hidden");

    if (managers.length === 0) {
      noMsg.classList.remove("hidden");
      return;
    }

    managers.forEach((manager, index) => {
      const statusColor =
        manager.status === "active"
          ? "bg-green-100 text-green-800"
          : "bg-gray-100 text-gray-800";
      const statusText = manager.status
        ? manager.status.charAt(0).toUpperCase() + manager.status.slice(1)
        : "Unknown";

      const row = document.createElement("tr");
      row.className = "border-b border-gray-200 hover:bg-gray-50 transition";
      row.innerHTML = `
                <td class="py-4 px-6 text-center text-gray-600 font-semibold text-sm">${
                  index + 1
                }</td>
                <td class="py-4 px-6 text-gray-800 font-medium">${
                  manager.full_name || "N/A"
                }</td>
                <td class="py-4 px-6 text-gray-600 text-sm">${
                  manager.email || "N/A"
                }</td>
                <td class="py-4 px-6 text-gray-600 text-sm">${
                  manager.phone || "N/A"
                }</td>
                <td class="py-4 px-6 text-center">
                    <span class="${statusColor} px-3 py-1 rounded-full text-xs font-semibold">${statusText}</span>
                </td>
                <td class="py-4 px-6 text-center">
                    <div class="flex justify-center gap-3">
                        <button class="edit-mgr-btn text-amber-500 hover:text-amber-700 transition p-1 hover:bg-amber-50 rounded" data-id="${
                          manager.id
                        }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7-4l7-7m0 0l4-4m-4 4l-4-4"></path>
                            </svg>
                        </button>
                        <button class="delete-mgr-btn text-red-500 hover:text-red-700 transition p-1 hover:bg-red-50 rounded" data-id="${
                          manager.id
                        }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </td>
            `;
      tbody.appendChild(row);
    });

    addManagerEventListeners();
  };

  // Add manager event listeners
  const addManagerEventListeners = () => {
    document.querySelectorAll(".edit-mgr-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const managerId = btn.dataset.id;
        const role =
          currentManagerType === "sales"
            ? "sales_manager"
            : "production_manager";
        const manager = allManagers[role]?.find((m) => m.id == managerId);

        if (manager) {
          openManagerModal("edit", manager);
        }
      });
    });

    document.querySelectorAll(".delete-mgr-btn").forEach((btn) => {
      btn.addEventListener("click", (e) => {
        const managerId = btn.dataset.id;
        const role =
          currentManagerType === "sales"
            ? "sales_manager"
            : "production_manager";
        const manager = allManagers[role]?.find((m) => m.id == managerId);

        if (confirm(`Delete "${manager?.full_name || "this manager"}"?`)) {
          deleteManager(managerId);
        }
      });
    });
  };

  // Open manager modal
  const openManagerModal = (mode, manager = null) => {
    const role =
      currentManagerType === "sales" ? "Sales Manager" : "Production Manager";

    if (mode === "create") {
      managerModalTitle.textContent = `Add New ${role}`;
      saveManagerBtn.textContent = "Create Manager";
      saveManagerBtn.classList.remove("bg-amber-600", "hover:bg-amber-700");
      saveManagerBtn.classList.add("bg-blue-600", "hover:bg-blue-700");
      passwordRequiredSpan.textContent = "*";
      document.getElementById("manager_password").required = true;
      managerForm.reset();
      document.getElementById("manager-id").value = "";
    } else {
      managerModalTitle.textContent = `Edit ${role}`;
      saveManagerBtn.textContent = "Update Manager";
      saveManagerBtn.classList.remove("bg-blue-600", "hover:bg-blue-700");
      saveManagerBtn.classList.add("bg-amber-600", "hover:bg-amber-700");
      passwordRequiredSpan.textContent = "";
      document.getElementById("manager_password").required = false;

      document.getElementById("manager-id").value = manager.id || "";
      document.getElementById("manager_name").value = manager.full_name || "";
      document.getElementById("manager_email").value = manager.email || "";
      document.getElementById("manager_phone").value = manager.phone || "";
      document.getElementById("manager_status").value = manager.status || "";
      document.getElementById("manager_password").value = "";
    }

    document.getElementById("manager-type").value = currentManagerType;
    openModal(managerModal);
  };

  // Save manager
  managerForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const managerId = document.getElementById("manager-id").value;
    const isEdit = !!managerId;
    const role =
      currentManagerType === "sales" ? "sales_manager" : "production_manager";

    const data = {
      full_name: document.getElementById("manager_name").value,
      email: document.getElementById("manager_email").value,
      phone: document.getElementById("manager_phone").value,
      status: document.getElementById("manager_status").value,
      role: role,
    };

    const password = document.getElementById("manager_password").value;
    if (password) {
      data.password = password;
    }

    if (isEdit) {
      data.id = managerId;
    }

    try {
      const url = isEdit ? USERS_UPDATE_URL : USERS_CREATE_URL;
      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify(data),
      });
      const result = await response.json();

      if (result.success) {
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg z-50";
        successMsg.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> ${
          result.message || "Manager saved successfully!"
        }`;
        document.body.appendChild(successMsg);
        setTimeout(() => successMsg.remove(), 3000);

        closeModal(managerModal);
        fetchManagersByRole(role);
      } else {
        alert("Error: " + (result.message || "Failed to save manager"));
      }
    } catch (error) {
      console.error("Error saving manager:", error);
      alert("Error saving manager");
    }
  });

  // Delete manager
  const deleteManager = async (managerId) => {
    const role =
      currentManagerType === "sales" ? "sales_manager" : "production_manager";

    try {
      const response = await fetch(USERS_DELETE_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify({ id: managerId }),
      });
      const result = await response.json();

      if (result.success) {
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg z-50";
        successMsg.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Manager deleted successfully!`;
        document.body.appendChild(successMsg);
        setTimeout(() => successMsg.remove(), 3000);

        fetchManagersByRole(role);
      }
    } catch (error) {
      console.error("Error deleting manager:", error);
      alert("Error deleting manager");
    }
  };

  // Add manager buttons
  addSalesBtn.addEventListener("click", () => {
    currentManagerType = "sales";
    openManagerModal("create");
  });

  addProdBtn.addEventListener("click", () => {
    currentManagerType = "production";
    openManagerModal("create");
  });

  // Close modal buttons
  closeManagerBtn.addEventListener("click", () => closeModal(managerModal));
  cancelManagerBtn.addEventListener("click", () => closeModal(managerModal));

  // Click outside modal to close
  managerModal.addEventListener("click", (e) => {
    if (e.target === managerModal) closeModal(managerModal);
  });

  // Load on page load
  loadGeneralSettings();
});
