<?php
session_start();
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['sales', 'sales_manager'])) {
    header("Location: ../login.php");
    exit;
}

$page_title = "Order Details";
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    header("Location: orders.php");
    exit;
}

include 'partials/header.php';

// Set active page for sidebar - order-details belongs to 'orders'
$active_page_override = 'orders.php';

include 'partials/sidebar.php';
?>

<div class="container-fluid">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Order Details</h1>
        <a href="orders.php" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Back to Orders</a>
    </div>

    <div id="orderDetails" class="bg-white p-4 md:p-8 rounded-lg multi-shadow">
        <!-- Order details will be loaded here -->
        <p>Loading order details...</p>
    </div>
</div>

<script src="../js/utils.js"></script>
<script>
    // Fallback formatting functions if utils.js doesn't load
    if (typeof formatNaira === 'undefined') {
        window.formatNaira = function(amount, decimals = 2) {
            if (amount === null || amount === undefined || isNaN(amount)) {
                return "₦0.00";
            }
            const num = parseFloat(amount);
            return "₦" + num.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        };
    }

    if (typeof formatDate === 'undefined') {
        window.formatDate = function(date) {
            if (!date) return "";
            return new Date(date).toLocaleDateString();
        };
    }

    if (typeof capitalizeWords === 'undefined') {
        window.capitalizeWords = function(str) {
            if (!str) return "";
            return str.replace(/\b\w/g, (char) => char.toUpperCase());
        };
    }

    document.addEventListener('DOMContentLoaded', function() {
        const orderId = <?php echo json_encode($order_id); ?>;

        fetch(`../../backend/api/orders/get_single.php?id=${orderId}`)
            .then(response => {
                if (response.status === 401) {
                    window.location.href = '../login.php';
                    return Promise.reject('Unauthorized');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const order = data.data;
                    const orderDetailsDiv = document.getElementById('orderDetails');
                    let itemsHtml = '';
                    order.items.forEach(item => {
                        const lineTotal = (parseFloat(item.quantity) * parseFloat(item.unit_price)) || 0;
                        itemsHtml += `
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 text-sm text-gray-800">${escapeHtml(item.product_name)}</td>
                                <td class="py-3 px-4 text-sm text-gray-700 text-center">${escapeHtml(String(item.quantity))}</td>
                                <td class="py-3 px-4 text-sm text-gray-700 text-right">${formatNaira(parseFloat(item.unit_price) || 0)}</td>
                                <td class="py-3 px-4 text-sm text-gray-700 text-right">${formatNaira(lineTotal)}</td>
                            </tr>
                        `;
                    });

                    orderDetailsDiv.innerHTML = `
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div><strong>Order ID:</strong> ${escapeHtml(order.order_number || '')}</div>
                            <div><strong>Order Date:</strong> ${formatDate(order.order_date || '')}</div>
                            <div><strong>Customer:</strong> ${escapeHtml(order.customer_name || '')}</div>
                            <div><strong>Status:</strong> <span class="${getStatusClasses(order.status)} text-xs font-semibold px-2 py-1 rounded-full">${capitalizeWords(escapeHtml(order.status || ''))}</span></div>
                        </div>
                        <h2 class="text-xl font-bold mb-4">Items</h2>
                        <div class="bg-white overflow-auto rounded">
                        <table class="min-w-full w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-semibold text-gray-600 uppercase">Product</th>
                                    <th class="py-3 px-4 text-center text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                                    <th class="py-3 px-4 text-right text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                                    <th class="py-3 px-4 text-right text-xs font-semibold text-gray-600 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                ${itemsHtml}
                            </tbody>
                        </table>
                        </div>
                        <div class="text-right mt-4">
                            <h2 class="text-2xl font-bold">Total: ${formatNaira(parseFloat(order.total_amount) || 0)}</h2>
                        </div>
                    `;
                } else {
                    alert('Failed to load order details: ' + data.message);
                }
            })
            .catch(error => {
                if (error !== 'Unauthorized') {
                    console.error('Error fetching order details:', error);
                    alert('An error occurred while fetching order details.');
                }
            });

        function getStatusClasses(status) {
            switch ((status || '').toLowerCase()) {
                case 'pending':
                    return 'bg-yellow-100 text-yellow-800';
                case 'delivered':
                    return 'bg-green-100 text-green-800';
                case 'cancelled':
                    return 'bg-red-100 text-red-800';
                case 'processing':
                    return 'bg-blue-100 text-blue-800';
                case 'out_for_delivery':
                    return 'bg-purple-100 text-purple-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
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

<?php include 'partials/footer.php'; ?>