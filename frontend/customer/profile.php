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

</head>

<body class="bg-gray-100">

    <?php require_once __DIR__ . '/partials/topbar.php'; ?>

    <!-- Profile Content -->
    <main class="py-10">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">My Profile</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Update Profile Form -->
                <div class="bg-white p-8 rounded-lg multi-shadow">
                    <h2 class="text-2xl font-bold mb-6">Update Profile</h2>
                    <form id="profileForm">
                        <div class="mb-4">
                            <label for="full_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" id="phone" name="phone" class="mt-1 block w-full border rounded px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                            <input type="text" id="address" name="address" class="mt-1 block w-full border rounded px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                            <input type="text" id="city" name="city" class="mt-1 block w-full border rounded px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                            <input type="text" id="state" name="state" class="mt-1 block w-full border rounded px-3 py-2">
                        </div>
                        <div class="mb-4">
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" class="mt-1 block w-full border rounded px-3 py-2">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Update Profile</button>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="bg-white p-8 rounded-lg multi-shadow">
                    <h2 class="text-2xl font-bold mb-6">Change Password</h2>
                    <form id="passwordForm">
                        <div class="mb-4">
                            <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" id="new_password" name="new_password" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <div class="mb-4">
                            <label for="confirm_new_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" id="confirm_new_password" name="confirm_new_password" class="mt-1 block w-full border rounded px-3 py-2" required>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                        document.getElementById('city').value = profile.city || '';
                        document.getElementById('state').value = profile.state || '';
                        document.getElementById('postal_code').value = profile.postal_code || '';
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
        });
    </script>

</body>

</html>