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
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-800">Manage Customers</h1>
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 9a2 2 0 100-4 2 2 0 000 4zm0 2a4 4 0 00-4 4v2h8v-2a4 4 0 00-4-4zm0 0a2 2 0 100-4 2 2 0 000 4z"></path>
                </svg>
                <span class="text-gray-600 text-sm font-medium" id="customerCount">0 customers</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input id="searchFilter" type="text" placeholder="Search by name, email, or phone..." class="flex-1 bg-gray-50 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="customersTable" class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="py-3 px-6 text-left font-semibold text-gray-500">Name</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-500">Email</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-500">Phone</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-500">City</th>
                            <th class="py-3 px-6 text-left font-semibold text-gray-500">Status</th>
                            <th class="py-3 px-6 text-center font-semibold text-gray-500">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Loading customers...
                            </td>
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
            const customerCount = document.getElementById('customerCount');
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

            function getStatusBadge(status) {
                const statusMap = {
                    'active': {
                        bg: 'bg-green-100',
                        text: 'text-green-800',
                        label: 'Active'
                    },
                    'inactive': {
                        bg: 'bg-gray-100',
                        text: 'text-gray-800',
                        label: 'Inactive'
                    },
                    'suspended': {
                        bg: 'bg-red-100',
                        text: 'text-red-800',
                        label: 'Suspended'
                    }
                };
                const st = (status || 'active').toLowerCase();
                const config = statusMap[st] || statusMap['active'];
                return `<span class="inline-block px-3 py-1 rounded-full text-xs font-medium ${config.bg} ${config.text}">${escapeHtml(config.label)}</span>`;
            }

            fetch(API_BASE() + '/users/get_customers.php', {
                    credentials: 'same-origin'
                })
                .then(r => r.json())
                .then(res => {
                    if (!res || !res.success) {
                        tBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-red-600 font-medium"><svg class="w-12 h-12 mx-auto text-red-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Failed to load customers</td></tr>';
                        return;
                    }
                    all = res.data || [];
                    updateCount();
                    render(all);
                }).catch(err => {
                    console.error(err);
                    tBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-red-600 font-medium">Network error</td></tr>';
                });

            function updateCount() {
                const count = all.length;
                customerCount.textContent = `${count} customer${count !== 1 ? 's' : ''}`;
            }

            function render(list) {
                tBody.innerHTML = '';
                if (!list || list.length === 0) {
                    tBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-gray-500"><svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>No customers found</td></tr>';
                    return;
                }
                list.forEach((c, idx) => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150';
                    tr.innerHTML = `
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                    ${escapeHtml(c.full_name.charAt(0).toUpperCase())}
                                </div>
                                <div class="font-medium text-gray-900">${escapeHtml(c.full_name)}</div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-gray-800 font-medium">${escapeHtml(c.email)}</td>
                        <td class="py-4 px-6 text-gray-800 font-medium">${escapeHtml(c.phone || 'N/A')}</td>
                        <td class="py-4 px-6 text-gray-800 font-medium capitalize">${escapeHtml(c.city || 'N/A')}</td>
                        <td class="py-4 px-6">${getStatusBadge(c.status || 'active')}</td>
                        <td class="py-4 px-6 text-center">
                            <a href="view-customer.php?id=${c.id}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View
                            </a>
                        </td>
                    `;
                    tBody.appendChild(tr);
                });
            }

            function filter() {
                const q = (searchFilter.value || '').toLowerCase();
                if (!q) {
                    updateCount();
                    return render(all);
                }
                const filtered = all.filter(c => (c.full_name || '').toLowerCase().includes(q) || (c.email || '').toLowerCase().includes(q) || (c.phone || '').toLowerCase().includes(q));
                tBody.innerHTML = '';
                if (filtered.length === 0) {
                    tBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-gray-500"><svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>No results found</td></tr>';
                    return;
                }
                render(filtered);
            }
            searchFilter.addEventListener('input', filter);
        });
    </script>

    <?php include 'partials/footer.php'; ?>