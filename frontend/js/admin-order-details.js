document.addEventListener('DOMContentLoaded', function() {
    const orderDetailsContainer = document.getElementById('order-details-container');
    const orderId = new URLSearchParams(window.location.search).get('id');

    if (!orderId) {
        orderDetailsContainer.innerHTML = '<p class="text-red-500">No order ID provided.</p>';
        return;
    }

    const apiEndpoint = '../api/ajax.php';

    // Function to render order details
    const renderOrderDetails = (order) => {
        let itemsHtml = '<h3 class="text-xl font-bold mb-4">Items</h3><ul class="divide-y divide-gray-200">';
        order.items.forEach(item => {
            itemsHtml += `
                <li class="py-4 flex justify-between items-center">
                    <div>
                        <p class="font-semibold">${escapeHTML(item.name)}</p>
                        <p class="text-sm text-gray-500">Quantity: ${item.quantity}</p>
                    </div>
                    <p class="font-semibold">$${(item.price * item.quantity).toFixed(2)}</p>
                </li>
            `;
        });
        itemsHtml += '</ul>';

        orderDetailsContainer.innerHTML = `
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Order #${order.id}</h1>
                    <p class="text-gray-600">Placed on ${new Date(order.created_at).toLocaleDateString()}</p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-blue-600">$${parseFloat(order.total_amount).toFixed(2)}</p>
                    <p class="text-sm"><span class="status-badge status-${order.status.toLowerCase()}">${escapeHTML(order.status)}</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-2xl font-bold mb-4">Customer</h2>
                    <p class="font-semibold">${escapeHTML(order.customer.name)}</p>
                    <p class="text-gray-600">${escapeHTML(order.customer.email)}</p>
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-4">Shipping Address</h2>
                    <p>${escapeHTML(order.shipping_address)}</p>
                </div>
            </div>

            <div class="mt-8">
                ${itemsHtml}
            </div>
        `;
    };

    // Fetch order details
    fetch(`${apiEndpoint}?action=get_order_details&id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                renderOrderDetails(data.data);
            } else {
                orderDetailsContainer.innerHTML = `<p class="text-red-500">Error: ${data.message || 'Could not fetch order details.'}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
            orderDetailsContainer.innerHTML = '<p class="text-red-500">An error occurred while fetching order data.</p>';
        });
});

// Utility to prevent XSS
function escapeHTML(str) {
    if (str === null || str === undefined) {
        return '';
    }
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}
