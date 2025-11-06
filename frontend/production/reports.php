<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'production_manager' || !isset($_SESSION['production_manager_id'])) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Production Reports";
?>

<?php include './partials/header.php'; ?>

<div class="flex-1 flex">
    <!-- Sidebar -->
    <?php include './partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 p-6 md:p-10">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Production Reports</h1>

            <!-- Placeholder for content -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-4">Reports</h2>
                <p class="text-gray-600">This page will contain various production reports, such as daily and monthly output summaries, material consumption reports, and efficiency analyses. The specific reports will be implemented in a future update.</p>
            </div>

        </div>
    </main>
</div>

<?php include './partials/footer.php'; ?>