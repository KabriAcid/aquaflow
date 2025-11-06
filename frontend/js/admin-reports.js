
document.addEventListener("DOMContentLoaded", () => {
    const generateReportBtn = document.getElementById("generateReportBtn");
    const reportContent = document.getElementById("reportContent");
    const spinner = generateReportBtn.querySelector(".spinner-border");

    generateReportBtn.addEventListener("click", async () => {
        // Show spinner and disable button
        spinner.classList.remove("d-none");
        generateReportBtn.disabled = true;

        try {
            const response = await fetch("http://127.0.0.1:5001/api/reports/sales");
            const result = await response.json();

            if (result.success) {
                displayReport(result.data);
            } else {
                reportContent.innerHTML = `<div class="alert alert-danger">Error: ${result.message}</div>`;
            }
        } catch (error) {
            reportContent.innerHTML = `<div class="alert alert-danger">Error: Could not connect to the reporting service.</div>`;
        }

        // Hide spinner and enable button
        spinner.classList.add("d-none");
        generateReportBtn.disabled = false;
    });

    function displayReport(data) {
        const topProductsList = Object.entries(data.top_selling_products)
            .map(([product, quantity]) => `<li>${product}: ${quantity} units</li>`)
            .join("");

        reportContent.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Sales Report Summary</h5>
                    <p class="card-text"><strong>Total Revenue:</strong> $${data.total_revenue.toFixed(2)}</p>
                    <h6 class="card-subtitle mb-2 text-muted">Top Selling Products:</h6>
                    <ul>${topProductsList}</ul>
                </div>
            </div>
        `;
    }
});

