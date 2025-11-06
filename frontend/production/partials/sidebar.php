<?php
// Check if a session is already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the user\'s name from the session, with a fallback
$userName = $_SESSION['user_name'] ?? 'Production Manager';

?>
<aside class="w-64 bg-card text-card-foreground p-6 flex flex-col shadow-lg">
    <div class="flex items-center gap-3 mb-8">
        <img src="../../favicon.png" alt="Aquaflow Logo" class="w-10 h-10">
        <h1 class="text-2xl font-bold">Aquaflow</h1>
    </div>

    <nav class="flex-1">
        <ul class="space-y-2">
            <li>
                <a href="dashboard.php" class="flex items-center gap-3 px-4 py-2 rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="manage-production.php" class="flex items-center gap-3 px-4 py-2 rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span>Manage Production</span>
                </a>
            </li>
            <li>
                <a href="manage-inventory.php" class="flex items-center gap-3 px-4 py-2 rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    <span>Manage Inventory</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="mt-auto">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-muted flex items-center justify-center font-bold text-muted-foreground">
                <?php echo strtoupper(substr($userName, 0, 1)); ?>
            </div>
            <div>
                <p class="font-semibold"><?php echo htmlspecialchars($userName); ?></p>
                <p class="text-sm text-muted-foreground">Production Manager</p>
            </div>
        </div>
        <a href="../../includes/logout.php" class="flex items-center gap-3 px-4 py-2 mt-4 rounded-lg text-muted-foreground hover:bg-muted hover:text-foreground transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H3"></path></svg>
            <span>Logout</span>
        </a>
    </div>
</aside>
