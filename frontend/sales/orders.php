<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'sales') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Manage Orders";

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manage Orders</h1>
    <p>This is where you will manage orders.</p>
</div>

<?php include 'partials/footer.php'; ?>