<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = 'Sales Manager';

$links = [
    ['href' => 'dashboard.php', 'icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'text' => 'Dashboard'],
    ['href' => 'orders.php', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'text' => 'Orders'],
    ['href' => 'products.php', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'text' => 'Products'],
    ['href' => 'customers.php', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A5.965 5.965 0 0112 13a5.965 5.965 0 013 5.197', 'text' => 'Customers'],
];

$current_page = basename($_SERVER['PHP_SELF']);

// Allow pages to override active nav link (e.g., order-details.php wants 'orders' link active)
if (!empty($active_page_override)) {
    $current_page = $active_page_override;
}

?>
<aside class="fixed left-0 top-0 w-64 h-screen bg-white text-gray-800 p-6 flex flex-col shadow-lg hidden md:flex z-40">
    <div class="flex items-center gap-3 mb-8 border-b pb-3">
        <img src="../../favicon.png" alt="Aquaflow Logo" class="w-10 h-10">
        <h1 class="text-2xl font-bold">Aquaflow</h1>
    </div>

    <nav class="flex-1">
        <?php
        // Map readable names to lucide icon names for consistent visuals
        $icon_map = [
            'Dashboard' => 'home',
            'Orders' => 'shopping-cart',
            'Products' => 'package',
            'Customers' => 'users',
        ];
        ?>
        <ul class="space-y-2">
            <?php foreach ($links as $link) : ?>
                <?php $isActive = ($current_page == $link['href']); ?>
                <?php $iconName = $icon_map[$link['text']] ?? 'circle'; ?>
                <li class="whitespace-nowrap">
                    <a href="<?php echo htmlspecialchars($link['href'], ENT_QUOTES); ?>" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors <?php echo $isActive ? 'bg-gray-800 text-white' : 'text-gray-500 hover:bg-gray-200'; ?>">
                        <i data-lucide="<?php echo $iconName; ?>" class="w-5 h-5" aria-hidden="true"></i>
                        <span><?php echo htmlspecialchars($link['text']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="mt-auto">
        <a href="logout.php" class="flex items-center gap-3 px-4 py-2 mt-4 rounded-lg text-gray-500 hover:bg-gray-200 transition-colors">
            <i data-lucide="log-out" class="w-5 h-5" aria-hidden="true"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
<div class="flex-1 flex flex-col md:ml-64">
    <main class="flex-1 p-6 bg-gray-100">