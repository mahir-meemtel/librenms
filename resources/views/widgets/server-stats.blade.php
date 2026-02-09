@php
    $template = $template ?? 1;
@endphp

@if($template == 1)
    {{-- Template 1: Modern Donut Gauges --}}
    <div class="server-stats-modern server-stats-template-1">
        <div class="col-sm-{{ $columns }}">
            <div class="gauge-title">{{ __('CPU Usage') }}</div>
            <div
                id="cpu-{{ $id }}"
                class="gauge-{{ $id }} gauge-container"
                data-value="{{ $cpu }}"
                data-max="100"
                data-symbol="%"
            ></div>
        </div>

        @foreach($mempools as $key => $mem)
            <div class="col-sm-{{ $columns }}">
                <div class="gauge-title">{{ $mem->mempool_descr}} {{ __('Usage') }}</div>
                <div
                    id="mem-{{ $key }}-{{ $id }}"
                    class="gauge-{{ $id }} gauge-container"
                    data-value="{{ $mem->used}}"
                    data-max="{{ $mem->total}}"
                    data-label="Mbytes"
                ></div>
            </div>
        @endforeach

        @foreach($disks as $key => $disk)
            <div class="col-sm-{{ $columns }}">
                <div class="gauge-title">{{ $disk->storage_descr}} {{ __('Usage') }}</div>
                <div
                    id="disk-{{ $key }}-{{ $id }}"
                    class="gauge-{{ $id }} gauge-container"
                    data-value="{{ $disk->used}}"
                    data-max="{{ $disk->total}}"
                    data-label="Mbytes"
                ></div>
            </div>
        @endforeach
    </div>

@elseif($template == 2)
    {{-- Template 2: Card Based --}}
    <div class="server-stats-modern server-stats-template-2">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <i class="fa-solid fa-microchip" aria-hidden="true"></i>
                    <span class="stat-card-title">{{ __('CPU Usage') }}</span>
                </div>
                <div class="stat-card-body">
                    <div class="stat-value" data-value="{{ $cpu }}" data-max="100" data-symbol="%">
                        <span class="stat-number">{{ round($cpu) }}</span>
                        <span class="stat-unit">%</span>
                    </div>
                    <div class="stat-progress">
                        <div class="stat-progress-bar" style="width: {{ $cpu }}%; background: {{ $cpu < 50 ? '#2ecc71' : ($cpu < 80 ? '#f39c12' : '#e74c3c') }};"></div>
                    </div>
                </div>
            </div>

            @foreach($mempools as $key => $mem)
                @php
                    $memPercentage = $mem->total > 0 ? ($mem->used / $mem->total) * 100 : 0;
                    $memColor = $memPercentage < 50 ? '#2ecc71' : ($memPercentage < 80 ? '#f39c12' : '#e74c3c');
                @endphp
                <div class="stat-card">
                    <div class="stat-card-header">
                        <i class="fa-solid fa-memory" aria-hidden="true"></i>
                        <span class="stat-card-title">{{ $mem->mempool_descr}} {{ __('Usage') }}</span>
                    </div>
                    <div class="stat-card-body">
                        <div class="stat-value">
                            <span class="stat-number">{{ round($mem->used) }}</span>
                            <span class="stat-unit">MB</span>
                        </div>
                        <div class="stat-info">
                            <span class="stat-total">/ {{ round($mem->total) }} MB</span>
                            <span class="stat-percentage">{{ round($memPercentage) }}%</span>
                        </div>
                        <div class="stat-progress">
                            <div class="stat-progress-bar" style="width: {{ $memPercentage }}%; background: {{ $memColor }};"></div>
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach($disks as $key => $disk)
                @php
                    $diskPercentage = $disk->total > 0 ? ($disk->used / $disk->total) * 100 : 0;
                    $diskColor = $diskPercentage < 50 ? '#2ecc71' : ($diskPercentage < 80 ? '#f39c12' : '#e74c3c');
                @endphp
                <div class="stat-card">
                    <div class="stat-card-header">
                        <i class="fa-solid fa-hard-drive" aria-hidden="true"></i>
                        <span class="stat-card-title">{{ $disk->storage_descr}} {{ __('Usage') }}</span>
                    </div>
                    <div class="stat-card-body">
                        <div class="stat-value">
                            <span class="stat-number">{{ round($disk->used) }}</span>
                            <span class="stat-unit">MB</span>
                        </div>
                        <div class="stat-info">
                            <span class="stat-total">/ {{ round($disk->total) }} MB</span>
                            <span class="stat-percentage">{{ round($diskPercentage) }}%</span>
                        </div>
                        <div class="stat-progress">
                            <div class="stat-progress-bar" style="width: {{ $diskPercentage }}%; background: {{ $diskColor }};"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@elseif($template == 3)
    {{-- Template 3: Progress Bars --}}
    <div class="server-stats-modern server-stats-template-3">
        <div class="progress-item">
            <div class="progress-header">
                <span class="progress-label">
                    <i class="fa-solid fa-microchip" aria-hidden="true"></i>
                    {{ __('CPU Usage') }}
                </span>
                <span class="progress-value">{{ round($cpu) }}%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: {{ $cpu }}%; background: {{ $cpu < 50 ? '#2ecc71' : ($cpu < 80 ? '#f39c12' : '#e74c3c') }};"></div>
            </div>
        </div>

        @foreach($mempools as $key => $mem)
            @php
                $memPercentage = $mem->total > 0 ? ($mem->used / $mem->total) * 100 : 0;
                $memColor = $memPercentage < 50 ? '#2ecc71' : ($memPercentage < 80 ? '#f39c12' : '#e74c3c');
            @endphp
            <div class="progress-item">
                <div class="progress-header">
                    <span class="progress-label">
                        <i class="fa-solid fa-memory" aria-hidden="true"></i>
                        {{ $mem->mempool_descr}} {{ __('Usage') }}
                    </span>
                    <span class="progress-value">{{ round($mem->used) }} / {{ round($mem->total) }} MB ({{ round($memPercentage) }}%)</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ $memPercentage }}%; background: {{ $memColor }};"></div>
                </div>
            </div>
        @endforeach

        @foreach($disks as $key => $disk)
            @php
                $diskPercentage = $disk->total > 0 ? ($disk->used / $disk->total) * 100 : 0;
                $diskColor = $diskPercentage < 50 ? '#2ecc71' : ($diskPercentage < 80 ? '#f39c12' : '#e74c3c');
            @endphp
            <div class="progress-item">
                <div class="progress-header">
                    <span class="progress-label">
                        <i class="fa-solid fa-hard-drive" aria-hidden="true"></i>
                        {{ $disk->storage_descr}} {{ __('Usage') }}
                    </span>
                    <span class="progress-value">{{ round($disk->used) }} / {{ round($disk->total) }} MB ({{ round($diskPercentage) }}%)</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ $diskPercentage }}%; background: {{ $diskColor }};"></div>
                </div>
            </div>
        @endforeach
    </div>

