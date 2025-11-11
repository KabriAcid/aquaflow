<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Admin Profile';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="flex-1 flex flex-col md:ml-64">
    <main class="flex-1 p-6 bg-gray-100">
        <div class="container-fluid">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">My Profile</h1>
                <p class="text-gray-500">Update your personal information and password.</p>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow max-w-lg mx-auto">
                <form id="profile-form">
                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="username" name="username" class="form-input w-full mt-1" readonly>
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" id="email" name="email" class="form-input w-full mt-1">
                    </div>
                    <div class="border-t pt-4 mt-4">
                        <p class="text-md font-semibold">Change Password</p>
                        <div class="mt-2">
                            <label for="current-password" class="block text-sm font-medium text-gray-700">Current Password</label>
                            <input type="password" id="current-password" name="current_password" class="form-input w-full mt-1">
                        </div>
                        <div class="mt-2">
                            <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" id="new-password" name="new_password" class="form-input w-full mt-1">
                        </div>
                        <div class="mt-2">
                            <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                            <input type="password" id="confirm-password" name="confirm_password" class="form-input w-full mt-1">
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>

            <script src="../js/admin-profile.js" defer></script>
        </div>
    </main>
    </script>
    <?php include 'partials/footer.php'; ?>