<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION['user_name'] ?? 'Admin';

// Links for the sidebar
$links = [
    ['href' => 'dashboard.php', 'icon' => 'M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'text' => 'Dashboard'],
    ['href' => 'reports.php', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10', 'text' => 'Reports'],
    ['href' => 'manage-users.php', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'text' => 'Manage Users'],
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
                <p class="text-sm text-gray-500">Admin</p>
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
