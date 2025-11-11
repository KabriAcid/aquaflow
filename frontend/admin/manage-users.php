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

<div class="flex-1 flex flex-col md:ml-64">
    <main class="flex-1 p-6 bg-gray-100">
        <div class="container-fluid">
            <div class="mb-6 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-700">Users</h1>
                    <p class="text-gray-500">Manage customer accounts.</p>
                </div>
                <button id="add-user-btn" class="btn-primary inline-flex items-center gap-2"><i data-lucide="user-plus" class="w-4 h-4 inline" aria-hidden="true"></i> Add User</button>
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

                // Set selected values for edit mode
                function setSelectedStateCity(stateValue, cityValue) {
                    const stateSelect = document.getElementById('state');
                    const citySelect = document.getElementById('city');

                    if (stateValue) {
                        stateSelect.value = stateValue;
                        populateCities(stateValue);

                        setTimeout(() => {
                            if (cityValue) {
                                citySelect.value = cityValue;
                            }
                        }, 100);
                    }
                }

                // Event listeners
                document.addEventListener('DOMContentLoaded', function() {
                    loadStatesData();

                    // Ensure lucide replaces any static placeholders (header icons) after DOM load.
                    if (window.lucide) {
                        lucide.createIcons();
                    }

                    const stateSelect = document.getElementById('state');
                    stateSelect.addEventListener('change', function() {
                        populateCities(this.value);
                    });
                });
            </script>
        </div>
    </main>

    <!-- Add/Edit User Modal -->
    <div id="user-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-lg">
            <h2 class="text-2xl font-bold mb-6" id="modal-title">Add New User</h2>
            <form id="user-form">
                <input type="hidden" id="user-id" name="user_id">
                <!-- 2x2 Grid Layout for Form Fields -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" id="phone" name="phone" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <select id="state" name="state" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select State</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City / LGA</label>
                        <select id="city" name="city" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select City</option>
                        </select>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                <div class="flex justify-end gap-4">
                    <button type="button" id="cancel-btn" class="btn-secondary">Cancel</button>
                    <button type="submit" id="save-btn" class="btn-primary">Save User</button>
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