document.addEventListener('DOMContentLoaded', function () {
    const salesActivityData = {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Orders',
            data: [5, 8, 3, 6, 7, 9, 4],
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.2)',
            fill: true,
            tension: 0.4
        }]
    };

    const salesActivityCtx = document.getElementById('salesActivityChart')?.getContext('2d');
    if (salesActivityCtx) {
        new Chart(salesActivityCtx, {
            type: 'line',
            data: salesActivityData,
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
});
