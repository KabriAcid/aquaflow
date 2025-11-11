<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = 'Production Manager';

// Links for the sidebar - specific to Production Manager
$links = [
    ['href' => 'dashboard.php', 'icon' => 'home', 'text' => 'Dashboard'],
    ['href' => 'manage-inventory.php', 'icon' => 'package', 'text' => 'Manage Inventory'],
    ['href' => 'manage-production.php', 'icon' => 'settings', 'text' => 'Manage Production'],
];

$current_page = basename($_SERVER['PHP_SELF']);
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
        <ul class="space-y-2">
            <?php foreach ($links as $link) : ?>
                <?php $isActive = ($current_page == $link['href']); ?>
                <li class="whitespace-nowrap">
                    <a href="<?php echo htmlspecialchars($link['href'], ENT_QUOTES); ?>" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors <?php echo $isActive ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100'; ?>">
                        <i data-lucide="<?php echo $link['icon']; ?>" class="w-5 h-5"></i>
                        <span><?php echo htmlspecialchars($link['text']); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="mt-auto border-t pt-4">
        <a href="logout.php" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
            <i data-lucide="log-out" class="w-5 h-5"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>