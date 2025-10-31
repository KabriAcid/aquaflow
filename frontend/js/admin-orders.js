document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const ordersTable = document.getElementById('orders-table').querySelector('tbody');
    const orderDetailsModal = document.getElementById('order-details-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const modalContent = document.getElementById('modal-content');
    const updateStatusSelect = document.getElementById('update-status-select');
    const updateStatusBtn = document.getElementById('update-status-btn');

    let currentOrderId = null;

    const showModal = () => orderDetailsModal.classList.remove('hidden');
    const hideModal = () => {
        orderDetailsModal.classList.add('hidden');
        modalContent.innerHTML = '';
        currentOrderId = null;
    };

    closeModalBtn.addEventListener('click', hideModal);

    searchInput.addEventListener('input', () => fetchOrders(searchInput.value, statusFilter.value));
    statusFilter.addEventListener('change', () => fetchOrders(searchInput.value, statusFilter.value));

    // Event delegation for View Details button
    ordersTable.addEventListener('click', (e) => {
        if (e.target.classList.contains('view-details-btn')) {
            const orderId = e.target.dataset.id;
            currentOrderId = orderId;
            handleViewDetails(orderId);
        }
    });

    const handleViewDetails = async (id) => {
        try {
            const res = await fetch(`../backend/api/orders/get_single.php?id=${id}`);
            const result = await res.json();

            if (result.success) {
                const order = result.data;
                modalContent.innerHTML = `
                    <div class="grid grid-cols-2 gap-4">
                        <div><strong>Order ID:</strong> #${order.id}</div>
                        <div><strong>Order Date:</strong> ${new Date(order.order_date).toLocaleDateString()}</div>
                        <div><strong>Customer:</strong> ${order.customer_name}</div>
                        <div><strong>Email:</strong> ${order.customer_email}</div>
                        <div><strong>Shipping Address:</strong> ${order.shipping_address}</div>
                        <div><strong>Total:</strong> <span class="font-bold text-lg">$${order.total_amount}</span></div>
                    </div>
                    <h3 class="text-xl font-bold mt-6 mb-4">Order Items</h3>
                    <div id="order-items-container"></div>
                `;
                const itemsContainer = document.getElementById('order-items-container');
                order.items.forEach(item => {
                    const itemEl = document.createElement('div');
                    itemEl.className = 'flex justify-between items-center border-b pb-2 mb-2';
                    itemEl.innerHTML = `
                        <div>
                            <p class="font-semibold">${item.product_name}</p>
                            <p class="text-sm text-muted-foreground">Quantity: ${item.quantity}</p>
                        </div>
                        <p>$${item.price}</p>
                    `;
                    itemsContainer.appendChild(itemEl);
                });
                updateStatusSelect.value = order.status;
                showModal();
            } else {
                alert(`Error: ${result.message}`);
            }
        } catch (error) {
            console.error('Error fetching order details:', error);
            alert('An unexpected error occurred.');
        }
    };

    // Update Order Status
    updateStatusBtn.addEventListener('click', async () => {
        if (!currentOrderId) return;
        const newStatus = updateStatusSelect.value;
        try {
            const res = await fetch(`../backend/api/orders/update_status.php?id=${currentOrderId}`,
             {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus })
            });
            const result = await res.json();
            if (result.success) {
                hideModal();
                fetchOrders(); // Refresh the list
            } else {
                alert(`Error: ${result.message}`);
            }
        } catch (error) {
            console.error('Error updating status:', error);
            alert('An unexpected error occurred.');
        }
    });


    async function fetchOrders(searchTerm = '', status = '') {
        try {
            // The API needs to support these query parameters
            const res = await fetch(`../backend/api/orders/get_all.php?search=${searchTerm}&status=${status}`);
            const result = await res.json();

            ordersTable.innerHTML = ''; // Clear existing rows

            if (result.success && result.data.length > 0) {
                result.data.forEach(order => {
                    const row = document.createElement('tr');
                    row.classList.add('border-b');
                    row.innerHTML = `
                        <td class="p-3">#${order.id}</td>
                        <td class="p-3">${order.customer_name}</td>
                        <td class="p-3">${new Date(order.order_date).toLocaleDateString()}</td>
                        <td class="p-3">$${order.total_amount}</td>
                        <td class="p-3"><span class="status-${order.status}">${order.status}</span></td>
                        <td class="p-3">
                            <button class="view-details-btn text-blue-500 hover:underline" data-id="${order.id}">View Details</button>
                        </td>
                    `;
                    ordersTable.appendChild(row);
                });
            } else {
                ordersTable.innerHTML = '<tr><td colspan="6" class="p-3 text-center">No orders found.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    }

    fetchOrders();
});
