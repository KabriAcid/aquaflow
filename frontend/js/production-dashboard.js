document.addEventListener('DOMContentLoaded', () => {
    // KPI Card Elements
    const totalProductionEl = document.getElementById('total-production');
    const bottledWaterOutputEl = document.getElementById('bottled-water-output');
    const sparklingBeveragesOutputEl = document.getElementById('sparkling-beverages-output');
    const lowStockItemsEl = document.getElementById('low-stock-items');

    // Chart and Table Elements
    const trendsChartCanvas = document.getElementById('production-trends-chart');
    const inventoryTbody = document.getElementById('inventory-summary-tbody');

    const API_URL = '../../backend/api/production/dashboard.php';

    // Check if all required elements exist
    if (!totalProductionEl || !bottledWaterOutputEl || !sparklingBeveragesOutputEl || !lowStockItemsEl || !trendsChartCanvas || !inventoryTbody) {
        console.error("One or more dashboard elements are missing from the DOM.");
        return;
    }

    // Main function to fetch data and update the UI
    const fetchDashboardData = async () => {
        try {
            const response = await fetch(API_URL, { credentials: 'same-origin' });
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const apiResponse = await response.json();
            if (!apiResponse.data) {
                throw new Error("Invalid API response format: missing 'data' field.");
            }
            
            updateDashboardUI(apiResponse.data);

        } catch (error) {
            console.error("Error fetching dashboard data:", error);
        }
    };

    // Function to update all parts of the dashboard
    const updateDashboardUI = (data) => {
        renderKpiCards(data.daily_output, data.stock_levels);
        renderInventorySummary(data.stock_levels);
        renderTrendsChart(data.production_trends);
    };

    // Renders the KPI cards with fetched data
    const renderKpiCards = (dailyOutput, stockLevels) => {
        totalProductionEl.textContent = (dailyOutput.total || 0).toLocaleString();
        bottledWaterOutputEl.textContent = (dailyOutput.bottled_water || 0).toLocaleString();
        sparklingBeveragesOutputEl.textContent = (dailyOutput.sparkling_beverages || 0).toLocaleString();

        const lowStockCount = stockLevels.filter(item => item.quantity <= item.reorder_point).length;
        lowStockItemsEl.textContent = lowStockCount;
    };

    // Renders the inventory summary table
    const renderInventorySummary = (stockLevels) => {
        inventoryTbody.innerHTML = ''; // Clear previous data

        if (stockLevels.length === 0) {
            inventoryTbody.innerHTML = '<tr><td colspan="3" class="text-center text-gray-500 p-4">No inventory data.</td></tr>';
            return;
        }

        stockLevels.forEach(item => {
            const status = item.quantity > item.reorder_point
                ? '<span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-green-600 bg-green-200">In Stock</span>'
                : '<span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-red-600 bg-red-200">Low Stock</span>';

            const row = `
                <tr>
                    <td class="p-2 border-b text-sm">${item.product_name}</td>
                    <td class="p-2 border-b text-sm text-right">${item.quantity.toLocaleString()}</td>
                    <td class="p-2 border-b text-sm text-center">${status}</td>
                </tr>
            `;
            inventoryTbody.innerHTML += row;
        });
    };

    // Renders the production trends chart
    const renderTrendsChart = (trendsData) => {
        if (!trendsData || !trendsChartCanvas) return;
        new Chart(trendsChartCanvas, {
            type: 'line',
            data: {
                labels: trendsData.labels,
                datasets: [{
                    label: 'Daily Production Volume',
                    data: trendsData.data,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Units Produced' }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    };

    // Initial data fetch
    fetchDashboardData();
});
