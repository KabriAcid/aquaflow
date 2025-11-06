document.addEventListener('DOMContentLoaded', () => {
    // Main buttons and modal elements
    const addProductBtn = document.getElementById('add-product-btn');
    const productsGrid = document.getElementById('products-grid');

    // Add/Edit Product Modal
    const productModal = document.getElementById('product-modal');
    const productForm = document.getElementById('product-form');
    const cancelProductBtn = document.getElementById('cancel-product-btn');
    const productModalTitle = document.getElementById('product-modal-title');

    // Record Production Modal
    const recordProductionModal = document.getElementById('record-production-modal');
    const recordProductionForm = document.getElementById('record-production-form');
    const cancelRecordBtn = document.getElementById('cancel-record-btn');
    const recordProductName = document.getElementById('record-product-name');
    const recordFeedback = document.getElementById('record-feedback');

    // API Endpoints
    const API_PRODUCTS_URL = '../../backend/api/products.php';
    const API_PRODUCTION_URL = '../../backend/api/production/production.php';

    // Generic modal handler
    const openModal = (modal) => modal.classList.add('flex', 'items-center', 'justify-center');
    const closeModal = (modal) => modal.classList.remove('flex', 'items-center', 'justify-center');

    // Fetch and display all products
    const fetchProducts = async () => {
        try {
            const response = await fetch(API_PRODUCTS_URL, { credentials: 'same-origin' });
            const { data } = await response.json();

            productsGrid.innerHTML = '';
            data.forEach(product => {
                const card = `
                    <div class="bg-white p-4 rounded-lg shadow-md flex flex-col">
                        <img src="${product.image_url || '../images/default-product.png'}" alt="${product.name}" class="w-full h-40 object-cover rounded-t-lg mb-4">
                        <h3 class="text-lg font-bold text-gray-800">${product.name}</h3>
                        <p class="text-gray-600 text-sm flex-grow">${product.description}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-xl font-bold text-gray-800">₦${parseFloat(product.price).toFixed(2)}</span>
                            <button class="record-production-btn btn-secondary btn-sm" data-product-id="${product.id}" data-product-name="${product.name}">Record</button>
                        </div>
                    </div>
                `;
                productsGrid.innerHTML += card;
            });
        } catch (error) {
            console.error('Error fetching products:', error);
            productsGrid.innerHTML = '<p class="text-red-500 col-span-full">Failed to load products. Please try again.</p>';
        }
    };

    // --- Add/Edit Product Logic ---
    addProductBtn.addEventListener('click', () => {
        productForm.reset();
        document.getElementById('product_id').value = '';
        productModalTitle.textContent = 'Add New Product';
        openModal(productModal);
    });
    cancelProductBtn.addEventListener('click', () => closeModal(productModal));

    productForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(productForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(API_PRODUCTS_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.status && result.status === 'success') {
                closeModal(productModal);
                fetchProducts();
            } else {
                alert('Failed to save product: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error saving product:', error);
            alert('An error occurred while saving the product.');
        }
    });

    // --- Record Production Logic ---
    productsGrid.addEventListener('click', (e) => {
        const recordBtn = e.target.closest('.record-production-btn');
        if (recordBtn) {
            const { productId, productName } = recordBtn.dataset;
            document.getElementById('record_product_id').value = productId;
            recordProductName.textContent = productName;
            recordProductionForm.reset();
            recordFeedback.classList.add('hidden');
            openModal(recordProductionModal);
        }
    });

    cancelRecordBtn.addEventListener('click', () => closeModal(recordProductionModal));

    recordProductionForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(recordProductionForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(API_PRODUCTION_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });
            const result = await response.json();

            recordFeedback.textContent = result.message;
            recordFeedback.classList.remove('hidden');

            if (result.status === 200) {
                recordFeedback.classList.add('bg-green-100', 'text-green-700');
                recordFeedback.classList.remove('bg-red-100', 'text-red-700');
                setTimeout(() => closeModal(recordProductionModal), 2000); // Close after 2s
            } else {
                recordFeedback.classList.add('bg-red-100', 'text-red-700');
                recordFeedback.classList.remove('bg-green-100', 'text-green-700');
            }
        } catch (error) {
            console.error('Error recording production:', error);
            recordFeedback.textContent = 'An unexpected error occurred.';
            recordFeedback.classList.add('bg-red-100', 'text-red-700');
            recordFeedback.classList.remove('hidden');
        }
    });

    // Initial Load
    fetchProducts();
});
