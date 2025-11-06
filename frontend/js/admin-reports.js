document.addEventListener('DOMContentLoaded', function () {
    const generateReportBtn = document.getElementById('generateReportBtn');
    const reportSpinner = document.getElementById('reportSpinner');
    const reportContent = document.getElementById('reportContent');
    const reportTable = document.getElementById('reportTable');
    const topProductsChartCanvas = document.getElementById('topProductsChart').getContext('2d');
    const salesOverTimeChartCanvas = document.getElementById('salesOverTimeChart').getContext('2d');

    let topProductsChart;
    let salesOverTimeChart;

    generateReportBtn.addEventListener('click', generateReport);

    async function generateReport() {
        reportSpinner.classList.remove('hidden');
        reportTable.innerHTML = '';

        try {
            const response = await fetch('http://127.0.0.1:5000/sales_report');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            renderCharts(data);
            renderReportTable(data.sales_data);

        } catch (error) {
            console.error('Error fetching sales report:', error);
            reportContent.innerHTML = `<p class="text-red-500">Error generating report: ${error.message}</p>`;
        } finally {
            reportSpinner.classList.add('hidden');
        }
    }

    function renderCharts(data) {
        // Destroy existing charts if they exist
        if (topProductsChart) {
            topProductsChart.destroy();
        }
        if (salesOverTimeChart) {
            salesOverTimeChart.destroy();
        }

        // Top Selling Products Chart
        const topProducts = data.top_products;
        topProductsChart = new Chart(topProductsChartCanvas, {
            type: 'bar',
            data: {
                labels: topProducts.map(p => p.product_name),
                datasets: [{
                    label: 'Total Quantity Sold',
                    data: topProducts.map(p => p.total_quantity_sold),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Sales Over Time Chart
        const salesOverTime = data.sales_over_time;
        salesOverTimeChart = new Chart(salesOverTimeChartCanvas, {
            type: 'line',
            data: {
                labels: salesOverTime.map(s => s.date),
                datasets: [{
                    label: 'Total Sales',
                    data: salesOverTime.map(s => s.total_sales),
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    function renderReportTable(salesData) {
        if (salesData.length === 0) {
            reportTable.innerHTML = '<p class="text-gray-500">No sales data available for the selected period.</p>';
            return;
        }

        const table = document.createElement('table');
        table.className = 'min-w-full divide-y divide-gray-200';

        const thead = document.createElement('thead');
        thead.className = 'bg-gray-50';
        thead.innerHTML = `
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Price</th>
                 <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
            </tr>
        `;
        table.appendChild(thead);

        const tbody = document.createElement('tbody');
        tbody.className = 'bg-white divide-y divide-gray-200';
        salesData.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">${item.order_id}</td>
                <td class="px-6 py-4 whitespace-nowrap">${item.product_name}</td>
                <td class="px-6 py-4 whitespace-nowrap">${item.quantity}</td>
                <td class="px-6 py-4 whitespace-nowrap">${item.total_price}</td>
                <td class="px-6 py-4 whitespace-nowrap">${item.order_date}</td>
            `;
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);

        reportTable.appendChild(table);
    }
});
