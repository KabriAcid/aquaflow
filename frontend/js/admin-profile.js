document.addEventListener("DOMContentLoaded", () => {
  // DOM Elements
  const profileForm = document.getElementById("profile-form");
  const passwordForm = document.getElementById("password-form");
  const fullnameInput = document.getElementById("fullname");
  const emailInput = document.getElementById("email");
  const currentPasswordInput = document.getElementById("current-password");
  const newPasswordInput = document.getElementById("new-password");
  const confirmPasswordInput = document.getElementById("confirm-password");

  // Profile display elements
  const profileFullname = document.getElementById("profile-fullname");
  const profileEmail = document.getElementById("profile-email");
  const profileDate = document.getElementById("profile-date");

  const GET_URL = "/aquaflow/backend/api/users/get.php";
  const UPDATE_URL = "/aquaflow/backend/api/users/update.php";

  // Fetch and display profile
  const fetchProfile = async () => {
    try {
      const response = await fetch(GET_URL, { credentials: "same-origin" });
      const result = await response.json();

      if (result.success && result.data) {
        const user = result.data;

        // Populate form inputs
        fullnameInput.value = user.full_name || user.username || "";
        emailInput.value = user.email || "";

        // Update profile card
        profileFullname.textContent =
          user.full_name || user.username || "Admin User";
        profileEmail.textContent = user.email || "N/A";

        // Format date
        if (user.created_at) {
          const date = new Date(user.created_at);
          profileDate.textContent = date.toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
          });
        }
      } else {
        console.error("Failed to fetch profile:", result.message);
        alert("Failed to load profile information");
      }
    } catch (error) {
      console.error("Error fetching profile:", error);
      alert("An error occurred while loading your profile");
    }
  };

  // Handle personal information form submission
  profileForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const fullname = fullnameInput.value.trim();
    const email = emailInput.value.trim();

    if (!fullname || !email) {
      alert("Please fill in all required fields");
      return;
    }

    const payload = {
      full_name: fullname,
      email: email,
    };

    try {
      const btn = document.getElementById("save-profile-btn");
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML =
        '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Saving...';

      const response = await fetch(UPDATE_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify(payload),
      });
      const result = await response.json();

      if (result.success === true) {
        // Show success message
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg z-50";
        successMsg.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Profile updated successfully!`;
        document.body.appendChild(successMsg);
        setTimeout(() => successMsg.remove(), 3000);

        // Update profile card
        profileFullname.textContent = fullname;
        profileEmail.textContent = email;
      } else {
        alert("Error: " + (result.message || "Failed to update profile"));
      }

      btn.innerHTML = originalText;
      btn.disabled = false;
    } catch (error) {
      console.error("Error updating profile:", error);
      alert("An error occurred while updating your profile");
      document.getElementById("save-profile-btn").disabled = false;
      document.getElementById("save-profile-btn").innerHTML =
        "Save Personal Information";
    }
  });

  // Handle password change form submission
  passwordForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const currentPassword = currentPasswordInput.value;
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;

    // Validation
    if (!currentPassword) {
      alert("Please enter your current password");
      return;
    }

    if (!newPassword) {
      alert("Please enter a new password");
      return;
    }

    if (newPassword.length < 8) {
      alert("New password must be at least 8 characters long");
      return;
    }

    if (newPassword !== confirmPassword) {
      alert("New passwords do not match");
      return;
    }

    if (currentPassword === newPassword) {
      alert("New password must be different from current password");
      return;
    }

    const payload = {
      password: newPassword,
      current_password: currentPassword,
    };

    try {
      const btn = document.getElementById("save-password-btn");
      const originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML =
        '<i data-lucide="loader" class="w-4 h-4 animate-spin inline"></i> Updating...';

      const response = await fetch(UPDATE_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        credentials: "same-origin",
        body: JSON.stringify(payload),
      });
      const result = await response.json();

      if (result.success === true) {
        // Show success message
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg z-50";
        successMsg.innerHTML = `<svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Password updated successfully!`;
        document.body.appendChild(successMsg);
        setTimeout(() => successMsg.remove(), 3000);

        // Clear password fields
        currentPasswordInput.value = "";
        newPasswordInput.value = "";
        confirmPasswordInput.value = "";
      } else {
        alert("Error: " + (result.message || "Failed to update password"));
      }

      btn.innerHTML = originalText;
      btn.disabled = false;
    } catch (error) {
      console.error("Error updating password:", error);
      alert("An error occurred while updating your password");
      document.getElementById("save-password-btn").disabled = false;
      document.getElementById("save-password-btn").innerHTML =
        "Update Password";
    }
  });

  // Load profile on page load
  fetchProfile();
});
