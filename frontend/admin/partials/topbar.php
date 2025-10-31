<?php
// Default page title if not set
$current_page_title = $page_title ?? 'Dashboard';
?>
<header class="bg-card text-card-foreground p-4 flex items-center justify-between shadow-md">
    <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($current_page_title); ?></h2>
    <div>
        <a href="../logout.php" class="text-muted-foreground hover:text-foreground">Logout</a>
    </div>
</header>
