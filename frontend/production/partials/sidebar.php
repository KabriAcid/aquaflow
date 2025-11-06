<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = 'Production Manager'; // Changed from Admin

// Links for the sidebar - specific to Production Manager
$links = [
    ['href' => 'dashboard.php', 'icon' => 'home', 'text' => 'Dashboard'],
    ['href' => 'manage-inventory.php', 'icon' => 'package', 'text' => 'Manage Inventory'],
    ['href' => 'manage-production.php', 'icon' => 'settings', 'text' => 'Manage Production'],
];

$current_page = basename($_SERVER['PHP_SELF']);

?>
<aside class="w-64 bg-white text-gray-800 p-6 flex-col shadow-lg hidden md:flex">
    <div class="flex items-center gap-3 mb-8 border-b pb-3">
        <img src="../../favicon.png" alt="Aquaflow Logo" class="w-10 h-10">
        <h1 class="text-2xl font-bold">Aquaflow</h1>
    </div>

    <nav class="flex-1">
        <ul class="space-y-2">
            <?php foreach ($links as $link) : ?>
                <?php $isActive = ($current_page == $link['href']); ?>
                <li class="whitespace-nowrap">
                    <a href="<?php echo htmlspecialchars($link['href'], ENT_QUOTES); ?>" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors <?php echo $isActive ? 'bg-gray-800 text-white' : 'text-gray-500 hover:bg-gray-200'; ?>">
                        <i data-lucide="<?php echo $link['icon']; ?>" class="w-5 h-5" aria-hidden="true"></i>
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