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
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Users</h1>
            <p class="text-gray-500">Manage system users — create accounts, change roles, deactivate or remove users as necessary.</p>
        </div>

        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">All Users</h3>
                <button class="btn-primary inline-flex items-center gap-2"><i data-lucide="user-plus" class="w-4 h-4" aria-hidden="true"></i> Create User</button>
            </div>
            <table class="w-full" id="users-table">
                <thead>
                    <tr class="border-b">
                        <th class="text-left p-3">Name</th>
                        <th class="text-left p-3">Email</th>
                        <th class="text-left p-3">Role</th>
                        <th class="text-left p-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows inserted by JS -->
                </tbody>
            </table>
        </div>

        <?php include 'partials/footer.php'; ?>
    </main>
</div>