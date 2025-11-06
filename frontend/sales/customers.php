<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'sales') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Manage Customers";

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manage Customers</h1>
    <p>This is where you will manage customers.</p>
</div>

<?php include 'partials/footer.php'; ?>