document.addEventListener('DOMContentLoaded', function() {
    const inventoryTableBody = document.querySelector('#inventory-table-body');
    const loadingIndicator = document.getElementById('loading-indicator');

    if (!inventoryTableBody || !loadingIndicator) {
        console.error("Required elements for inventory management are not found.");
        return;
    }

    // Function to fetch and render inventory data
    function loadInventory() {
        loadingIndicator.style.display = 'block';
        inventoryTableBody.innerHTML = ''; // Clear existing data

        fetch('../../backend/api/production/inventory.php')
            .then(response => response.json())
            .then(apiResponse => {
                if (!apiResponse.data) {
                    throw new Error("Invalid API response: No data field.");
                }
                renderInventory(apiResponse.data);
                loadingIndicator.style.display = 'none';
            })
            .catch(error => {
                console.error("Error fetching inventory:", error);
                loadingIndicator.style.display = 'none';
                inventoryTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-red-500 py-4">Failed to load inventory.</td></tr>`;
            });
    }

    // Function to render the inventory table
    function renderInventory(inventory) {
        if (inventory.length === 0) {
            inventoryTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-gray-500 py-4">No inventory data available.</td></tr>`;
            return;
        }

        inventory.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-3 px-4 border-b">${item.product_name}</td>
                <td class="py-3 px-4 border-b text-right">${item.quantity.toLocaleString()}</td>
                <td class="py-3 px-4 border-b">${new Date(item.last_updated).toLocaleString()}</td>
                <td class="py-3 px-4 border-b text-center">
                    <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600" onclick="openUpdateModal(${item.id}, '${item.product_name}', ${item.quantity})">Update</button>
                </td>
            `;
            inventoryTableBody.appendChild(row);
        });
    }

    // Initial load of inventory data
    loadInventory();
});

// Function to open the update modal
function openUpdateModal(productId, productName, currentQuantity) {
    const modal = document.getElementById('update-inventory-modal');
    if (!modal) return;

    modal.querySelector('#modal-product-name').textContent = productName;
    modal.querySelector('#update-quantity').value = currentQuantity;
    modal.querySelector('#update-product-id').value = productId;
    modal.classList.remove('hidden');
}

// Function to close the update modal
function closeUpdateModal() {
    const modal = document.getElementById('update-inventory-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Handle form submission for updating inventory
document.getElementById('update-inventory-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const productId = document.getElementById('update-product-id').value;
    const newQuantity = document.getElementById('update-quantity').value;

    fetch('../../backend/api/production/inventory.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId, quantity: newQuantity })
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message); // Show success/error message
        if (data.status === 200) {
            closeUpdateModal();
            document.dispatchEvent(new CustomEvent('inventoryUpdated'));
        }
    })
    .catch(error => {
        console.error("Error updating inventory:", error);
        alert("Failed to update inventory.");
    });
});

document.addEventListener('inventoryUpdated', function(){
    // Refreshes the inventory table when an update happens
    document.querySelector('#inventory-table-body').innerHTML = '';
    document.getElementById('loading-indicator').style.display = 'block';

    fetch('../../backend/api/production/inventory.php')
        .then(response => response.json())
        .then(apiResponse => {
            if (!apiResponse.data) {
                throw new Error("Invalid API response: No data field.");
            }
            renderInventory(apiResponse.data);
            document.getElementById('loading-indicator').style.display = 'none';
        })
        .catch(error => {
            console.error("Error fetching inventory:", error);
            document.getElementById('loading-indicator').style.display = 'none';
            document.querySelector('#inventory-table-body').innerHTML = `<tr><td colspan="4" class="text-center text-red-500 py-4">Failed to load inventory.</td></tr>`;
        });
});

function renderInventory(inventory) {
        const inventoryTableBody = document.querySelector('#inventory-table-body');
        if (inventory.length === 0) {
            inventoryTableBody.innerHTML = `<tr><td colspan="4" class="text-center text-gray-500 py-4">No inventory data available.</td></tr>`;
            return;
        }

        inventory.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="py-3 px-4 border-b">${item.product_name}</td>
                <td class="py-3 px-4 border-b text-right">${item.quantity.toLocaleString()}</td>
                <td class="py-3 px-4 border-b">${new Date(item.last_updated).toLocaleString()}</td>
                <td class="py-3 px-4 border-b text-center">
                    <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600" onclick="openUpdateModal(${item.id}, '${item.product_name}', ${item.quantity})">Update</button>
                </td>
            `;
            inventoryTableBody.appendChild(row);
        });
    }
