document.addEventListener('DOMContentLoaded', function() {
    // Fetch and display dashboard data
    async function fetchDashboardData() {
        try {
            // Fetch sales summary
            const salesRes = await fetch('../backend/api/sales/summary.php');
            const salesData = await salesRes.json();

            if (salesData.success) {
                document.getElementById('total-sales').textContent = `$${salesData.data.total_sales}`;
                document.getElementById('total-orders').textContent = salesData.data.total_orders;
            }

            // Fetch users
            const usersRes = await fetch('../backend/api/users/get_all.php');
            const usersData = await usersRes.json();

            if (usersData.success) {
                const customers = usersData.data.filter(user => user.role === 'customer');
                const salesManagers = usersData.data.filter(user => user.role === 'sales_manager');

                document.getElementById('total-customers').textContent = customers.length;
                document.getElementById('total-sales-managers').textContent = salesManagers.length;
            }

            // Initialize sales chart
            const salesChartCtx = document.getElementById('sales-chart').getContext('2d');
            new Chart(salesChartCtx, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'Sales',
                        data: [65, 59, 80, 81, 56, 55, 40],
                        fill: false,
                        borderColor: '#3b82f6',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        } catch (error) {
            console.error('Error fetching dashboard data:', error);
        }
    }

    fetchDashboardData();
});