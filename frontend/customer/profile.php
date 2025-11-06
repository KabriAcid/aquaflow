<?php
// customer/profile.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Aquaflow</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../favicon.png">

    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

</head>

<body class="bg-gray-100">

    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <!-- Profile Content -->
    <main class="py-10">
        <div class="container-fluid">
            <div class="max-w-4xl mx-auto px-4">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-700">My Profile</h1>
                    <p class="text-gray-500">Manage your account information and settings.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Update Profile Form -->
                    <div class="bg-white p-6 rounded-lg multi-shadow">
                        <div class="flex items-center mb-6">
                            <div class="bg-blue-500 rounded-full p-2 mr-3">
                                <i data-lucide="user" class="w-5 h-5 text-white"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-600">Update Profile</h3>
                        </div>
                        <form id="profileForm">
                            <!-- 2x2 Grid Layout for Form Fields -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                    <input type="text" id="full_name" name="full_name" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                    <input type="tel" id="phone" name="phone" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <input type="text" id="address" name="address" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                    <select id="state" name="state" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select State</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City / LGA</label>
                                    <select id="city" name="city" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select City</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-6">
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" id="postal_code" name="postal_code" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Update Profile
                            </button>
                        </form>
                    </div>

                    <!-- Change Password Form -->
                    <div class="bg-white p-6 rounded-lg multi-shadow">
                        <div class="flex items-center mb-6">
                            <div class="bg-red-500 rounded-full p-2 mr-3">
                                <i data-lucide="lock" class="w-5 h-5 text-white"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-600">Change Password</h3>
                        </div>
                        <form id="passwordForm">
                            <div class="mb-4">
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                                <input type="password" id="current_password" name="current_password" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <div class="mb-4">
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password" id="new_password" name="new_password" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <div class="mb-6">
                                <label for="confirm_new_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <input type="password" id="confirm_new_password" name="confirm_new_password" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                            <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                                <i data-lucide="key" class="w-4 h-4"></i>
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
    </main>

    <script>
        // States and Cities Management
        let statesData = {};

        // Load states and cities data
        async function loadStatesData() {
            try {
                const response = await fetch('../../backend/api/location/states_cities.php');
                statesData = await response.json();
                populateStates();
            } catch (error) {
                console.error('Error loading states data:', error);
            }
        }

        // Populate states dropdown
        function populateStates() {
            const stateSelect = document.getElementById('state');
            stateSelect.innerHTML = '<option value="">Select State</option>';

            for (const [stateId, stateInfo] of Object.entries(statesData)) {
                const option = document.createElement('option');
                option.value = stateId;
                option.textContent = stateInfo.name;
                stateSelect.appendChild(option);
            }
        }

        // Populate cities based on selected state
        function populateCities(selectedState) {
            const citySelect = document.getElementById('city');
            citySelect.innerHTML = '<option value="">Select City</option>';

            if (selectedState && statesData[selectedState]) {
                const cities = statesData[selectedState].cities || [];
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.id;
                    option.textContent = city.name;
                    citySelect.appendChild(option);
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Load states and cities data
            loadStatesData();

            // Event listener for state change
            const stateSelect = document.getElementById('state');
            stateSelect.addEventListener('change', function() {
                populateCities(this.value);
            });
            // verify session
            fetch('../../backend/api/auth/me.php', {
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(userData => {
                    if (!userData.success) {
                        window.location.href = '../login.php';
                        return;
                    }

                    // load full profile (server endpoint should read session)
                    return fetch('../../backend/api/auth/get_profile.php', {
                        credentials: 'same-origin'
                    });
                })
                .then(res => res ? res.json() : null)
                .then(data => {
                    if (data && data.success && data.data) {
                        const profile = data.data;
                        document.getElementById('full_name').value = profile.full_name || '';
                        document.getElementById('phone').value = profile.phone || '';
                        document.getElementById('address').value = profile.address || '';
                        document.getElementById('postal_code').value = profile.postal_code || '';

                        // Set state and city values after states are loaded
                        if (profile.state) {
                            setTimeout(() => {
                                document.getElementById('state').value = profile.state;
                                populateCities(profile.state);

                                setTimeout(() => {
                                    if (profile.city) {
                                        document.getElementById('city').value = profile.city;
                                    }
                                }, 100);
                            }, 200);
                        }
                    }
                })
                .catch(err => {
                    console.error('Error loading profile', err);
                    window.location.href = '../login.php';
                });

            document.getElementById('profileForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const profileData = Object.fromEntries(formData.entries());

                fetch('../../backend/api/auth/update_profile.php', {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(profileData)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Profile updated successfully');
                        } else {
                            alert('Error updating profile: ' + data.message);
                        }
                    });
            });

            document.getElementById('passwordForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const passwordData = Object.fromEntries(formData.entries());

                if (passwordData.new_password !== passwordData.confirm_new_password) {
                    alert('New passwords do not match.');
                    return;
                }

                fetch('../../backend/api/auth/change_password.php', {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(passwordData)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert('Password changed successfully');
                            this.reset();
                        } else {
                            alert('Error changing password: ' + data.message);
                        }
                    });
            });

            document.getElementById('logoutBtn').addEventListener('click', function() {
                fetch('../../backend/api/auth/logout.php', {
                        method: 'POST',
                        credentials: 'same-origin'
                    })
                    .then(() => window.location.href = '../login.php')
                    .catch(() => window.location.href = '../login.php');
            });

            // Initialize Lucide icons
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>

</body>

</html>