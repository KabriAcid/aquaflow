<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION['user_name'] ?? 'Sales Manager';

$links = [
    ['href' => 'dashboard.php', 'icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'text' => 'Dashboard'],
    ['href' => 'orders.php', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'text' => 'Orders'],
    ['href' => 'products.php', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'text' => 'Products'],
    ['href' => 'customers.php', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A5.965 5.965 0 0112 13a5.965 5.965 0 013 5.197', 'text' => 'Customers'],
];

$current_page = basename($_SERVER['PHP_SELF']);

?>
<aside class="w-64 bg-white text-gray-800 p-6 flex-col shadow-lg hidden md:flex">
    <div class="flex items-center gap-3 mb-8">
        <img src="../../favicon.png" alt="Aquaflow Logo" class="w-10 h-10">
        <h1 class="text-2xl font-bold">Aquaflow</h1>
    </div>

    <nav class="flex-1">
        <ul class="space-y-2">
            <?php foreach ($links as $link) : ?>
                <?php $isActive = ($current_page == $link['href']); ?>
                <li>
                    <a href="<?php echo $link['href']; ?>" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-colors <?php echo $isActive ? 'bg-gray-800 text-white' : 'text-gray-500 hover:bg-gray-200'; ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $link['icon']; ?>"></path></svg>
                        <span><?php echo $link['text']; ?></span>
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
                <p class="text-sm text-gray-500">Sales Manager</p>
            </div>
        </div>
        <a href="../../includes/logout.php" class="flex items-center gap-3 px-4 py-2 mt-4 rounded-lg text-gray-500 hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3"></path></svg>
            <span>Logout</span>
        </a>
    </div>
</aside>
<div class="flex-1 flex flex-col">
    <?php include 'partials/topbar.php'; ?>
    <main class="flex-1 p-6 bg-gray-100">
