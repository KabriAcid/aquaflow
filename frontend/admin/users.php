<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = 'User Management';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="flex-1 flex flex-col">
    <main class="flex-1 p-6 bg-gray-100">
        <div class="container-fluid">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-700">User Management</h1>
                    <p class="text-gray-500">Add, edit, or remove users.</p>
                </div>
                <button id="add-user-btn" class="btn-primary">Add New User</button>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <div class="overflow-x-auto">
                    <table class="w-full" id="users-table">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3">Username</th>
                                <th class="text-left p-3">Email</th>
                                <th class="text-left p-3">Role</th>
                                <th class="text-left p-3">State</th>
                                <th class="text-left p-3">LGA</th>
                                <th class="text-left p-3">Phone</th>
                                <th class="text-left p-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody">
                            <!-- User rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <script src="../js/admin-users.js" defer></script>
        </div>
    </main>

<!-- Add/Edit User Modal -->
<div id="user-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center">
    <div class="bg-white rounded-lg p-8 max-w-lg w-full multi-shadow">
        <h2 id="modal-title" class="text-xl font-bold mb-4"></h2>
        <form id="user-form">
            <input type="hidden" id="user-id" name="user_id">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" class="form-input w-full mt-1" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="form-input w-full mt-1" required>
            </div>
            <div class="mb-4" id="password-field">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="form-input w-full mt-1">
            </div>
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role" class="form-select w-full mt-1" required>
                    <option value="customer">Customer</option>
                    <option value="sales_manager">Sales Manager</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                <input type="text" id="state" name="state" class="form-input w-full mt-1">
            </div>
            <div class="mb-4">
                <label for="lga" class="block text-sm font-medium text-gray-700">LGA</label>
                <input type="text" id="lga" name="lga" class="form-input w-full mt-1">
            </div>
            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-input w-full mt-1">
            </div>
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" id="cancel-btn" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<?php include 'partials/footer.php'; ?>