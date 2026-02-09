<div class="globe-widget-modern">
    <div class="globe-container">
        <div id='chart_div-{{ $id }}' class="globe-chart"></div>
    </div>
</div>

<script type='text/javascript'>
    loadjs('https://www.gstatic.com/charts/loader.js', function() {
        google.charts.load('current', {'packages': ['geochart'], callback: function() {
                var data = new google.visualization.DataTable();
                data.addColumn('number', 'Latitude');
                data.addColumn('number', 'Longitude');
                data.addColumn('string', 'Label');
                data.addColumn('number', 'Status');
                data.addColumn('number', 'Size');
                data.addColumn({type: 'string', role: 'tooltip', 'p': {'html': true}});
                data.addRows({!! $locations !!});
                var options = {
                    region: '{{ $region }}',
                    resolution: '{{ $resolution }}',
                    displayMode: 'markers',
                    keepAspectRatio: 1,
                    magnifyingGlass: {enable: true, zoomFactor: 100},
                    colorAxis: {minValue: 0,  maxValue: 100, colors: ['#28a745', '#ffc107', '#dc3545']},
                    markerOpacity: 0.90,
                    tooltip: {isHtml: true},
                    backgroundColor: '#ffffff',
                    datalessRegionColor: '#f8f9fa',
                    defaultColor: '#e9ecef'
                };
                var chart = new google.visualization.GeoChart(document.getElementById('chart_div-{{ $id }}'));
                chart.draw(data, options);
                
                // Handle resize
                window.addEventListener('resize', function() {
                    chart.draw(data, options);
                });
            }
        });
    });
</script>

<style>
.globe-widget-modern {
    padding: 8px;
}

.globe-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    overflow: hidden;
    position: relative;
}

.globe-chart {
    width: 100%;
    height: 100%;
    min-height: 300px;
}
</style>
