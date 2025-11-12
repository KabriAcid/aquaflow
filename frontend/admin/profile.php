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

<div class="md:ml-64">
    <main class="p-6 bg-gray-100 min-h-screen">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Admin Profile</h1>
                <p class="text-gray-600 mt-1">Manage your account information and security settings.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-6">
                        <div class="text-center mb-6">
                            <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <h2 id="profile-fullname" class="text-xl font-bold text-gray-800">Loading...</h2>
                            <p id="profile-role" class="text-sm text-gray-500 mt-1">Admin</p>
                        </div>
                        <div class="border-t pt-4 space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Email</p>
                                <p id="profile-email" class="text-sm text-gray-700">Loading...</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase">Member Since</p>
                                <p id="profile-date" class="text-sm text-gray-700">Loading...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div class="lg:col-span-2">
                    <!-- Personal Information Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Personal Information
                        </h3>
                        <form id="profile-form" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="fullname" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                    <input type="text" id="fullname" name="fullname" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Your full name" required>
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="your.email@example.com" required>
                                </div>
                            </div>

                            <button type="submit" id="save-profile-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Save Personal Information
                            </button>
                        </form>
                    </div>

                    <!-- Change Password Section -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Change Password
                        </h3>
                        <form id="password-form" class="space-y-4">
                            <div>
                                <label for="current-password" class="block text-sm font-semibold text-gray-700 mb-2">Current Password</label>
                                <input type="password" id="current-password" name="current_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Enter current password">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="new-password" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                                    <input type="password" id="new-password" name="new_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Enter new password">
                                    <p class="text-xs text-gray-500 mt-1">At least 8 characters recommended</p>
                                </div>
                                <div>
                                    <label for="confirm-password" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                                    <input type="password" id="confirm-password" name="confirm_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Confirm new password">
                                </div>
                            </div>

                            <button type="submit" id="save-password-btn" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include 'partials/footer.php'; ?>
</div>

<script src="../js/admin-profile.js" defer></script>