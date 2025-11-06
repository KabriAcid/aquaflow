document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product-id');
    const productionForm = document.getElementById('manage-production-form');
    const formFeedback = document.getElementById('form-feedback');

    if (!productSelect || !productionForm || !formFeedback) {
        console.error("Required elements for managing production are not found.");
        return;
    }

    // Fetch products to populate the dropdown
    function loadProducts() {
        fetch('../../backend/api/production/production.php')
            .then(response => response.json())
            .then(apiResponse => {
                if (!apiResponse.data) {
                    throw new Error("Invalid API response: No data field.");
                }
                populateProductDropdown(apiResponse.data);
            })
            .catch(error => {
                console.error("Error fetching products:", error);
                productSelect.innerHTML = '<option value="">Failed to load products</option>';
            });
    }

    // Populate the product dropdown
    function populateProductDropdown(products) {
        if (products.length === 0) {
            productSelect.innerHTML = '<option value="">No products available</option>';
            return;
        }

        products.forEach(product => {
            const option = document.createElement('option');
            option.value = product.id;
            option.textContent = product.product_name;
            productSelect.appendChild(option);
        });
    }

    // Handle the production form submission
    productionForm.addEventListener('submit', function(event) {
        event.preventDefault();
        formFeedback.textContent = ''; // Clear previous feedback
        formFeedback.classList.add('hidden');

        const formData = new FormData(productionForm);
        const data = {
            product_id: formData.get('product-id'),
            quantity: formData.get('quantity'),
            shift: formData.get('shift')
        };

        fetch('../../backend/api/production/production.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(apiResponse => {
            formFeedback.textContent = apiResponse.message;
            formFeedback.classList.remove('hidden');

            if (apiResponse.status === 200) {
                formFeedback.classList.remove('text-red-500');
                formFeedback.classList.add('text-green-600');
                productionForm.reset(); // Reset the form on success
            } else {
                formFeedback.classList.remove('text-green-600');
                formFeedback.classList.add('text-red-500');
            }
        })
        .catch(error => {
            console.error("Error recording production:", error);
            formFeedback.textContent = "An unexpected error occurred. Please try again.";
            formFeedback.classList.remove('hidden', 'text-green-600');
            formFeedback.classList.add('text-red-500');
        });
    });

    // Initial load of products
    loadProducts();
});
