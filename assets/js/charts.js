(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var charts = document.querySelectorAll('.sofir-chart-canvas');

        charts.forEach(function(canvas) {
            var type = canvas.dataset.type || 'line';
            var values = canvas.dataset.values ? JSON.parse(canvas.dataset.values) : [];

            if (typeof Chart === 'undefined') {
                console.warn('Chart.js not loaded');
                return;
            }

            var ctx = canvas.getContext('2d');
            new Chart(ctx, {
                type: type,
                data: {
                    labels: values.map(function(v) { return v.label || ''; }),
                    datasets: [{
                        label: 'Data',
                        data: values.map(function(v) { return v.value || 0; }),
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });
        });
    });
})();
