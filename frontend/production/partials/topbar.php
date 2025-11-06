<?php
// It is assumed that a session has been started on the page including this partial
$userName = $_SESSION['user_name'] ?? 'Production Manager';
?>
<header class="flex-1 flex flex-col">
    <div class="bg-card text-card-foreground shadow-md p-4 flex items-center justify-between">
        <div>
            <button class="md:hidden">
                <svg class="w-6 h-6 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
        <div class="flex items-center gap-4">
            <div class="relative">
                <button class="focus:outline-none">
                    <svg class="w-6 h-6 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </button>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-muted flex items-center justify-center font-bold text-muted-foreground">
                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                </div>
                <div>
                    <p class="font-semibold"><?php echo htmlspecialchars($userName); ?></p>
                    <p class="text-sm text-muted-foreground">Production Manager</p>
                </div>
            </div>
        </div>
    </div>
    <main class="flex-1 p-6 bg-muted/40">
        <!-- Content goes here -->
