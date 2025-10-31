document.addEventListener('DOMContentLoaded', function() {
    const customersTableBody = document.querySelector('#customers-table tbody');
    const searchInput = document.getElementById('search-input');

    const apiEndpoint = '../api/ajax.php';

    // Function to render customer rows
    const renderCustomers = (customers) => {
        customersTableBody.innerHTML = ''; // Clear existing rows
        if (customers.length === 0) {
            customersTableBody.innerHTML = '<tr><td colspan="4" class="p-3 text-center">No customers found.</td></tr>';
            return;
        }
        customers.forEach(customer => {
            const row = document.createElement('tr');
            row.className = 'border-b';
            row.innerHTML = `
                <td class="p-3">${escapeHTML(customer.name)}</td>
                <td class="p-3">${escapeHTML(customer.email)}</td>
                <td class="p-3">${new Date(customer.created_at).toLocaleDateString()}</td>
                <td class="p-3">
                    <button class="text-blue-600 hover:underline view-details-btn" data-id="${customer.id}">Details</button>
                </td>
            `;
            customersTableBody.appendChild(row);
        });
    };

    // Function to fetch customers
    const fetchCustomers = (searchTerm = '') => {
        const url = `${apiEndpoint}?action=get_customers&search=${encodeURIComponent(searchTerm)}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.data)) {
                    renderCustomers(data.data);
                } else {
                    customersTableBody.innerHTML = `<tr><td colspan="4" class="p-3 text-center text-red-500">Error: ${data.message || 'Could not fetch customers.'}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error fetching customers:', error);
                customersTableBody.innerHTML = '<tr><td colspan="4" class="p-3 text-center text-red-500">An error occurred while fetching data.</td></tr>';
            });
    };

    // Event listener for the search input
    searchInput.addEventListener('input', () => {
        fetchCustomers(searchInput.value);
    });

    // Event delegation for view details buttons
    customersTableBody.addEventListener('click', (event) => {
        if (event.target.classList.contains('view-details-btn')) {
            const customerId = event.target.getAttribute('data-id');
            window.location.href = `customer-details.php?id=${customerId}`;
        }
    });

    // Initial fetch of customers
    fetchCustomers();
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
