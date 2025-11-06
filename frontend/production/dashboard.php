<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'production_manager') {
    header('Location: ../login.php');
    exit;
}

$userName = isset($_SESSION['user_name']) && !empty($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Production Manager';
$page_title = "Production Manager";

include 'partials/header.php';
include 'partials/sidebar.php';
// Page layout wrapper (kept in page so partials only render their component)
?>
<div class="flex-1 flex flex-col">
    <main class="flex-1 p-6 bg-gray-100">
        <?php
        ?>

        <div class="container-fluid">
            <div class="mb-6">
                <h1 class="text-2xl font-semibold text-gray-700">Production Manager</h1>
                <p class="text-gray-500">Welcome, <?php echo htmlspecialchars($userName); ?>!</p>
            </div>
        </div>

        <?php include 'partials/footer.php'; ?>