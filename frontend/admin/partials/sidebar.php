<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION['user_name'] ?? 'Admin';

// Links for the sidebar
$links = [
    ['href' => 'dashboard.php', 'icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'text' => 'Dashboard'],
    ['href' => 'manage-users.php', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'text' => 'Manage Users'],
    ['href' => 'manage-sales-managers.php', 'icon' => 'M3 10h18M12 14a4 4 0 100-8 4 4 0 000 8z', 'text' => 'Sales Managers'],
    ['href' => 'manage-production-managers.php', 'icon' => 'M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6M12 7v.01', 'text' => 'Production Managers'],
    ['href' => 'customers.php', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1', 'text' => 'Customers'],
    ['href' => 'manage-products.php', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6', 'text' => 'Products'],
    ['href' => 'manage-orders.php', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7', 'text' => 'Orders'],
    ['href' => 'manage-inventory.php', 'icon' => 'M3 10h18M3 14h18m-9-4v8', 'text' => 'Inventory'],
    ['href' => 'reports.php', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5', 'text' => 'Reports'],
    ['href' => 'financial.php', 'icon' => 'M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3', 'text' => 'Financials'],
    ['href' => 'settings.php', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.165c1.58-.91 3.4.58 2.49 2.16a1.724 1.724 0 001.165 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.165 2.573c.91 1.58-.91 3.4-2.49 2.49a1.724 1.724 0 00-2.573 1.165c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.165c-1.58.91-3.4-.91-2.49-2.49a1.724 1.724 0 00-1.165-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.165-2.573c-.91-1.58.91-3.4 2.49-2.49.897.517 2.12.117 2.573-1.165z', 'text' => 'Settings'],
    ['href' => 'logs.php', 'icon' => 'M8 17l4-4 4 4M8 7h8', 'text' => 'Logs'],
];

$current_page = basename($_SERVER['PHP_SELF']);

?>
<aside class="w-64 bg-white text-gray-800 p-6 flex-col shadow-lg hidden md:flex">
    <div class="flex items-center gap-3 mb-8">
        <img src="../../favicon.png" alt="Aquaflow Logo" class="w-10 h-10">
        <h1 class="text-2xl font-bold">Aquaflow</h1>
    </div>

    <nav class="flex-1">
        <?php
        // Prepare a single icon map to avoid recreating it on each loop iteration
        $icon_map = [
            'Dashboard' => 'home',
            'Manage Users' => 'users',
            'Sales Managers' => 'user-check',
            'Production Managers' => 'user',
            'Customers' => 'users',
            'Products' => 'package',
            'Orders' => 'shopping-cart',
            'Inventory' => 'archive',
            'Reports' => 'bar-chart-2',
            'Financials' => 'dollar-sign',
            'Settings' => 'settings',
            'Logs' => 'file-text'
        ];
        ?>
        <ul class="space-y-2">
            <?php foreach ($links as $link) : ?>
                <?php $isActive = ($current_page == $link['href']); ?>
                <?php $iconName = $icon_map[$link['text']] ?? 'circle'; ?>
                <li>
                    <a href="<?php echo htmlspecialchars($link['href'], ENT_QUOTES); ?>" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors <?php echo $isActive ? 'bg-gray-800 text-white' : 'text-gray-500 hover:bg-gray-200'; ?>">
                        <i data-lucide="<?php echo $iconName; ?>" class="w-5 h-5" aria-hidden="true"></i>
                        <span><?php echo htmlspecialchars($link['text']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="mt-auto">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-500">
                <?php echo strtoupper(substr($userName, 0, 1)); ?>
            </div>
            <div>
                <p class="font-semibold"><?php echo htmlspecialchars($userName); ?></p>
                <p class="text-sm text-gray-500">Admin</p>
            </div>
        </div>
        <a href="../../includes/logout.php" class="flex items-center gap-3 px-4 py-2 mt-4 rounded-lg text-gray-500 hover:bg-gray-200 transition-colors">
            <i data-lucide="log-out" class="w-5 h-5" aria-hidden="true"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
<div class="flex-1 flex flex-col">
    <main class="flex-1 p-6 bg-gray-100">