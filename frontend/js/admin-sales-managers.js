document.addEventListener('DOMContentLoaded', function() {
    const addManagerBtn = document.getElementById('add-manager-btn');
    const addManagerModal = document.getElementById('add-manager-modal');
    const cancelBtn = document.getElementById('cancel-btn');
    const addManagerForm = document.getElementById('add-manager-form');
    const salesManagersTable = document.getElementById('sales-managers-table').querySelector('tbody');

    // Show the modal
    addManagerBtn.addEventListener('click', () => {
        addManagerModal.classList.remove('hidden');
    });

    // Hide the modal
    cancelBtn.addEventListener('click', () => {
        addManagerModal.classList.add('hidden');
    });

    // Handle form submission
    addManagerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const res = await fetch('../backend/api/users/create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, email, password })
            });

            if (res.ok) {
                addManagerModal.classList.add('hidden');
                fetchSalesManagers();
            } else {
                const error = await res.json();
                alert(error.message);
            }
        } catch (error) {
            console.error('Error creating sales manager:', error);
        }
    });

    // Fetch and display sales managers
    async function fetchSalesManagers() {
        try {
            const res = await fetch('../backend/api/users/get_all.php');
            const users = await res.json();

            salesManagersTable.innerHTML = ''; // Clear existing rows

            const salesManagers = users.data.filter(user => user.role === 'sales_manager');

            if (salesManagers.length > 0) {
                salesManagers.forEach(manager => {
                    const row = document.createElement('tr');
                    row.classList.add('border-b');
                    row.innerHTML = `
                        <td class="p-3">${manager.name}</td>
                        <td class="p-3">${manager.email}</td>
                        <td class="p-3">
                            <button class="text-blue-500 hover:underline">Edit</button>
                            <button class="text-red-500 hover:underline ml-4">Delete</button>
                        </td>
                    `;
                    salesManagersTable.appendChild(row);
                });
            } else {
                salesManagersTable.innerHTML = '<p>No sales managers found.</p>';
            }
        } catch (error) {
            console.error('Error fetching sales managers:', error);
        }
    }

    fetchSalesManagers();
});
