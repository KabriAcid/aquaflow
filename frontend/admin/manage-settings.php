<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Settings';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="md:ml-64">
    <main class="p-6 bg-gray-100 min-h-screen">
        <div class="max-w-6xl mx-auto">
            <!-- Header Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Admin Settings</h1>
                <p class="text-gray-600 mt-1">Configure system settings and manage team members.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Settings Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-4 sticky top-6">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase mb-4">Settings</h3>
                        <nav class="space-y-2">
                            <button class="settings-nav w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition active bg-blue-50 text-blue-600" data-tab="general">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                General Settings
                            </button>
                            <button class="settings-nav w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition" data-tab="sales">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10h.01M11 10h.01M7 10h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Sales Managers
                            </button>
                            <button class="settings-nav w-full text-left px-4 py-2 rounded-lg hover:bg-gray-50 transition" data-tab="production">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Production Managers
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="lg:col-span-3">
                    <!-- General Settings Tab -->
                    <div class="settings-content" data-tab="general">
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                General Settings
                            </h2>
                            <form id="general-settings-form" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">Company Name</label>
                                        <input type="text" id="company_name" name="company_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="e.g., AquaFlow Ltd">
                                    </div>
                                    <div>
                                        <label for="company_email" class="block text-sm font-semibold text-gray-700 mb-2">Company Email</label>
                                        <input type="email" id="company_email" name="company_email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="info@company.com">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="company_phone" class="block text-sm font-semibold text-gray-700 mb-2">Company Phone</label>
                                        <input type="tel" id="company_phone" name="company_phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="+234 xxx xxxx xxx">
                                    </div>
                                    <div>
                                        <label for="company_address" class="block text-sm font-semibold text-gray-700 mb-2">Company Address</label>
                                        <input type="text" id="company_address" name="company_address" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="123 Main Street, City">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="delivery_fee" class="block text-sm font-semibold text-gray-700 mb-2">Delivery Fee (₦)</label>
                                        <input type="number" id="delivery_fee" name="delivery_fee" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="500" step="0.01">
                                    </div>
                                    <div>
                                        <label for="minimum_order" class="block text-sm font-semibold text-gray-700 mb-2">Minimum Order Amount (₦)</label>
                                        <input type="number" id="minimum_order" name="minimum_order" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="1000" step="0.01">
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                    Save General Settings
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Sales Managers Tab -->
                    <div class="settings-content hidden" data-tab="sales">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10h.01M11 10h.01M7 10h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Sales Managers
                                </h2>
                                <button id="add-sales-mgr-btn" type="button" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center gap-2 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Sales Manager
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="text-center py-3 px-6 font-semibold text-gray-700 w-12">#</th>
                                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Full Name</th>
                                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Email</th>
                                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Phone</th>
                                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Status</th>
                                            <th class="text-center py-3 px-6 font-semibold text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="sales-mgr-tbody">
                                        <!-- Sales managers will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="no-sales-msg" class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 inline-block text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="mt-2">No sales managers added yet</p>
                            </div>
                        </div>
                    </div>

                    <!-- Production Managers Tab -->
                    <div class="settings-content hidden" data-tab="production">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Production Managers
                                </h2>
                                <button id="add-prod-mgr-btn" type="button" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg inline-flex items-center gap-2 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Add Production Manager
                                </button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            <th class="text-center py-3 px-6 font-semibold text-gray-700 w-12">#</th>
                                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Full Name</th>
                                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Email</th>
                                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Phone</th>
                                            <th class="text-left py-3 px-6 font-semibold text-gray-700">Status</th>
                                            <th class="text-center py-3 px-6 font-semibold text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="prod-mgr-tbody">
                                        <!-- Production managers will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="no-prod-msg" class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 inline-block text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="mt-2">No production managers added yet</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Manager Modal (Reusable for both Sales and Production) -->
    <div id="manager-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" style="display: none; align-items: center; justify-content: center;">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col" style="display: flex;">
            <!-- Modal Header -->
            <div class="border-b border-gray-200 p-6 bg-white flex-shrink-0">
                <div class="flex items-center justify-between">
                    <h2 id="manager-modal-title" class="text-2xl font-bold text-gray-800">Add Sales Manager</h2>
                    <button type="button" id="close-manager-modal-btn" class="text-gray-400 hover:text-gray-600 transition p-1 rounded hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="manager-form" class="flex-1 overflow-y-auto p-6 pb-24" style="scrollbar-width: none; -ms-overflow-style: none;">
                <input type="hidden" id="manager-id" name="manager_id">
                <input type="hidden" id="manager-type" name="manager_type">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="manager_name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                        <input type="text" id="manager_name" name="manager_name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Full Name" required>
                    </div>
                    <div>
                        <label for="manager_email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                        <input type="email" id="manager_email" name="manager_email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="email@example.com" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="manager_phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                        <input type="tel" id="manager_phone" name="manager_phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="+234 xxx xxxx xxx">
                    </div>
                    <div>
                        <label for="manager_password" class="block text-sm font-semibold text-gray-700 mb-2">Password <span id="password-required-mgr" class="text-red-500">*</span></label>
                        <input type="password" id="manager_password" name="manager_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" placeholder="Enter password" required>
                    </div>
                </div>

                <div>
                    <label for="manager_status" class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                    <select id="manager_status" name="manager_status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition" required>
                        <option value="">Select Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="border-t border-gray-200 p-6 bg-white flex-shrink-0 flex justify-end gap-3 sticky bottom-0">
                <button type="button" id="cancel-manager-btn" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" id="save-manager-btn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">Save Manager</button>
            </div>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>
</div>

<script src="../js/admin-settings.js" defer></script>