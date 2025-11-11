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

<div class="flex-1 flex flex-col md:ml-64">
    <main class="flex-1 p-6 bg-gray-100">
        <div class="container-fluid">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">Settings</h1>
            </div>

            <div class="bg-white p-6 rounded-lg multi-shadow">
                <p class="text-gray-600">This is a placeholder for future settings. More options will be available here in upcoming updates.</p>
            </div>
        </div>
    </main>

    <?php include 'partials/footer.php'; ?>