@elseif($template == 4)
    {{-- Template 4: Compact Grid --}}
    <div class="server-stats-modern server-stats-template-4">
        <div class="compact-grid">
            <div class="compact-stat">
                <div class="compact-icon">
                    <i class="fa-solid fa-microchip" aria-hidden="true"></i>
                </div>
                <div class="compact-info">
                    <div class="compact-label">{{ __('CPU') }}</div>
                    <div class="compact-value">{{ round($cpu) }}<span class="compact-unit">%</span></div>
                </div>
                <div class="compact-mini-gauge" data-value="{{ $cpu }}" data-max="100"></div>
            </div>

            @foreach($mempools as $key => $mem)
                @php
                    $memPercentage = $mem->total > 0 ? ($mem->used / $mem->total) * 100 : 0;
                @endphp
                <div class="compact-stat">
                    <div class="compact-icon">
                        <i class="fa-solid fa-memory" aria-hidden="true"></i>
                    </div>
                    <div class="compact-info">
                        <div class="compact-label">{{ $mem->mempool_descr}}</div>
                        <div class="compact-value">{{ round($memPercentage) }}<span class="compact-unit">%</span></div>
                    </div>
                    <div class="compact-mini-gauge" data-value="{{ $memPercentage }}" data-max="100"></div>
                </div>
            @endforeach

            @foreach($disks as $key => $disk)
                @php
                    $diskPercentage = $disk->total > 0 ? ($disk->used / $disk->total) * 100 : 0;
                @endphp
                <div class="compact-stat">
                    <div class="compact-icon">
                        <i class="fa-solid fa-hard-drive" aria-hidden="true"></i>
                    </div>
                    <div class="compact-info">
                        <div class="compact-label">{{ $disk->storage_descr}}</div>
                        <div class="compact-value">{{ round($diskPercentage) }}<span class="compact-unit">%</span></div>
                    </div>
                    <div class="compact-mini-gauge" data-value="{{ $diskPercentage }}" data-max="100"></div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<script type='text/javascript'>
    @if($template == 1)
    // Template 1: JustGage initialization
    $('.gauge-{{ $id }}').each(function() {
        var $el = $(this);
        var value = parseFloat($el.data('value')) || 0;
        var max = parseFloat($el.data('max')) || 100;
        var symbol = $el.data('symbol') || '';
        var label = $el.data('label') || '';
        var percentage = (value / max) * 100;
        
        // Determine color based on percentage
        var gaugeColor;
        if (percentage < 50) {
            gaugeColor = '#2ecc71'; // Green
        } else if (percentage < 80) {
            gaugeColor = '#f39c12'; // Orange
        } else {
            gaugeColor = '#e74c3c'; // Red
        }
        
        new JustGage({
            id: this.id,
            value: value,
            min: 0,
            max: max,
            symbol: symbol,
            label: label,
            gaugeWidthScale: 0.4,
            gaugeColor: '#e8e8e8',
            levelColors: [gaugeColor],
            showMinMax: false,
            donut: true,
            donutStartAngle: 0,
            valueFontColor: '#2c3e50',
            valueFontFamily: 'Segoe UI, Roboto, -apple-system, BlinkMacSystemFont, sans-serif',
            valueFontSize: '20px',
            valueFontWeight: '600',
            labelFontColor: '#7f8c8d',
            labelFontFamily: 'Segoe UI, Roboto, -apple-system, BlinkMacSystemFont, sans-serif',
            labelFontSize: '11px',
            shadowOpacity: 0.1,
            shadowSize: 2,
            shadowVerticalOffset: 1,
            textRenderer: function(val) {
                var formatted = Math.round(val);
                if (symbol) {
                    return formatted + symbol;
                }
                if (label) {
                    if (formatted >= 1000) {
                        formatted = (formatted / 1000).toFixed(1) + 'K';
                    }
                    return formatted + ' ' + label;
                }
                return formatted;
            },
            customSectors: [{
                color: gaugeColor,
                lo: 0,
                hi: max
            }],
            counter: true,
            decimals: 0,
            humanFriendly: false
        });
    });

    // Responsive resizing for Template 1
    if (window.ResizeObserver) {
        $('.gauge-{{ $id }}').each(function() {
            var gaugeId = this.id;
            var resizeObserver = new ResizeObserver(function(entries) {
                var gauge = window['gauge_' + gaugeId];
                if (gauge) {
                    gauge.refresh();
                }
            });
            resizeObserver.observe(this);
        });
    }
    @elseif($template == 4)
    // Template 4: Mini gauges
    $('.compact-mini-gauge').each(function() {
        var $el = $(this);
        var value = parseFloat($el.data('value')) || 0;
        var max = parseFloat($el.data('max')) || 100;
        var percentage = (value / max) * 100;
        
        var gaugeColor;
        if (percentage < 50) {
            gaugeColor = '#2ecc71';
        } else if (percentage < 80) {
            gaugeColor = '#f39c12';
        } else {
            gaugeColor = '#e74c3c';
        }
        
        new JustGage({
            id: this.id + '-{{ $id }}',
            value: value,
            min: 0,
            max: max,
            gaugeWidthScale: 0.3,
            gaugeColor: '#e8e8e8',
            levelColors: [gaugeColor],
            showMinMax: false,
            donut: true,
            donutStartAngle: 0,
            valueFontColor: '#2c3e50',
            valueFontFamily: 'Segoe UI, Roboto, -apple-system, BlinkMacSystemFont, sans-serif',
            valueFontSize: '14px',
            valueFontWeight: '600',
            labelFontColor: 'transparent',
            shadowOpacity: 0.1,
            shadowSize: 1,
            shadowVerticalOffset: 1,
            textRenderer: function(val) {
                return Math.round(val) + '%';
            },
            customSectors: [{
                color: gaugeColor,
                lo: 0,
                hi: max
            }],
            counter: true,
            decimals: 0,
            humanFriendly: false
        });
    });
    @endif
