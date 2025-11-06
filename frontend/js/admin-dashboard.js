document.addEventListener("DOMContentLoaded", function () {
  // Fetch dashboard metrics from backend and populate stat cards
  async function fetchDashboardData() {
    const salesEl = document.getElementById("total-sales");
    const customersEl = document.getElementById("total-customers");
    const ordersEl = document.getElementById("total-orders");
    const salesManagersEl = document.getElementById("total-sales-managers");

    try {
      // Helper to try multiple candidate URLs (root-relative then relative) to avoid broken relative-path issues
      const rootBase = `${location.origin}/aquaflow`;
      async function tryFetch(path) {
        const candidates = [
          rootBase + path,
          "../../" + path.replace(/^\/+/, ""),
        ];
        for (const url of candidates) {
          try {
            console.debug("Trying dashboard fetch URL:", url);
            const res = await fetch(url, { credentials: "same-origin" });
            if (res.ok) return res;
            // log non-ok and continue to next candidate
            console.warn("Fetch returned non-ok for", url, res.status);
          } catch (e) {
            console.warn("Fetch failed for", url, e);
          }
        }
        return null;
      }

      const salesRes = await tryFetch("/backend/api/sales/summary.php");
      const customersRes = await tryFetch("/backend/api/customers/get_all.php");
      const usersRes = await tryFetch("/backend/api/users/get_all.php");
      const ordersRes = await tryFetch("/backend/api/orders/get_all.php");

      // helper to safely parse JSON or return null
      async function safeJson(res) {
        if (!res.ok) {
          const text = await res.text();
          console.error("Dashboard fetch error", res.status, text);
          return null;
        }
        try {
          return await res.json();
        } catch (e) {
          console.error("Invalid JSON from", res.url, e);
          return null;
        }
      }

      const salesData = await safeJson(salesRes);
      const customersData = await safeJson(customersRes);
      const usersData = await safeJson(usersRes);
      const ordersData = await safeJson(ordersRes);

      if (salesData && salesData.data) {
        const totalSales = parseFloat(salesData.data.total_sales || 0).toFixed(
          2
        );
        if (salesEl) salesEl.textContent = `$${totalSales}`;
      }

      if (customersData && Array.isArray(customersData.data)) {
        if (customersEl) customersEl.textContent = customersData.data.length;
      }

      if (usersData && Array.isArray(usersData.data)) {
        // count users with role containing 'sales'
        const salesManagers = usersData.data.filter((u) =>
          (u.role || "").toLowerCase().includes("sales")
        ).length;
        if (salesManagersEl) salesManagersEl.textContent = salesManagers;
      }

      if (ordersData && Array.isArray(ordersData.data)) {
        if (ordersEl) ordersEl.textContent = ordersData.data.length;
      }
    } catch (err) {
      console.error("Error fetching dashboard data:", err);
    }
  }

  // kick off dashboard data fetch
  fetchDashboardData();

  // Dummy data for the charts
  const salesData = {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
    datasets: [
      {
        label: "Sales",
        data: [120, 150, 180, 220, 200, 250, 280],
        borderColor: "#3B82F6",
        backgroundColor: "rgba(59, 130, 246, 0.2)",
        fill: true,
        tension: 0.4,
      },
    ],
  };

  const topProductsData = {
    labels: ["Product A", "Product B", "Product C"],
    datasets: [
      {
        data: [300, 50, 100],
        backgroundColor: ["#3B82F6", "#10B981", "#F59E0B"],
        hoverOffset: 4,
      },
    ],
  };

  // Sales Overview Chart (Line)
  const salesOverviewCtx = document
    .getElementById("salesOverviewChart")
    ?.getContext("2d");
  if (salesOverviewCtx) {
    new Chart(salesOverviewCtx, {
      type: "line",
      data: salesData,
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
  }

  // Top Products Chart (Doughnut)
  const topProductsCtx = document
    .getElementById("topProductsChart")
    ?.getContext("2d");
  if (topProductsCtx) {
    new Chart(topProductsCtx, {
      type: "doughnut",
      data: topProductsData,
      options: {
        responsive: true,
        maintainAspectRatio: false,
      },
    });
  }
});
