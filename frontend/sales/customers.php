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
    <link rel="stylesheet" href="../css/tailwind.css">
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
            <div class="bg-white p-8 rounded-lg shadow-md">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">Customer ID</th>
                            <th class="py-2 px-4 border-b">Name</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">Phone</th>
                            <th class="py-2 px-4 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="customersTableBody">
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
                    const row = `
                        <tr>
                            <td class="py-2 px-4 border-b">${customer.id}</td>
                            <td class="py-2 px-4 border-b">${customer.full_name}</td>
                            <td class="py-2 px-4 border-b">${customer.email}</td>
                            <td class="py-2 px-4 border-b">${customer.phone}</td>
                            <td class="py-2 px-4 border-b">
                                <a href="customer-details.php?id=${customer.id}" class="text-blue-500 hover:underline">View Details</a>
                            </td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            }
        });
    </script>

</body>
</html>