</script>

<style>
/* Common Styles */
.server-stats-modern {
    padding: 8px;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

/* Template 1: Modern Donut Gauges */
.server-stats-template-1 .gauge-title {
    text-align: center;
    font-size: 0.8em;
    font-weight: 600;
    color: #34495e;
    margin-bottom: 10px;
    margin-top: 5px;
    letter-spacing: 0.2px;
    text-transform: uppercase;
}

.server-stats-template-1 .gauge-container {
    height: 90px;
    margin-bottom: 15px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.server-stats-template-1 .gauge-container svg {
    width: 100% !important;
    height: 100% !important;
}

.server-stats-template-1 .gauge-container .gauge-value {
    font-weight: 600 !important;
    text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
}

.server-stats-template-1 .gauge-container .gauge-label {
    margin-top: 5px !important;
    font-weight: 500 !important;
}

/* Template 2: Card Based */
.server-stats-template-2 .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
}

.server-stats-template-2 .stat-card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    padding: 16px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.server-stats-template-2 .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
}

.server-stats-template-2 .stat-card-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
}

.server-stats-template-2 .stat-card-header i {
    color: #2c539e;
    font-size: 16px;
}

.server-stats-template-2 .stat-card-title {
    font-size: 12px;
    font-weight: 600;
    color: #2c3e50;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.server-stats-template-2 .stat-card-body {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.server-stats-template-2 .stat-value {
    display: flex;
    align-items: baseline;
    gap: 4px;
}

