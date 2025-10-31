<?php
// Default page title if not set
$current_page_title = $page_title ?? 'Dashboard';
?>
<header class="bg-white p-4 flex items-center justify-between shadow-md">
    <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($current_page_title); ?></h2>
    <div>
        <a href="../../backend/api/auth/logout.php" class="text-gray-600 hover:text-gray-800">Logout</a>
    </div>
</header>