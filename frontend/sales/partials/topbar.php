<?php
// sales/partials/topbar.php
?>
<header class="bg-white shadow-md p-4 flex justify-between items-center">
    <div>
        <h1 class="text-xl font-bold">Sales Dashboard</h1>
    </div>
    <div class="flex items-center">
        <span class="mr-4">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Sales Manager'); ?></span>
        <a href="../../backend/api/auth/logout.php" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Logout</a>
    </div>
</header>
