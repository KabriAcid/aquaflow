document.addEventListener("DOMContentLoaded", () => {
  // DOM Elements - with null checks
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

  // Log which elements were found
  console.log("DOM Elements found:", {
    profileForm: !!profileForm,
    passwordForm: !!passwordForm,
    fullnameInput: !!fullnameInput,
    emailInput: !!emailInput,
    profileFullname: !!profileFullname,
    profileEmail: !!profileEmail,
    profileDate: !!profileDate,
  });

  const GET_URL = "/aquaflow/backend/api/users/get.php";
  const UPDATE_URL = "/aquaflow/backend/api/users/update.php";

  console.log("Admin Profile Script Loaded");
  console.log("GET_URL:", GET_URL);
  console.log("UPDATE_URL:", UPDATE_URL);

  // Fetch and display profile
  const fetchProfile = async () => {
    try {
      const response = await fetch(GET_URL, { credentials: "same-origin" });

      // Check if response is ok
      if (!response.ok) {
        console.error("HTTP Error:", response.status, response.statusText);

        // Update display to show error
        if (profileFullname)
          profileFullname.textContent = "Error loading profile";
        if (profileEmail) profileEmail.textContent = "Failed to load";
        alert("Failed to load profile. Please log in again.");
        return;
      }

      const result = await response.json();
      console.log("Profile fetch result:", result);

      if (result.success && result.data) {
        const user = result.data;

        // Populate form inputs with null checks
        if (fullnameInput)
          fullnameInput.value = user.full_name || user.username || "";
        if (emailInput) emailInput.value = user.email || "";

        // Update profile card with null checks
        if (profileFullname)
          profileFullname.textContent =
            user.full_name || user.username || "Admin User";
        if (profileEmail) profileEmail.textContent = user.email || "N/A";

        // Format date
        if (user.created_at && profileDate) {
          const date = new Date(user.created_at);
          profileDate.textContent = date.toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric",
          });
        }
      } else {
        console.error("API Error - Failed to fetch profile:", result.message);
        if (profileFullname) profileFullname.textContent = "Error";
        if (profileEmail) profileEmail.textContent = "N/A";
        alert(
          "Failed to load profile information: " +
            (result.message || "Unknown error")
        );
      }
    } catch (error) {
      console.error("Error fetching profile:", error);
      if (profileFullname) profileFullname.textContent = "Error";
      if (profileEmail) profileEmail.textContent = "N/A";
      alert("An error occurred while loading your profile: " + error.message);
    }
  };

  // Handle personal information form submission
  if (profileForm) {
    profileForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!fullnameInput || !emailInput) {
        console.error("Form inputs not found");
        alert("Form elements are not properly loaded");
        return;
      }

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
          if (profileFullname) profileFullname.textContent = fullname;
          if (profileEmail) profileEmail.textContent = email;
        } else {
          alert("Error: " + (result.message || "Failed to update profile"));
        }

        btn.innerHTML = originalText;
        btn.disabled = false;
      } catch (error) {
        console.error("Error updating profile:", error);
        alert("An error occurred while updating your profile");
        const saveBtn = document.getElementById("save-profile-btn");
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.innerHTML = "Save Personal Information";
        }
      }
    });
  }

  // Handle password change form submission
  if (passwordForm) {
    passwordForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!currentPasswordInput || !newPasswordInput || !confirmPasswordInput) {
        console.error("Password form inputs not found");
        alert("Password form elements are not properly loaded");
        return;
      }

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
          if (currentPasswordInput) currentPasswordInput.value = "";
          if (newPasswordInput) newPasswordInput.value = "";
          if (confirmPasswordInput) confirmPasswordInput.value = "";
        } else {
          alert("Error: " + (result.message || "Failed to update password"));
        }

        btn.innerHTML = originalText;
        btn.disabled = false;
      } catch (error) {
        console.error("Error updating password:", error);
        alert("An error occurred while updating your password");
        const saveBtn = document.getElementById("save-password-btn");
        if (saveBtn) {
          saveBtn.disabled = false;
          saveBtn.innerHTML = "Update Password";
        }
      }
    });
  }

  // Load profile on page load
  fetchProfile();
});
