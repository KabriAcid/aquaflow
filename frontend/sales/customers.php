    <?php
    session_start();
    if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['sales', 'sales_manager'])) {
        header('Location: ../login.php');
        exit;
    }

    $page_title = "Manage Customers";

    include 'partials/header.php';
    include 'partials/sidebar.php';
    ?>

    <div class="container-fluid">
        <h1 class="text-2xl mb-4 text-gray-800 font-semibold">Manage Customers</h1>
        <div class="bg-white p-6 rounded-lg multi-shadow">
            <div class="mb-4 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-600">Customers</h3>
                <div>
                    <input id="searchFilter" type="text" placeholder="Search customers..." class="border rounded px-3 py-2" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="customersTable" class="w-full text-left table-auto">
                    <thead>
                        <tr class="text-sm text-gray-600 border-b">
                            <th class="py-2 px-3">Name</th>
                            <th class="py-2 px-3">Email</th>
                            <th class="py-2 px-3">Phone</th>
                            <th class="py-2 px-3">City</th>
                            <th class="py-2 px-3">Status</th>
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
            const tBody = document.querySelector('#customersTable tbody');
            const searchFilter = document.getElementById('searchFilter');
            let all = [];

            function API_BASE() {
                const parts = window.location.pathname.split('/');
                const idx = parts.indexOf('frontend');
                if (idx !== -1) return parts.slice(0, idx).join('/') + '/backend/api';
                return '/backend/api';
            }

            function escapeHtml(s) {
                if (s === null || s === undefined) return '';
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            fetch(API_BASE() + '/users/get_customers.php', {
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(res => {
                    if (!res || !res.success) {
                        tBody.innerHTML = '<tr><td colspan="6" class="py-6 text-center text-red-600">Failed to load customers</td></tr>';
                        return;
                    }
                    all = res.data || [];
                    render(all);
                }).catch(err => {
                    console.error(err);
                    tBody.innerHTML = '<tr><td colspan="6" class="py-6 text-center text-red-600">Network error</td></tr>';
                });

            function render(list) {
                tBody.innerHTML = '';
                if (!list || list.length === 0) {
                    tBody.innerHTML = '<tr><td colspan="6" class="py-6 text-center text-gray-500">No customers</td></tr>';
                    return;
                }
                list.forEach(c => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b text-sm';
                    tr.innerHTML = `
                <td class="py-3 px-3">${escapeHtml(c.full_name)}</td>
                <td class="py-3 px-3">${escapeHtml(c.email)}</td>
                <td class="py-3 px-3">${escapeHtml(c.phone||'')}</td>
                <td class="py-3 px-3">${escapeHtml(c.city||'')}</td>
                <td class="py-3 px-3">${escapeHtml(c.status||'')}</td>
                <td class="py-3 px-3"><a href="view-customer.php?id=${c.id}" class="text-blue-600 text-sm">View</a></td>
            `;
                    tBody.appendChild(tr);
                });
            }

            function filter() {
                const q = (searchFilter.value || '').toLowerCase();
                if (!q) return render(all);
                render(all.filter(c => (c.full_name || '').toLowerCase().includes(q) || (c.email || '').toLowerCase().includes(q) || (c.phone || '').toLowerCase().includes(q)));
            }
            searchFilter.addEventListener('input', filter);
        });
    </script>

    <?php include 'partials/footer.php'; ?>