.server-stats-template-2 .stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
}

.server-stats-template-2 .stat-unit {
    font-size: 14px;
    font-weight: 500;
    color: #6c757d;
}

.server-stats-template-2 .stat-info {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #6c757d;
}

.server-stats-template-2 .stat-percentage {
    font-weight: 600;
    color: #2c3e50;
}

.server-stats-template-2 .stat-progress {
    height: 10px;
    background: linear-gradient(135deg, #f0f0f0 0%, #e8e8e8 100%);
    border-radius: 10px;
    overflow: hidden;
    margin-top: 4px;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
}

.server-stats-template-2 .stat-progress-bar {
    height: 100%;
    border-radius: 10px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

.server-stats-template-2 .stat-progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        90deg,
        transparent 0%,
        rgba(255, 255, 255, 0.3) 50%,
        transparent 100%
    );
    animation: shimmer 2s infinite;
    pointer-events: none;
}

/* Template 3: Progress Bars */
.server-stats-template-3 {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.server-stats-template-3 .progress-item {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    padding: 16px;
}

.server-stats-template-3 .progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.server-stats-template-3 .progress-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #2c3e50;
}

.server-stats-template-3 .progress-label i {
    color: #2c539e;
    font-size: 14px;
}

.server-stats-template-3 .progress-value {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
}

.server-stats-template-3 .progress-bar-container {
    height: 14px;
    background: linear-gradient(135deg, #f0f0f0 0%, #e8e8e8 100%);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
}

.server-stats-template-3 .progress-bar-fill {
    height: 100%;
    border-radius: 12px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
}

.server-stats-template-3 .progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        90deg,
        transparent 0%,
        rgba(255, 255, 255, 0.35) 50%,
        transparent 100%
    );
    animation: shimmer 2s infinite;
    pointer-events: none;
}

/* Template 4: Compact Grid */
.server-stats-template-4 .compact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
}

.server-stats-template-4 .compact-stat {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    padding: 12px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.server-stats-template-4 .compact-stat:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
}

.server-stats-template-4 .compact-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #2c539e 0%, #1e3a6f 100%);
    border-radius: 8px;
    color: #ffffff;
    font-size: 16px;
    flex-shrink: 0;
}

.server-stats-template-4 .compact-info {
    flex: 1;
    min-width: 0;
}

.server-stats-template-4 .compact-label {
    font-size: 11px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.server-stats-template-4 .compact-value {
    font-size: 20px;
    font-weight: 700;
    color: #2c3e50;
    line-height: 1;
}

.server-stats-template-4 .compact-unit {
    font-size: 12px;
    font-weight: 500;
    color: #6c757d;
    margin-left: 2px;
}

.server-stats-template-4 .compact-mini-gauge {
    width: 50px;
    height: 50px;
    flex-shrink: 0;
}

.server-stats-template-4 .compact-mini-gauge svg {
    width: 100% !important;
    height: 100% !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .server-stats-template-1 .gauge-container {
        height: 80px;
    }
    
    .server-stats-template-1 .gauge-title {
        font-size: 0.75em;
        margin-bottom: 8px;
    }

    .server-stats-template-2 .stats-grid {
        grid-template-columns: 1fr;
    }

    .server-stats-template-4 .compact-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    }
}

@media (max-width: 480px) {
    .server-stats-template-1 .gauge-container {
        height: 70px;
    }
    
    .server-stats-template-1 .gauge-title {
        font-size: 0.7em;
    }

    .server-stats-template-4 .compact-stat {
        flex-direction: column;
        text-align: center;
    }

    .server-stats-template-4 .compact-mini-gauge {
        width: 60px;
        height: 60px;
    }
}

/* Shimmer animation for progress bars */
@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}
</style>
