document.addEventListener('DOMContentLoaded', function() {
    // Target elements where data will be rendered
    const dashboardContainer = document.querySelector('#production-dashboard-content');
    const trendsChartCanvas = document.getElementById('production-trends-chart');

    // Check if the necessary elements are on the page
    if (!dashboardContainer || !trendsChartCanvas) {
        console.error("Dashboard containers not found. Ensure the required HTML elements exist.");
        return;
    }

    // Fetch data from the production dashboard API
    fetch('../../backend/api/production/dashboard.php')
        .then(response => {
            // Check for a successful response, otherwise throw an error
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(apiResponse => {
            // Ensure the API response has a 'data' field
            if (!apiResponse.data) {
                throw new Error("Invalid API response format: missing 'data' field.");
            }
            const data = apiResponse.data;

            // Render the fetched data onto the dashboard
            renderDashboard(data);
        })
        .catch(error => {
            // Display an error message if the fetch fails
            console.error("Error fetching dashboard data:", error);
            dashboardContainer.innerHTML = `<div class="text-red-500">Failed to load dashboard data. Please check the console for details.</div>`;
        });

    /**
     * Renders the entire dashboard with data from the API.
     * @param {object} data - The data object from the API response.
     */
    function renderDashboard(data) {
        // Render the main metrics and stock levels
        renderMetrics(data.daily_output);
        renderStockLevels(data.stock_levels);

        // Render the production trends chart
        renderTrendsChart(data.production_trends);
    }

    /**
     * Renders the daily output metrics.
     * @param {object} dailyOutput - The daily output data.
     */
    function renderMetrics(dailyOutput) {
        const metricsContainer = document.getElementById('daily-output-metrics');
        if (!metricsContainer) return;

        metricsContainer.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-100 p-4 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-blue-800">Total Production</h3>
                    <p class="text-2xl font-bold">${dailyOutput.total.toLocaleString()} Units</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-green-800">Bottled Water</h3>
                    <p class="text-2xl font-bold">${dailyOutput.bottled_water.toLocaleString()} Units</p>
                </div>
                <div class="bg-purple-100 p-4 rounded-lg shadow-sm">
                    <h3 class="text-lg font-semibold text-purple-800">Sparkling Beverages</h3>
                    <p class="text-2xl font-bold">${dailyOutput.sparkling_beverages.toLocaleString()} Units</p>
                </div>
            </div>
        `;
    }

    /**
     * Renders the stock levels table.
     * @param {Array} stockLevels - An array of stock level objects.
     */
    function renderStockLevels(stockLevels) {
        const stockLevelsContainer = document.getElementById('stock-levels-summary');
        if (!stockLevelsContainer) return;

        const tableRows = stockLevels.map(item => {
            // Determine the stock status based on quantity vs. reorder point
            const status = item.quantity > item.reorder_point ? 
                '<span class="text-green-600 font-semibold">In Stock</span>' : 
                '<span class="text-red-600 font-semibold">Low Stock</span>';

            return `
                <tr>
                    <td class="py-2 px-4 border-b">${item.product_name}</td>
                    <td class="py-2 px-4 border-b text-right">${item.quantity.toLocaleString()}</td>
                    <td class="py-2 px-4 border-b text-center">${status}</td>
                </tr>
            `;
        }).join('');

        stockLevelsContainer.innerHTML = `
            <div class="bg-white p-6 rounded-lg shadow-lg mt-8">
                <h3 class="text-xl font-bold mb-4">Current Stock Levels</h3>
                <table class="min-w-full">
                    <thead>
                        <tr>
                            <th class="text-left py-2 px-4 border-b">Product</th>
                            <th class="text-right py-2 px-4 border-b">Quantity</th>
                            <th class="text-center py-2 px-4 border-b">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableRows}
                    </tbody>
                </table>
            </div>
        `;
    }

    /**
     * Renders the production trends chart using Chart.js.
     * @param {object} trendsData - The production trends data.
     */
    function renderTrendsChart(trendsData) {
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
                        title: {
                            display: true,
                            text: 'Units Produced'
                        }
                    }
                }
            }
        });
    }
});
