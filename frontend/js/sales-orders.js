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

  const ordersTableBody = document.querySelector("#ordersTable tbody");
  const statusOptions = [
    { v: "pending", t: "Pending" },
    { v: "processing", t: "Processing" },
    { v: "out_for_delivery", t: "Out for delivery" },
    { v: "delivered", t: "Delivered" },
    { v: "cancelled", t: "Cancelled" },
  ];

  // Compute API base relative to the current app path so requests work when app is in a subfolder
  function getApiBase() {
    const parts = window.location.pathname.split("/");
    const idx = parts.indexOf("frontend");
    if (idx !== -1) {
      const root = parts.slice(0, idx).join("/"); // e.g. "" and "aquaflow" => "/aquaflow"
      return root + "/backend/api";
    }
    // fallback to root-based path
    return "/backend/api";
  }
  const API_BASE = getApiBase();

  function createAlert(msg, type = "success") {
    const alert = document.createElement("div");
    alert.className = `p-3 rounded mb-3 ${
      type === "success"
        ? "bg-green-100 text-green-800"
        : "bg-red-100 text-red-800"
    }`;
    alert.textContent = msg;
    const container = document.querySelector(".container-fluid");
    container.insertBefore(alert, container.firstChild);
    setTimeout(() => alert.remove(), 3500);
  }

  function safeJson(resp) {
    return resp.text().then((t) => {
      try {
        return JSON.parse(t);
      } catch (e) {
        // attach raw text for debugging
        return { __invalid_json: true, __raw: t, status: resp.status };
      }
    });
  }

  // Helper to render status badges
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

  // Determine payment status based on order status
  function getPaymentStatusForOrderStatus(orderStatus) {
    switch ((orderStatus || "").toLowerCase()) {
      case "delivered":
        return "paid";
      case "cancelled":
        return "refunded";
      case "processing":
      case "out_for_delivery":
        return "paid";
      case "pending":
      default:
        return "unpaid";
    }
  }

  function renderOrders(orders) {
    ordersTableBody.innerHTML = "";
    if (!orders || orders.length === 0) {
      ordersTableBody.innerHTML =
        '<tr><td colspan="7" class="py-6 text-center text-gray-500">No orders found</td></tr>';
      return;
    }

    orders.forEach((order) => {
      const tr = document.createElement("tr");
      tr.className = "text-sm text-gray-700 border-b";

      const date = order.order_date
        ? new Date(order.order_date).toLocaleString()
        : "";

      tr.innerHTML = `
                <td class="py-2 px-3"><a href="order-details.php?id=${
                  order.id
                }" class="text-sm text-blue-600">${escapeHtml(
        order.order_number || "#" + order.id
      )}</a></td>
                <td class="py-2 px-3">${escapeHtml(
                  order.customer_name || order.customer_id || ""
                )}</td>
                <td class="py-2 px-3">₦${Number(
                  order.total_amount || order.subtotal || 0
                ).toLocaleString()}</td>
                <td class="py-2 px-3" data-status-cell></td>
                <td class="py-2 px-3" data-payment-cell>${escapeHtml(
                  order.payment_status || ""
                )}</td>
                <td class="py-2 px-3">${escapeHtml(date)}</td>
                <td class="py-2 px-3" data-actions></td>
            `;

      // render status badge
      const statusCell = tr.querySelector("[data-status-cell]");
      statusCell.innerHTML = renderStatusBadge(order.status || "pending");

      // create status select + update button
      const actionsCell = tr.querySelector("[data-actions]");
      const select = document.createElement("select");
      select.className = "border rounded px-2 py-1 mr-2";
      statusOptions.forEach((opt) => {
        const o = document.createElement("option");
        o.value = opt.v;
        o.textContent = opt.t;
        if (opt.v === order.status) o.selected = true;
        select.appendChild(o);
      });

      const btn = document.createElement("button");
      btn.className = "bg-blue-600 text-white rounded px-3 py-1 text-sm";
      btn.textContent = "Update";
      btn.addEventListener("click", function () {
        const newStatus = select.value;
        const newPaymentStatus = getPaymentStatusForOrderStatus(newStatus);
        btn.disabled = true;
        btn.textContent = "Updating...";
        fetch(API_BASE + "/orders/update_status.php", {
          method: "POST",
          credentials: "same-origin",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            order_id: order.id,
            status: newStatus,
            payment_status: newPaymentStatus,
          }),
        })
          .then(safeJson)
          .then((data) => {
            btn.disabled = false;
            btn.textContent = "Update";
            if (!data) {
              createAlert("Invalid server response", "error");
              return;
            }
            if (data.__invalid_json) {
              console.error("Invalid JSON from update_status.php", data.__raw);
              createAlert("Invalid server response (see console)", "error");
              return;
            }
            if (data.success) {
              tr.querySelector("[data-status-cell]").innerHTML =
                renderStatusBadge(newStatus);
              tr.querySelector("[data-payment-cell]").textContent =
                newPaymentStatus;
              createAlert("Order status and payment status updated");
            } else {
              createAlert(data.message || "Failed to update", "error");
            }
          })
          .catch((err) => {
            btn.disabled = false;
            btn.textContent = "Update";
            createAlert("Network or server error", "error");
            console.error(err);
          });
      });

      actionsCell.appendChild(select);
      actionsCell.appendChild(btn);

      ordersTableBody.appendChild(tr);
    });
  }

  function escapeHtml(str) {
    if (!str) return "";
    return String(str).replace(/[&<>"]+/g, function (s) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[s];
    });
  }

  // initial load with debugging when server returns non-JSON
  fetch(API_BASE + "/orders/get_all.php", { credentials: "same-origin" })
    .then((resp) =>
      resp.text().then((t) => ({ ok: resp.ok, status: resp.status, text: t }))
    )
    .then(({ ok, status, text }) => {
      let parsed = null;
      try {
        parsed = JSON.parse(text);
      } catch (e) {
        parsed = null;
      }
      if (!parsed) {
        console.error("Non-JSON response from get_all.php", { status, text });
        const safe = escapeHtml(text.replace(/\n/g, " ")).slice(0, 2000);
        ordersTableBody.innerHTML = `<tr><td colspan="7" class="py-6 text-left text-red-500"><strong>Invalid server response</strong><div class="mt-2 text-xs text-gray-700">${safe}</div></td></tr>`;
        return;
      }
      if (!parsed.success) {
        ordersTableBody.innerHTML = `<tr><td colspan="7" class="py-6 text-center text-red-500">${escapeHtml(
          parsed.message || "Error"
        )}</td></tr>`;
        return;
      }
      renderOrders(parsed.data || []);
    })
    .catch((err) => {
      console.error(err);
      ordersTableBody.innerHTML =
        '<tr><td colspan="7" class="py-6 text-center text-red-500">Network error</td></tr>';
    });
});
