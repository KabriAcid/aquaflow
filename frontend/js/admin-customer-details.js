document.addEventListener('DOMContentLoaded', function() {
    const customerDetailsContainer = document.getElementById('customer-details-container');
    const ordersTableBody = document.querySelector('#orders-table tbody');
    const customerId = new URLSearchParams(window.location.search).get('id');

    if (!customerId) {
        customerDetailsContainer.innerHTML = '<p class="text-red-500">No customer ID provided.</p>';
        return;
    }

    const apiEndpoint = '../api/ajax.php';

    // Function to render customer details
    const renderCustomerDetails = (customer) => {
        customerDetailsContainer.innerHTML = `
            <h1 class="text-3xl font-bold text-gray-800 mb-2">${escapeHTML(customer.name)}</h1>
            <p class="text-gray-600 mb-4">${escapeHTML(customer.email)}</p>
            <p class="text-sm text-gray-500">Customer since: ${new Date(customer.created_at).toLocaleDateString()}</p>
        `;
    };

    // Function to render order history
    const renderOrders = (orders) => {
        ordersTableBody.innerHTML = '';
        if (orders.length === 0) {
            ordersTableBody.innerHTML = '<tr><td colspan="5" class="p-3 text-center">This customer has no orders.</td></tr>';
            return;
        }
        orders.forEach(order => {
            const row = document.createElement('tr');
            row.className = 'border-b';
            row.innerHTML = `
                <td class="p-3">#${order.id}</td>
                <td class="p-3">${new Date(order.created_at).toLocaleDateString()}</td>
                <td class="p-3">$${parseFloat(order.total_amount).toFixed(2)}</td>
                <td class="p-3"><span class="status-badge status-${order.status.toLowerCase()}">${escapeHTML(order.status)}</span></td>
                <td class="p-3">
                    <a href="order-details.php?id=${order.id}" class="text-blue-600 hover:underline">View</a>
                </td>
            `;
            ordersTableBody.appendChild(row);
        });
    };

    // Fetch customer details
    fetch(`${apiEndpoint}?action=get_customer_details&id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                renderCustomerDetails(data.data);
            } else {
                customerDetailsContainer.innerHTML = `<p class="text-red-500">Error: ${data.message || 'Could not fetch customer details.'}</p>`;
            }
        })
        .catch(error => {
            console.error('Error fetching customer details:', error);
            customerDetailsContainer.innerHTML = '<p class="text-red-500">An error occurred while fetching customer data.</p>';
        });

    // Fetch customer order history
    fetch(`${apiEndpoint}?action=get_customer_orders&id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && Array.isArray(data.data)) {
                renderOrders(data.data);
            } else {
                ordersTableBody.innerHTML = `<tr><td colspan="5" class="p-3 text-center text-red-500">Error: ${data.message || 'Could not fetch order history.'}</td></tr>`;
            }
        })
        .catch(error => {
            console.error('Error fetching order history:', error);
            ordersTableBody.innerHTML = '<tr><td colspan="5" class="p-3 text-center text-red-500">An error occurred while fetching order data.</td></tr>';
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
