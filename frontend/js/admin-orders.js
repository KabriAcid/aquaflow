document.addEventListener('DOMContentLoaded', function() {
    const ordersTableBody = document.querySelector('#orders-table tbody');
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');

    const apiEndpoint = '../api/ajax.php';

    const renderOrders = (orders) => {
        ordersTableBody.innerHTML = '';
        if (orders.length === 0) {
            ordersTableBody.innerHTML = '<tr><td colspan="6" class="p-3 text-center">No orders found.</td></tr>';
            return;
        }
        orders.forEach(order => {
            const row = document.createElement('tr');
            row.className = 'border-b';
            row.innerHTML = `
                <td class="p-3">#${order.id}</td>
                <td class="p-3">${escapeHTML(order.customer_name)}</td>
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

    const fetchOrders = (searchTerm = '', status = 'all') => {
        const url = `${apiEndpoint}?action=get_orders&search=${encodeURIComponent(searchTerm)}&status=${encodeURIComponent(status)}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    renderOrders(data.data);
                } else {
                    ordersTableBody.innerHTML = `<tr><td colspan="6" class="p-3 text-center text-red-500">Error: ${data.message || 'Could not fetch orders.'}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching orders:', error);
                ordersTableBody.innerHTML = '<tr><td colspan="6" class="p-3 text-center text-red-500">An error occurred while fetching data.</td></tr>';
            });
    };

    searchInput.addEventListener('input', () => {
        fetchOrders(searchInput.value, statusFilter.value);
    });

    statusFilter.addEventListener('change', () => {
        fetchOrders(searchInput.value, statusFilter.value);
    });

    // Initial fetch
    fetchOrders();
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
