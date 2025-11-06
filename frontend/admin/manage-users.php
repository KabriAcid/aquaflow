<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Manage Users";

include 'partials/header.php';
include 'partials/sidebar.php';
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Manage Users</h1>
    <p>This is where you will manage users.</p>
</div>

<?php include 'partials/footer.php'; ?>
