<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'production_manager') {
    header('Location: ../login.php');
    exit;
}

$page_title = "Manage Production";
?>

<?php include './partials/header.php'; ?>

<div class="flex-1 flex">
    <!-- Sidebar -->
    <?php include './partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 bg-gray-100 p-6 md:p-10">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Record Daily Production</h1>

            <!-- Production Recording Form -->
            <div class="bg-white p-8 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-6">New Production Log</h2>

                <form id="manage-production-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product Selection -->
                        <div>
                            <label for="product-id" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                            <select id="product-id" name="product-id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Loading products...</option>
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity Produced</label>
                            <input type="number" id="quantity" name="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required min="1">
                        </div>

                        <!-- Shift -->
                        <div>
                            <label for="shift" class="block text-sm font-medium text-gray-700 mb-1">Shift</label>
                            <select id="shift" name="shift" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select a shift...</option>
                                <option value="morning">Morning</option>
                                <option value="afternoon">Afternoon</option>
                                <option value="night">Night</option>
                            </select>
                        </div>
                    </div>

                    <!-- Submission Feedback -->
                    <div id="form-feedback" class="mt-6 hidden p-4 rounded-md"></div>

                    <!-- Submit Button -->
                    <div class="mt-8 text-right">
                        <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Record Production</button>
                    </div>
                </form>
            </div>

        </div>
    </main>
</div>

<script src="../js/manage-production.js"></script>

<?php include './partials/footer.php'; ?>