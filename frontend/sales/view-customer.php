<?php
session_start();
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['sales', 'sales_manager'])) {
    header('Location: ../login.php');
    exit;
}

$page_title = "Customer Orders";

include 'partials/header.php';
include 'partials/sidebar.php';

$customer_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<div class="container-fluid">
    <div class="mb-6">
        <h1 class="h3 mb-4 text-gray-800 font-semibold inline">Customer Orders</h1> >
        <div id="customerInfo" class="inline">Loading customer...</div>
    </div>

    <div class="bg-white p-6 rounded-lg multi-shadow">
        <div class="overflow-x-auto">
            <table id="customerOrdersTable" class="w-full text-left table-auto">
                <thead>
                    <tr class="text-sm text-gray-600 border-b">
                        <th class="py-2 px-3">Order #</th>
                        <th class="py-2 px-3">Date</th>
                        <th class="py-2 px-3">Amount</th>
                        <th class="py-2 px-3">Status</th>
                        <th class="py-2 px-3">Payment</th>
                        <th class="py-2 px-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="6" class="py-6 text-center text-gray-500">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cid = <?php echo $customer_id ?: 0; ?>;
        const info = document.getElementById('customerInfo');
        const tbody = document.querySelector('#customerOrdersTable tbody');

        // Formatting functions
        function formatNaira(amount, decimals = 2) {
            if (amount === null || amount === undefined || isNaN(amount)) {
                return "₦0.00";
            }
            const num = parseFloat(amount);
            return "₦" + num.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // helper: map status to badge classes and render a small badge
        function statusBadgeClasses(s) {
            if (!s) return 'bg-gray-100 text-gray-800';
            s = s.toString().toLowerCase();
            if (s.includes('pending')) return 'bg-yellow-100 text-yellow-800';
            if (s.includes('processing') || s.includes('in_progress') || s.includes('in-progress')) return 'bg-blue-100 text-blue-800';
            if (s.includes('completed') || s.includes('delivered') || s.includes('paid')) return 'bg-green-100 text-green-800';
            if (s.includes('cancel') || s.includes('returned') || s.includes('failed')) return 'bg-red-100 text-red-800';
            return 'bg-gray-100 text-gray-800';
        }

        function statusLabel(s) {
            if (!s) return 'Unknown';
            s = s.toString().replace(/_/g, ' ');
            return s.split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
        }

        function renderStatusBadge(s) {
            const classes = statusBadgeClasses(s);
            const label = statusLabel(s);
            return `<span class="inline-block px-2 py-1 rounded-full text-xs font-medium ${classes}">${label}</span>`;
        }

        function renderPaymentStatus(paymentStatus) {
            const status = (paymentStatus || "").toLowerCase();
            if (status === "paid") {
                return `<span class="inline-flex items-center justify-center w-6 h-6 bg-green-100 rounded-full">
                    <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                </span>`;
            } else {
                return `<span class="inline-flex items-center justify-center w-6 h-6 bg-red-100 rounded-full">
                    <i data-lucide="x" class="w-4 h-4 text-red-600"></i>
                </span>`;
            }
        }

        function API_BASE() {
            const parts = window.location.pathname.split('/');
            const idx = parts.indexOf('frontend');
            if (idx !== -1) return parts.slice(0, idx).join('/') + '/backend/api';
            return '/backend/api';
        }

        if (!cid) {
            info.innerHTML = '<div class="text-red-600">Invalid customer id</div>';
            tbody.innerHTML = '';
            return;
        }

        // fetch customer list to get name (reuse customers endpoint)
        fetch(API_BASE() + '/users/get_customers.php', {
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(res => {
                if (res && res.success) {
                    const cust = (res.data || []).find(c => c.id == cid);
                    if (cust) info.innerHTML = `<strong>${cust.full_name}</strong> — ${cust.email}`;
                    else info.innerHTML = 'Customer not found';
                }
            }).catch(err => console.error(err));

        fetch(API_BASE() + '/users/get_orders.php?id=' + encodeURIComponent(cid), {
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(res => {
                if (!res || !res.success) {
                    tbody.innerHTML = '<tr><td colspan="6" class="py-6 text-center text-red-600">Failed to load orders</td></tr>';
                    return;
                }
                const orders = res.data || [];
                if (orders.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="py-6 text-center text-gray-500">No orders</td></tr>';
                    return;
                }
                tbody.innerHTML = '';
                orders.forEach(o => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b text-sm';
                    const dt = new Date(o.order_date || o.created_at || '');
                    tr.innerHTML = `
          <td class="py-3 px-3"><a href="order-details.php?id=${o.id}" class="text-blue-600">${o.order_number}</a></td>
          <td class="py-3 px-3">${isNaN(dt.getTime())? (o.order_date||'') : dt.toLocaleString()}</td>
          <td class="py-3 px-3">${formatNaira(parseFloat(o.total_amount)||0)}</td>
          <td class="py-3 px-3">${renderStatusBadge(o.status || 'pending')}</td>
          <td class="py-3 px-3 text-center">${renderPaymentStatus(o.payment_status || '')}</td>
          <td class="py-3 px-3"><a href="order-details.php?id=${o.id}" class="text-blue-600">View</a></td>
        `;
                    tbody.appendChild(tr);
                });
            }).catch(err => {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="6" class="py-6 text-center text-red-600">Network error</td></tr>';
            });
    });
</script>

<?php include 'partials/footer.php'; ?>