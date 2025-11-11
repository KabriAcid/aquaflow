document.addEventListener("DOMContentLoaded", function () {
  // Ensure formatting functions are available (fallback if utils.js doesn't load)
  if (typeof formatNaira === "undefined") {
    window.formatNaira = function (amount, decimals = 2) {
      if (amount === null || amount === undefined || isNaN(amount)) {
        return "₦0.00";
      }
      const num = parseFloat(amount);
      return "₦" + num.toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };
  }

  if (typeof formatDate === "undefined") {
    window.formatDate = function (date) {
      if (!date) return "";
      return new Date(date).toLocaleDateString();
    };
  }

  if (typeof capitalizeWords === "undefined") {
    window.capitalizeWords = function (str) {
      if (!str) return "";
      return str.replace(/\b\w/g, (char) => char.toUpperCase());
    };
  }

  const salesActivityCtx = document
    .getElementById("salesActivityChart")
    ?.getContext("2d");

  // Render a simple placeholder chart (will be updated after orders load)
  let salesChart = null;
  if (salesActivityCtx) {
    salesChart = new Chart(salesActivityCtx, {
      type: "line",
      data: {
        labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        datasets: [
          {
            label: "Orders",
            data: [0, 0, 0, 0, 0, 0, 0],
            borderColor: "#10B981",
            backgroundColor: "rgba(16, 185, 129, 0.2)",
            fill: true,
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } },
      },
    });
  }

  // small helper to escape HTML
  function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return "";
    return String(unsafe)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  // Compute API base relative to current path (handles subfolder deployments)
  function getApiBase() {
    const parts = window.location.pathname.split("/");
    const idx = parts.indexOf("frontend");
    if (idx !== -1) {
      const root = parts.slice(0, idx).join("/");
      return root + "/backend/api";
    }
    return "/backend/api";
  }
  const API_BASE = getApiBase();

  // Status badge helpers (match sales-orders.js)
  function statusLabel(s) {
    switch ((s || "").toLowerCase()) {
      case "processing":
        return "Processing";
      case "out_for_delivery":
        return "Out for delivery";
      case "delivered":
        return "Delivered";
      case "cancelled":
        return "Cancelled";
      case "pending":
      default:
        return "Pending";
    }
  }

  function statusBadgeClasses(s) {
    switch ((s || "").toLowerCase()) {
      case "processing":
        return "inline-block px-2 py-0.5 rounded text-white bg-blue-600";
      case "out_for_delivery":
        return "inline-block px-2 py-0.5 rounded text-white bg-indigo-600";
      case "delivered":
        return "inline-block px-2 py-0.5 rounded text-white bg-green-600";
      case "cancelled":
        return "inline-block px-2 py-0.5 rounded text-white bg-red-600";
      case "pending":
      default:
        return "inline-block px-2 py-0.5 rounded text-yellow-800 bg-yellow-100";
    }
  }

  function renderStatusBadge(s) {
    const label = statusLabel(s);
    const classes = statusBadgeClasses(s);
    return `<span class="${classes}">${escapeHtml(label)}</span>`;
  }

  // Fetch recent orders and populate stats + table
  function loadRecentOrders() {
    fetch(API_BASE + "/orders/get_all.php?limit=10", {
      credentials: "same-origin",
    })
      .then((r) => r.json())
      .then((res) => {
        if (!res.success) {
          console.error("Failed to load orders", res);
          return;
        }
        const orders = res.data || [];

        // compute stats
        const pending = orders.filter(
          (o) => (o.status || "").toLowerCase() === "pending"
        ).length;
        const salesTotal = orders.reduce(
          (acc, o) =>
            acc + (parseFloat(o.total_amount || o.total_amount || 0) || 0),
          0
        );
        // new customers count is not available from orders endpoint; leave as 0 for now
        const newCustomers = 0;

        document.getElementById("pendingCount").textContent = pending;
        document.getElementById("mySalesTotal").textContent =
          formatNaira(salesTotal);
        document.getElementById("newCustomersCount").textContent = newCustomers;

        // populate table
        const tbody = document.querySelector("#recentOrdersTable tbody");
        if (!tbody) return;
        tbody.innerHTML = "";
        if (orders.length === 0) {
          tbody.innerHTML =
            '<tr><td colspan="6" class="py-6 text-center text-gray-500">No recent orders</td></tr>';
        } else {
          orders.forEach((o) => {
            const tr = document.createElement("tr");
            tr.className = "border-b";
            const orderDate = new Date(o.order_date || o.created_at || "");
            tr.innerHTML = `
                            <td class="py-3 px-3"><a href="order-details.php?id=${
                              o.id
                            }" class="text-sm text-blue-600">${escapeHtml(
              o.order_number || "#" + o.id
            )}</a></td>
                            <td class="py-3 px-3 text-sm">${escapeHtml(
                              o.customer_name || o.customer_id || ""
                            )}</td>
                            <td class="py-3 px-3 text-sm">${formatNaira(
                              parseFloat(o.total_amount || 0) || 0
                            )}</td>
                            <td class="py-3 px-3 text-sm">${renderStatusBadge(
                              o.status || "pending"
                            )}</td>
                            <td class="py-3 px-3 text-sm">${capitalizeWords(
                              escapeHtml(o.payment_status || "")
                            )}</td>
                            <td class="py-3 px-3 text-sm">${
                              isNaN(orderDate.getTime())
                                ? escapeHtml(o.order_date || "")
                                : formatDate(o.order_date)
                            }</td>
                        `;
            tbody.appendChild(tr);
          });
        }

        // update chart data (simple aggregation by day name from order_date)
        if (salesChart) {
          const dayCounts = {
            Sun: 0,
            Mon: 0,
            Tue: 0,
            Wed: 0,
            Thu: 0,
            Fri: 0,
            Sat: 0,
          };
          orders.forEach((o) => {
            const d = new Date(o.order_date || o.created_at || "");
            if (!isNaN(d.getTime())) {
              const day = d.toLocaleDateString(undefined, { weekday: "short" });
              dayCounts[day] = (dayCounts[day] || 0) + 1;
            }
          });
          const labels = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
          salesChart.data.labels = labels;
          salesChart.data.datasets[0].data = labels.map(
            (l) => dayCounts[l] || 0
          );
          salesChart.update();
        }
      })
      .catch((err) => console.error("Could not load recent orders", err));
  }

  loadRecentOrders();

  // Hook order details modal behavior: intercept recent orders links and show modal
  function showOrderModal(orderId) {
    const modal = document.getElementById("orderDetailsModal");
    const content = document.getElementById("orderModalContent");
    if (!modal || !content) return;
    modal.classList.remove("hidden");
    content.innerHTML = '<p class="text-gray-500">Loading...</p>';
    fetch(
      API_BASE + `/orders/get_single.php?id=${encodeURIComponent(orderId)}`,
      { credentials: "same-origin" }
    )
      .then((r) => r.json())
      .then((res) => {
        if (!res || !res.success) {
          content.innerHTML = `<div class="text-red-600">${escapeHtml(
            (res && res.message) || "Failed to load order"
          )}</div>`;
          return;
        }
        const o = res.data;
        let html = `<div class="mb-3"><strong>Order #</strong> ${escapeHtml(
          o.order_number
        )}</div>`;
        html += `<div class="mb-2"><strong>Customer</strong> ${escapeHtml(
          o.customer_name || o.customer_id || ""
        )}</div>`;
        html += `<div class="mb-2"><strong>Status</strong> ${renderStatusBadge(
          o.status || "pending"
        )}</div>`;
        html += `<div class="mb-2"><strong>Payment</strong> ${capitalizeWords(
          escapeHtml(o.payment_status || "")
        )}</div>`;
        html += `<div class="mb-2"><strong>Amount</strong> ${formatNaira(
          parseFloat(o.total_amount || o.subtotal || 0) || 0
        )}</div>`;
        html +=
          '<div class="mt-4"><strong>Items</strong><ul class="list-disc pl-6">';
        (o.items || []).forEach((it) => {
          html += `<li>${escapeHtml(it.product_name)} — ${escapeHtml(
            String(it.quantity)
          )} × ${formatNaira(parseFloat(it.unit_price) || 0)}</li>`;
        });
        html += "</ul></div>";
        content.innerHTML = html;
      })
      .catch((err) => {
        console.error(err);
        content.innerHTML =
          '<div class="text-red-600">Network or server error</div>';
      });
  }

  document.addEventListener("click", function (e) {
    const a = e.target.closest('a[href^="order-details.php"]');
    if (a) {
      e.preventDefault();
      const url = new URL(a.href, window.location.href);
      const id = url.searchParams.get("id");
      if (id) showOrderModal(id);
    }
  });

  const modalClose = document.getElementById("orderModalClose");
  if (modalClose)
    modalClose.addEventListener("click", function () {
      const modal = document.getElementById("orderDetailsModal");
      if (modal) modal.classList.add("hidden");
    });
});
