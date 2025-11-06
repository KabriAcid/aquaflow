<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Manage Users';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="flex-1 flex flex-col">
    <main class="flex-1 p-6 bg-gray-100">
        <div class="container-fluid">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-700">Users</h1>
                    <p class="text-gray-500">Manage customer accounts.</p>
                </div>
                <button id="add-user-btn" class="btn-primary inline-flex items-center gap-2"><i data-lucide="user-plus" class="w-4 h-4" aria-hidden="true"></i> Add User</button>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">All Users</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full" id="users-table">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3">Name</th>
                                <th class="text-left p-3">Email</th>
                                <th class="text-left p-3">Phone</th>
                                <th class="text-left p-3">Date Joined</th>
                                <th class="text-left p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody">
                            <!-- Rows will be inserted by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <script src="../js/admin-users.js" defer></script>
        </div>
    </main>

    <!-- Add User Modal -->
    <div id="add-user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6">Add New User</h2>
            <form id="add-user-form">
                <div class="mb-4">
                    <label for="add-name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="add-name" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="add-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="add-email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="add-phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" id="add-phone" name="phone" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-6">
                    <label for="add-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="add-password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="flex justify-end gap-4">
                    <button type="button" id="cancel-add-btn" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="edit-user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6">Edit User</h2>
            <form id="edit-user-form">
                <input type="hidden" id="edit-user-id" name="user_id">
                <div class="mb-4">
                    <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="edit-name" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="edit-email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="edit-phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" id="edit-phone" name="phone" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-6">
                    <label for="edit-password" class="block text-sm font-medium text-gray-700 mb-1">New Password (optional)</label>
                    <input type="password" id="edit-password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex justify-end gap-4">
                    <button type="button" id="cancel-edit-btn" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-sm">
            <h2 class="text-2xl font-bold mb-4">Confirm Deletion</h2>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
            <form id="delete-user-form">
                <input type="hidden" id="delete-user-id" name="user_id">
                <div class="flex justify-end gap-4">
                    <button type="button" id="cancel-delete-btn" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
    </script>
    <?php include 'partials/footer.php'; ?>