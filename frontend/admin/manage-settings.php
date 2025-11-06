<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = 'Manage Settings';
include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="flex-1 flex flex-col">

    <main class="flex-1 p-6 bg-gray-100">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-700">Settings</h1>
            <p class="text-gray-500">Manage system settings, user roles, and application configurations.</p>
        </div>

        <div class="bg-white p-6 rounded-lg multi-shadow">
            <p>This page is under construction. Functionality to add, edit, and view settings will be implemented here.</p>
        </div>

        <?php include 'partials/footer.php'; ?>
    </main>
</div>