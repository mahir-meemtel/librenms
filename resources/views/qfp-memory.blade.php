<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QFP Memory Chart</title>
    <script src="{{ asset('html/js/chart.umd.min.js') }}"></script>
</head>
<body>
    <div style="width: 100%; max-width: 700px; margin: 0 auto;">
        <canvas id="memoryChart"></canvas>
    </div>

    <script>
    fetch('{{ route('qfp-memory.data') }}')
        .then(res => res.json())
        .then(data => {
            const ctx = document.getElementById('memoryChart').getContext('2d');

            new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label;
                                    const value = context.raw;
                                    const total = data.stats.total;
                                    const perc = (value / total * 100).toFixed(2);
                                    return `${label}: ${value} MB (${perc}%)`;
                                }
                            }
                        }
                    }
                }
            });
        })
        .catch(err => console.error(err));
    </script>
</body>
</html>
