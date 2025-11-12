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

  // Progress bar elements
  const loadingProgress = document.getElementById("loadingProgress");
  const progressBar = document.getElementById("progressBar");
  const progressPercentage = document.getElementById("progressPercentage");
  const loadingStatus = document.getElementById("loadingStatus");
  const reportBtnText = document.getElementById("reportBtnText");

  // Simulate progressive loading
  const simulateProgress = () => {
    return new Promise((resolve) => {
      let progress = 0;
      const statuses = [
        { percent: 10, text: "Connecting to database..." },
        { percent: 25, text: "Fetching sales data..." },
        { percent: 40, text: "Analyzing top products..." },
        { percent: 60, text: "Processing sales trends..." },
        { percent: 75, text: "Compiling recent orders..." },
        { percent: 90, text: "Generating charts..." },
        { percent: 100, text: "Finalizing report..." },
      ];

      const interval = setInterval(() => {
        if (progress >= 100) {
          clearInterval(interval);
          resolve();
          return;
        }

        const nextStatus = statuses.find((s) => s.percent > progress);
        if (nextStatus) {
          progress = nextStatus.percent;
          progressBar.style.width = `${progress}%`;
          progressPercentage.textContent = `${progress}%`;
          loadingStatus.textContent = nextStatus.text;
        }
      }, 400);
    });
  };

  const generateReport = async () => {
    // Show loading UI
    reportSpinner.classList.remove("hidden");
    loadingProgress.classList.remove("hidden");
    reportContent.classList.add("hidden");
    generateReportBtn.disabled = true;
    reportBtnText.textContent = "Generating...";

    // Reset progress
    progressBar.style.width = "0%";
    progressPercentage.textContent = "0%";
    loadingStatus.textContent = "Initializing...";

    try {
      // Start progress simulation
      const progressPromise = simulateProgress();

      // Fetch data
      const response = await fetch(API_URL, {
        credentials: "same-origin",
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();

      // Wait for progress animation to complete
      await progressPromise;

      if (result.success && result.data) {
        const data = result.data;
        renderCharts(data);
        renderTable(data.recent_orders);
        reportContent.classList.remove("hidden");

        // Show success message
        const successMsg = document.createElement("div");
        successMsg.className =
          "fixed top-4 right-4 bg-green-100 text-green-700 border border-green-300 rounded-lg p-4 shadow-lg z-50 animate-slide-in";
        successMsg.innerHTML = `
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Report generated successfully!</span>
          </div>
        `;
        document.body.appendChild(successMsg);
        setTimeout(() => {
          successMsg.remove();
        }, 3000);
      } else {
        reportContent.innerHTML =
          '<p class="text-center text-gray-500">No data available to generate a report.</p>';
        reportContent.classList.remove("hidden");
      }
    } catch (error) {
      console.error("Error generating report:", error);
      reportContent.innerHTML = `
        <div class="text-center py-8">
          <svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="text-red-600 font-semibold mb-2">Failed to generate report</p>
          <p class="text-gray-500 text-sm">Please ensure the Python microservice is running on port 5001</p>
        </div>
      `;
      reportContent.classList.remove("hidden");
    } finally {
      reportSpinner.classList.add("hidden");
      loadingProgress.classList.add("hidden");
      generateReportBtn.disabled = false;
      reportBtnText.textContent = "Generate Report";
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

  const getStatusBadge = (status) => {
    const statusColors = {
      pending: "bg-yellow-100 text-yellow-800",
      processing: "bg-blue-100 text-blue-800",
      ready: "bg-purple-100 text-purple-800",
      shipped: "bg-indigo-100 text-indigo-800",
      delivered: "bg-green-100 text-green-800",
      cancelled: "bg-red-100 text-red-800",
    };

    const colorClass =
      statusColors[status?.toLowerCase()] || "bg-gray-100 text-gray-800";
    const displayStatus = status
      ? status.charAt(0).toUpperCase() + status.slice(1)
      : "Unknown";

    return `<span class="px-2 py-1 rounded-full text-xs font-medium ${colorClass}">${displayStatus}</span>`;
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
                                <td class="p-2">${formatNaira(
                                  order.total_amount
                                )}</td>
                                <td class="p-2">${getStatusBadge(
                                  order.status
                                )}</td>
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
