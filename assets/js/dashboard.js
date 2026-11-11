(() => {
    'use strict';

    const clock = document.getElementById('missionClock');
    const date = document.getElementById('missionDate');

    function updateLocalClock() {
        const now = new Date();
        if (clock) {
            clock.textContent = new Intl.DateTimeFormat(undefined, {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
            }).format(now);
        }
        if (date) {
            date.textContent = new Intl.DateTimeFormat(undefined, {
                weekday: 'short', day: '2-digit', month: 'short', year: 'numeric'
            }).format(now);
        }
    }
    updateLocalClock();
    window.setInterval(updateLocalClock, 1000);

    if (typeof Chart === 'undefined') {
        document.querySelectorAll('.chart-canvas-wrap').forEach((container) => {
            container.innerHTML = '<p class="text-secondary small">Chart library could not be loaded. Check your internet connection and reload this page.</p>';
        });
        return;
    }

    Chart.defaults.color = '#A3AFBE';
    Chart.defaults.font.family = "Inter, sans-serif";
    Chart.defaults.font.size = 10;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.boxWidth = 7;
    Chart.defaults.plugins.legend.labels.padding = 16;

    const atmosphereCanvas = document.getElementById('atmosphereChart');
    if (atmosphereCanvas) {
        new Chart(atmosphereCanvas, {
            type: 'doughnut',
            data: {
                labels: ['Carbon dioxide (CO₂)', 'Nitrogen (N₂)', 'Argon (Ar)', 'Oxygen (O₂)', 'Carbon monoxide (CO)'],
                datasets: [{
                    data: [95.1, 2.59, 1.94, 0.16, 0.06],
                    backgroundColor: ['#E66A45', '#50C8E8', '#39C58A', '#E9B44C', '#788697'],
                    borderColor: '#141E2B',
                    borderWidth: 3,
                    hoverOffset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '67%',
                plugins: {
                    legend: { position: 'right' },
                    tooltip: { callbacks: { label: (context) => `${context.label}: ${context.raw}%` } }
                }
            }
        });
    }

    const orbitalCanvas = document.getElementById('orbitalChart');
    if (orbitalCanvas) {
        new Chart(orbitalCanvas, {
            type: 'bar',
            data: {
                labels: ['Length of day (hours)', 'Length of year (Earth days)'],
                datasets: [
                    { label: 'Mars', data: [24.6, 687], backgroundColor: '#E66A45', borderRadius: 3, maxBarThickness: 25 },
                    { label: 'Earth', data: [23.9, 365.25], backgroundColor: '#50C8E8', borderRadius: 3, maxBarThickness: 25 }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.07)' }, ticks: { color: '#788697' } },
                    y: { grid: { display: false }, ticks: { color: '#A3AFBE' } }
                },
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { callbacks: { label: (context) => `${context.dataset.label}: ${context.raw}${context.dataIndex === 0 ? ' hours' : ' Earth days'}` } }
                }
            }
        });
    }
})();
