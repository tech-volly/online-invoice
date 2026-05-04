<script>
// Quarterly Comparison Chart Script
$(document).ready(function () {
    // Get quarterly comparison data
    var quarterlyComparisonData = {
        data: @json($quarterlyComparisonData['data']),
        fyLabel: "@json($quarterlyComparisonData['fy_label'])",
        fyLabel1: "@json($quarterlyComparisonData['fy_label_1'])",
        fyLabel2: "@json($quarterlyComparisonData['fy_label_2'])"
    };
    
    var quarters = quarterlyComparisonData.data.map(item => item.quarter);
    var currentYearData = quarterlyComparisonData.data.map(item => item.current);
    var previousYear1Data = quarterlyComparisonData.data.map(item => item.previous_1);
    var previousYear2Data = quarterlyComparisonData.data.map(item => item.previous_2);
    
    var ctx = document.getElementById('quarterlyComparisonChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: quarters,
                datasets: [
                    {
                        label: quarterlyComparisonData.fyLabel,
                        data: currentYearData,
                        backgroundColor: '#667eea',
                        borderColor: '#667eea',
                        borderWidth: 1
                    },
                    {
                        label: quarterlyComparisonData.fyLabel1,
                        data: previousYear1Data,
                        backgroundColor: '#764ba2',
                        borderColor: '#764ba2',
                        borderWidth: 1
                    },
                    {
                        label: quarterlyComparisonData.fyLabel2,
                        data: previousYear2Data,
                        backgroundColor: '#f59e0b',
                        borderColor: '#f59e0b',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += '$' + context.parsed.y.toLocaleString();
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
