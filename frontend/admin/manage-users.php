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

<div class="md:ml-64">
    <main class="p-6 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8 flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Manage Users</h1>
                    <p class="text-gray-600 mt-1">Create, edit, and manage user accounts.</p>
                </div>
                <button id="add-user-btn" class="btn-primary inline-flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-5 h-5 inline"></i> Add User
                </button>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden relative">
                <div id="loading-indicator" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center hidden z-40 rounded-lg">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-4 border-gray-300 border-t-blue-500 mx-auto mb-4"></div>
                        <p class="text-gray-600 font-medium">Loading users...</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-center py-3 px-6 font-semibold text-gray-700 w-12">#</th>
                                <th class="text-left py-3 px-6 font-semibold text-gray-700">Full Name</th>
                                <th class="text-left py-3 px-6 font-semibold text-gray-700">Email</th>
                                <th class="text-left py-3 px-6 font-semibold text-gray-700">Role</th>
                                <th class="text-left py-3 px-6 font-semibold text-gray-700">Phone</th>
                                <th class="text-left py-3 px-6 font-semibold text-gray-700">Status</th>
                                <th class="text-center py-3 px-6 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody">
                            <!-- Rows will be injected by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div id="no-users-message" class="text-center py-8 text-gray-500 hidden">
                    <i data-lucide="inbox" class="w-12 h-12 inline-block text-gray-300 mb-2"></i>
                    <p class="mt-2">No users found</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Add/Edit User Modal -->
    <div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" style="display: none; align-items: center; justify-content: center;">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col" style="display: flex;">
            <!-- Modal Header -->
            <div class="border-b border-gray-200 p-6 bg-white flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h2 id="modal-title" class="text-2xl font-bold text-gray-800">Add New User</h2>
                    <button type="button" id="close-modal-btn" class="text-gray-400 hover:text-gray-600 transition p-1 rounded hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body - Scrollable -->
            <form id="user-form" class="flex-1 overflow-y-auto p-6 pb-24" style="scrollbar-width: none; -ms-overflow-style: none;">
                <input type="hidden" id="user-id" name="user_id">

                <!-- Name & Email -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="e.g., John Doe" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="john@example.com" required>
                    </div>
                </div>

                <!-- Phone & Role -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                        <input type="tel" id="phone" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="e.g., +234 xxx xxxx xxx">
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Role *</label>
                        <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                            <option value="">Select role</option>
                            <option value="customer">Customer</option>
                            <option value="sales_manager">Sales Manager</option>
                            <option value="production_manager">Production Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <!-- State & City -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="state" class="block text-sm font-semibold text-gray-700 mb-2">State</label>
                        <select id="state" name="state" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div>
                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">City / LGA</label>
                        <select id="city" name="city" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                            <option value="">Select City</option>
                        </select>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password <span id="password-required" class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Enter password" required>
                    <p class="text-xs text-gray-500 mt-1">Leave empty to keep existing password (for edit mode)</p>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                    <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        <option value="">Select status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </form>

            <!-- Modal Footer - Fixed -->
            <div class="border-t border-gray-200 p-6 bg-white flex-shrink-0 flex justify-end gap-3 sticky bottom-0">
                <button type="button" id="cancel-btn" class="btn-secondary">Cancel</button>
                <button type="submit" id="save-btn" class="btn-primary">Save User</button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" style="display: none; align-items: center; justify-content: center;">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-sm">
            <!-- Modal Header -->
            <div class="border-b border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-800">Delete User</h2>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <p class="text-gray-600 mb-2">Are you sure you want to delete this user?</p>
                <p class="text-sm text-gray-500">This action cannot be undone.</p>
            </div>

            <!-- Modal Footer -->
            <form id="delete-user-form" class="border-t border-gray-200 p-6">
                <input type="hidden" id="delete-user-id" name="user_id">
                <div class="flex justify-end gap-3">
                    <button type="button" id="cancel-delete-btn" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-danger">Delete User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="../js/admin-users.js" defer></script>
<?php include 'partials/footer.php'; ?>