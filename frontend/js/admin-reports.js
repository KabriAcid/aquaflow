document.addEventListener("DOMContentLoaded", () => {
  const generateReportBtn = document.getElementById("generateReportBtn");
  const reportSpinner = document.getElementById("reportSpinner");
  const reportContent = document.getElementById("reportContent");
  const topProductsCtx = document
    .getElementById("topProductsChart")
    .getContext("2d");
  const salesOverTimeCtx = document
    .getElementById("salesOverTimeChart")
    .getContext("2d");
  const reportTableContainer = document.getElementById("reportTable");

  let topProductsChart, salesOverTimeChart;

  const API_URL = "../../backend/api/reports/get_sales_report.php";
  const EXPORT_URL = "../../backend/api/reports/export_sales_report.php";

  const generateReport = async () => {
    reportSpinner.classList.remove("hidden");
    generateReportBtn.disabled = true;

    try {
      const response = await fetch(API_URL);
      const { data } = await response.json();

      if (data) {
        renderCharts(data);
        renderTable(data.recent_orders);
        reportContent.classList.remove("hidden");
      } else {
        reportContent.innerHTML =
          '<p class="text-center text-gray-500">No data available to generate a report.</p>';
      }
    } catch (error) {
      console.error("Error generating report:", error);
      reportContent.innerHTML =
        '<p class="text-center text-red-500">Failed to generate report. Please try again.</p>';
    } finally {
      reportSpinner.classList.add("hidden");
      generateReportBtn.disabled = false;
    }
  };

  const renderCharts = (data) => {
    // Destroy existing charts if they exist
    if (topProductsChart) topProductsChart.destroy();
    if (salesOverTimeChart) salesOverTimeChart.destroy();

    // Top Selling Products Chart
    topProductsChart = new Chart(topProductsCtx, {
      type: "bar",
      data: {
        labels: data.top_products.map((p) => p.name),
        datasets: [
          {
            label: "Quantity Sold",
            data: data.top_products.map((p) => p.total_quantity),
            backgroundColor: "rgba(59, 130, 246, 0.5)",
            borderColor: "rgba(59, 130, 246, 1)",
            borderWidth: 1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
      },
    });

    // Sales Over Time Chart
    salesOverTimeChart = new Chart(salesOverTimeCtx, {
      type: "line",
      data: {
        labels: data.sales_over_time.map((s) => s.date),
        datasets: [
          {
            label: "Total Sales",
            data: data.sales_over_time.map((s) => s.total_sales),
            fill: false,
            borderColor: "rgb(75, 192, 192)",
            tension: 0.1,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
      },
    });
  };

  const renderTable = (orders) => {
    if (!orders || orders.length === 0) {
      reportTableContainer.innerHTML =
        '<p class="text-center text-gray-500">No recent orders to display.</p>';
      return;
    }

    const table = `
            <h4 class="text-md font-semibold text-gray-600 mb-2 mt-6">Recent Orders</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-2">Order ID</th>
                            <th class="text-left p-2">Customer</th>
                            <th class="text-left p-2">Date</th>
                            <th class="text-left p-2">Total</th>
                            <th class="text-left p-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${orders
                          .map(
                            (order) => `
                            <tr class="border-b">
                                <td class="p-2">#${order.order_id}</td>
                                <td class="p-2">${order.customer_name}</td>
                                <td class="p-2">${new Date(
                                  order.order_date
                                ).toLocaleDateString()}</td>
                                <td class="p-2">$${parseFloat(
                                  order.total_amount
                                ).toFixed(2)}</td>
                                <td class="p-2">${order.status}</td>
                            </tr>
                        `
                          )
                          .join("")}
                    </tbody>
                </table>
            </div>
        `;
    reportTableContainer.innerHTML = table;
  };

  // Export report to CSV
  const exportReport = (reportType = "orders") => {
    const startDate = new Date();
    startDate.setDate(startDate.getDate() - 30);
    const endDate = new Date();

    const startDateStr = startDate.toISOString().split("T")[0];
    const endDateStr = endDate.toISOString().split("T")[0];

    const url = `${EXPORT_URL}?start_date=${startDateStr}&end_date=${endDateStr}&report_type=${reportType}`;

    // Create a temporary link and trigger download
    const link = document.createElement("a");
    link.href = url;
    link.download = `sales_report_${reportType}_${startDateStr}_to_${endDateStr}.csv`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  // Add export buttons after report is generated
  const addExportButtons = () => {
    const existingButtons = document.getElementById("export-buttons");
    if (existingButtons) {
      existingButtons.remove();
    }

    const exportContainer = document.createElement("div");
    exportContainer.id = "export-buttons";
    exportContainer.className = "mt-6 flex gap-3 justify-end";
    exportContainer.innerHTML = `
            <button onclick="window.exportOrdersCSV()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Orders
            </button>
            <button onclick="window.exportProductsCSV()" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Products
            </button>
            <button onclick="window.exportSummaryCSV()" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Summary
            </button>
        `;

    reportTableContainer.insertAdjacentElement("afterend", exportContainer);
  };

  // Expose export functions globally
  window.exportOrdersCSV = () => exportReport("orders");
  window.exportProductsCSV = () => exportReport("products");
  window.exportSummaryCSV = () => exportReport("summary");

  generateReportBtn.addEventListener("click", async () => {
    await generateReport();
    addExportButtons();
  });

  // Initially, hide the report content until a report is generated.
  reportContent.classList.add("hidden");
});
