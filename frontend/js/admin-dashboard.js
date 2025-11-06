document.addEventListener('DOMContentLoaded', function () {
    // Dummy data for the charts
    const salesData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        datasets: [{
            label: 'Sales',
            data: [120, 150, 180, 220, 200, 250, 280],
            borderColor: '#3B82F6',
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            fill: true,
            tension: 0.4
        }]
    };

    const topProductsData = {
        labels: ['Product A', 'Product B', 'Product C'],
        datasets: [{
            data: [300, 50, 100],
            backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
            hoverOffset: 4
        }]
    };

    // Sales Overview Chart (Line)
    const salesOverviewCtx = document.getElementById('salesOverviewChart')?.getContext('2d');
    if (salesOverviewCtx) {
        new Chart(salesOverviewCtx, {
            type: 'line',
            data: salesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Top Products Chart (Doughnut)
    const topProductsCtx = document.getElementById('topProductsChart')?.getContext('2d');
    if (topProductsCtx) {
        new Chart(topProductsCtx, {
            type: 'doughnut',
            data: topProductsData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    }
});
