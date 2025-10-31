<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'sales_manager') {
    header("Location: ../login.php");
    exit;
}

$page_title = "Edit Product";
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    header("Location: products.php");
    exit;
}

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
                <h1 class="text-3xl font-bold text-gray-800">Edit Product</h1>
                <a href="products.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Products</a>
            </div>

            <div class="bg-white p-4 md:p-8 rounded-lg multi-shadow">
                <form id="editProductForm">
                    <input type="hidden" id="productId" name="productId" value="<?php echo htmlspecialchars($product_id); ?>">
                    <div class="mb-4">
                        <label for="productName" class="block text-gray-700 text-sm font-bold mb-2">Product Name</label>
                        <input type="text" id="productName" name="productName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>
                    <div class="mb-4">
                        <label for="productDescription" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea id="productDescription" name="productDescription" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="unitPrice" class="block text-gray-700 text-sm font-bold mb-2">Unit Price (₦)</label>
                            <input type="number" id="unitPrice" name="unitPrice" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div>
                            <label for="currentStock" class="block text-gray-700 text-sm font-bold mb-2">Current Stock</label>
                            <input type="number" id="currentStock" name="currentStock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                    </div>
                    <div class="mb-6">
                        <label for="productImage" class="block text-gray-700 text-sm font-bold mb-2">Product Image URL</label>
                        <input type="text" id="productImage" name="productImage" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

        </main>

        <?php include 'partials/footer.php'; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productId = document.getElementById('productId').value;

            // Fetch product details to pre-fill the form
            fetch(`../../backend/api/products/get_single.php?id=${productId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.data;
                        document.getElementById('productName').value = product.name;
                        document.getElementById('productDescription').value = product.description;
                        document.getElementById('unitPrice').value = product.unit_price;
                        document.getElementById('currentStock').value = product.current_stock;
                        document.getElementById('productImage').value = product.image_url;
                    } else {
                        alert('Failed to load product details: ' + data.message);
                        window.location.href = 'products.php';
                    }
                })
                .catch(error => {
                    console.error('Error fetching product details:', error);
                    alert('An error occurred while fetching product details.');
                });

            document.getElementById('editProductForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);
                const productData = {
                    id: formData.get('productId'),
                    name: formData.get('productName'),
                    description: formData.get('productDescription'),
                    unit_price: parseFloat(formData.get('unitPrice')),
                    current_stock: parseInt(formData.get('currentStock')),
                    image_url: formData.get('productImage'),
                };

                fetch('../../backend/api/products/update.php', {
                        method: 'POST', // Using POST to handle the update
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(productData),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Product updated successfully!');
                            window.location.href = 'products.php';
                        } else {
                            alert('Failed to update product: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error updating product:', error);
                        alert('An error occurred while updating the product.');
                    });
            });
        });
    </script>

</body>

</html>