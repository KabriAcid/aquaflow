document.addEventListener('DOMContentLoaded', () => {
    const ordersTbody = document.getElementById('orders-tbody');
    const searchInput = document.getElementById('search-input');
    const statusFilter = document.getElementById('status-filter');
    const orderDetailsModal = document.getElementById('order-details-modal');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const modalContent = document.getElementById('modal-content');
    const updateStatusSelect = document.getElementById('update-status-select');
    const updateStatusBtn = document.getElementById('update-status-btn');

    let allOrders = [];
    let currentOrderId = null;

    const API_URL = '../../backend/api/orders/get_all.php';
    const UPDATE_URL = '../../backend/api/orders/update.php';

    const openModal = () => {
        orderDetailsModal.classList.remove('hidden');
        orderDetailsModal.classList.add('flex');
    };

    const closeModal = () => {
        orderDetailsModal.classList.add('hidden');
        orderDetailsModal.classList.remove('flex');
        currentOrderId = null;
    };

    closeModalBtn.addEventListener('click', closeModal);

    const renderOrders = (orders) => {
        ordersTbody.innerHTML = '';
        if (orders.length === 0) {
            ordersTbody.innerHTML = '<tr><td colspan="6" class="p-3 text-center text-gray-500">No orders found.</td></tr>';
            return;
        }

        orders.forEach(order => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="p-3 border-b">#${order.id}</td>
                <td class="p-3 border-b">${order.customer_name}</td>
                <td class="p-3 border-b">${new Date(order.order_date).toLocaleDateString()}</td>
                <td class="p-3 border-b">${formatNaira(order.total_amount)}</td>
                <td class="p-3 border-b">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-${getStatusColor(order.status)}-100 text-${getStatusColor(order.status)}-800">${order.status.charAt(0).toUpperCase() + order.status.slice(1)}</span>
                </td>
                <td class="p-3 border-b">
                    <button class="view-btn text-blue-500 hover:text-blue-700" data-id="${order.id}"><i class="fas fa-eye"></i> View</button>
                </td>
            `;
            ordersTbody.appendChild(tr);
        });
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'pending': return 'yellow';
            case 'processing': return 'blue';
            case 'shipped': return 'purple';
            case 'delivered': return 'green';
            case 'cancelled': return 'red';
            default: return 'gray';
        }
    };

    const fetchOrders = async () => {
        try {
            const response = await fetch(API_URL);
            const { data } = await response.json();
            allOrders = data;
            renderOrders(allOrders);
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    };

    const filterAndSearchOrders = () => {
        const searchTerm = searchInput.value.toLowerCase();
        const status = statusFilter.value;

        let filteredOrders = allOrders;

        if (status) {
            filteredOrders = filteredOrders.filter(order => order.status === status);
        }

        if (searchTerm) {
            filteredOrders = filteredOrders.filter(order => 
                order.order_id.toString().includes(searchTerm) || 
                order.customer_name.toLowerCase().includes(searchTerm)
            );
        }

        renderOrders(filteredOrders);
    };

    searchInput.addEventListener('input', filterAndSearchOrders);
    statusFilter.addEventListener('change', filterAndSearchOrders);

    ordersTbody.addEventListener('click', async (e) => {
        if (e.target.closest('.view-btn')) {
            const orderId = e.target.closest('.view-btn').dataset.id;
            currentOrderId = orderId;
            try {
                // Fetch detailed order info, including items
                const response = await fetch(`${API_URL}?order_id=${orderId}`);
                const { data } = await response.json();

                if (data) {
                    modalContent.innerHTML = `
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><strong>Order ID:</strong> #${data.order_id}</div>
                            <div><strong>Order Date:</strong> ${new Date(data.order_date).toLocaleString()}</div>
                            <div><strong>Customer:</strong> ${data.customer_name}</div>
                            <div><strong>Email:</strong> ${data.customer_email}</div>
                            <div class="col-span-2"><strong>Shipping Address:</strong> ${data.shipping_address}</div>
                        </div>
                        <div class="mt-6">
                            <h3 class="font-bold mb-2">Order Items</h3>
                            <ul class="border-t border-b divide-y">
                                ${data.items.map(item => `
                                    <li class="py-2 flex justify-between">
                                        <span>${item.product_name} (x${item.quantity})</span>
                                        <span>$${parseFloat(item.price).toFixed(2)}</span>
                                    </li>
                                `).join('')}
                            </ul>
                            <div class="mt-2 text-right font-bold">Total: $${parseFloat(data.total_amount).toFixed(2)}</div>
                        </div>
                    `;
                    updateStatusSelect.value = data.status;
                    openModal();
                } else {
                    console.error('Order details not found');
                }
            } catch (error) {
                console.error('Error fetching order details:', error);
            }
        }
    });

    updateStatusBtn.addEventListener('click', async () => {
        if (!currentOrderId) return;

        const newStatus = updateStatusSelect.value;
        
        try {
            const response = await fetch(UPDATE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: currentOrderId, status: newStatus })
            });
            const result = await response.json();

            if (result.status === 'success') {
                closeModal();
                fetchOrders(); // Refresh the list
            } else {
                console.error('Update failed:', result.message);
            }
        } catch (error) {
            console.error('Error updating status:', error);
        }
    });

    fetchOrders();
});
