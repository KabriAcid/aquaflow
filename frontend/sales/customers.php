<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'sales_manager') {
    header("Location: ../login.php");
    exit;
}

$page_title = "Manage Customers";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Aquaflow</title>
    <link rel="shortcut icon" href="../../favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="bg-gray-100 flex">

    <?php include 'partials/sidebar.php'; ?>

    <div class="flex-1 flex flex-col">
        <?php include 'partials/topbar.php'; ?>

        <main class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">Manage Customers</h1>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <input type="text" id="searchInput" class="w-full p-3 border border-gray-300 rounded-lg" placeholder="Search by name or email...">
            </div>

            <!-- Customers Table -->
            <div class="bg-white p-4 md:p-8 rounded-lg multi-shadow overflow-auto">
                <table class="min-w-full w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                            <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Phone</th>
                            <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="customersTableBody" class="bg-white divide-y divide-gray-100">
                        <!-- Customer rows will be inserted here -->
                    </tbody>
                </table>
            </div>

        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let allCustomers = [];

            fetchCustomers();

            document.getElementById('searchInput').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const filteredCustomers = allCustomers.filter(customer =>
                    customer.full_name.toLowerCase().includes(searchTerm) ||
                    customer.email.toLowerCase().includes(searchTerm)
                );
                renderCustomers(filteredCustomers);
            });

            function fetchCustomers() {
                fetch('../../backend/api/customers/get_all.php')
                    .then(response => {
                        if (response.status === 401) {
                            window.location.href = '../login.php';
                            return Promise.reject('Unauthorized');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            allCustomers = data.data;
                            renderCustomers(allCustomers);
                        } else {
                            alert('Failed to load customers: ' + data.message);
                        }
                    })
                    .catch(error => {
                        if (error !== 'Unauthorized') {
                            console.error('Error fetching customers:', error);
                            alert('An error occurred while fetching customers.');
                        }
                    });
            }

            function renderCustomers(customers) {
                const tableBody = document.getElementById('customersTableBody');
                tableBody.innerHTML = ''; // Clear existing rows
                customers.forEach(customer => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50';
                    tr.innerHTML = `
                        <td class="py-3 px-4 text-sm text-gray-700">${escapeHtml(String(customer.id))}</td>
                        <td class="py-3 px-4 text-sm text-gray-800">${escapeHtml(customer.full_name || '')}</td>
                        <td class="py-3 px-4 text-sm text-gray-700">${escapeHtml(customer.email || '')}</td>
                        <td class="py-3 px-4 text-sm text-gray-700 text-center">${escapeHtml(customer.phone || '')}</td>
                        <td class="py-3 px-4 text-sm text-center">
                            <a href="customer-details.php?id=${encodeURIComponent(customer.id)}" class="text-blue-600 hover:underline">View Details</a>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });
            }

            function escapeHtml(text) {
                if (text === null || text === undefined) return '';
                return String(text).replace(/[&<>"'`]/g, function(s) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": "&#39;",
                        '`': '&#96;'
                    })[s];
                });
            }
        });
    </script>

</body>

</html>