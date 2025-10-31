<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
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
            <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Customers</h1>

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
            const token = localStorage.getItem('authToken');
            if (!token) {
                window.location.href = '../login.php';
                return;
            }

            fetch('../../backend/api/customers/get_all.php', {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tableBody = document.getElementById('customersTableBody');
                    tableBody.innerHTML = ''; // Clear existing rows
                    data.data.forEach(customer => {
                        const row = `
                            <tr>
                                <td class="py-2 px-4 border-b">${customer.id}</td>
                                <td class="py-2 px-4 border-b">${customer.name}</td>
                                <td class="py-2 px-4 border-b">${customer.email}</td>
                                <td class="py-2 px-4 border-b">${customer.phone}</td>
                                <td class="py-2 px-4 border-b"><a href="customer-details.php?id=${customer.id}" class="text-blue-500 hover:underline">View Details</a></td>
                            </tr>
                        `;
                        tableBody.innerHTML += row;
                    });
                } else {
                    alert('Failed to load customers: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching customers:', error);
                alert('An error occurred while fetching customers.');
            });
        });
    </script>

</body>
</html>
