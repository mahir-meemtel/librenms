<div class="device-summary-horiz-modern">
    <div class="summary-grid">
        <!-- Devices Card -->
        <div class="summary-card">
            <div class="summary-card-header">
                <a href="{{ url('devices') }}" class="summary-card-title">
                    <i class="fa-solid fa-server" aria-hidden="true"></i>
                    {{ __('Devices') }}
                </a>
                <a href="{{ url('devices') }}" class="summary-total">{{ $devices['total'] }}</a>
            </div>
            <div class="summary-stats">
                <a href="{{ url('devices/state=up/format=list_detail') }}" class="stat-item stat-up">
                    <span class="stat-label">{{ __('Up') }}</span>
                    <span class="stat-value">{{ $devices['up'] }}</span>
                </a>
                <a href="{{ url('devices/state=down/format=list_detail') }}" class="stat-item stat-down">
                    <span class="stat-label">{{ __('Down') }}</span>
                    <span class="stat-value">{{ $devices['down'] }}</span>
                </a>
                <a href="{{ url('devices/ignore=1/format=list_detail') }}" class="stat-item stat-ignored">
                    <span class="stat-label">{{ __('Ignored') }}</span>
                    <span class="stat-value">{{ $devices['ignored'] }}</span>
                </a>
                <a href="{{ url('devices/disable_notify=1/format=list_detail') }}" class="stat-item stat-disabled">
                    <span class="stat-label">{{ __('Alert Disabled') }}</span>
                    <span class="stat-value">{{ $devices['disable_notify'] }}</span>
                </a>
                <a href="{{ url('devices/disabled=1/format=list_detail') }}" class="stat-item stat-shutdown">
                    <span class="stat-label">{{ __('Disabled') }}</span>
                    <span class="stat-value">{{ $devices['disabled'] }}</span>
                </a>
            </div>
        </div>

        <!-- Ports Card -->
        <div class="summary-card">
            <div class="summary-card-header">
                <a href="{{ url('ports') }}" class="summary-card-title">
                    <i class="fa-solid fa-plug" aria-hidden="true"></i>
                    {{ __('Ports') }}
                </a>
                <a href="{{ url('ports') }}" class="summary-total">{{ $ports['total'] }}</a>
            </div>
            <div class="summary-stats">
                <a href="{{ url('ports/format=list_detail/state=up') }}" class="stat-item stat-up">
                    <span class="stat-label">{{ __('Up') }}</span>
                    <span class="stat-value">{{ $ports['up'] }}</span>
                </a>
                <a href="{{ url('ports/format=list_detail/state=down') }}" class="stat-item stat-down">
                    <span class="stat-label">{{ __('Down') }}</span>
                    <span class="stat-value">{{ $ports['down'] }}</span>
                </a>
                <a href="{{ url('ports/format=list_detail/ignore=1') }}" class="stat-item stat-ignored">
                    <span class="stat-label">{{ __('Ignored') }}</span>
                    <span class="stat-value">{{ $ports['ignored'] }}</span>
                </a>
                <a href="{{ url('ports/format=list_detail/state=admindown') }}" class="stat-item stat-shutdown">
                    <span class="stat-label">{{ __('Shutdown') }}</span>
                    <span class="stat-value">{{ $ports['shutdown'] }}</span>
                </a>
                @if($summary_errors)
                    <a href="{{ url('ports/format=list_detail/errors=1') }}" class="stat-item stat-error">
                        <span class="stat-label">{{ __('Errored') }}</span>
                        <span class="stat-value">{{ $ports['errored'] }}</span>
                    </a>
                @endif
            </div>
        </div>

        @if($show_services)
        <!-- Services Card -->
        <div class="summary-card">
            <div class="summary-card-header">
                <a href="{{ url('services') }}" class="summary-card-title">
                    <i class="fa-solid fa-gear" aria-hidden="true"></i>
                    {{ __('Services') }}
                </a>
                <a href="{{ url('services') }}" class="summary-total">{{ $services['total'] }}</a>
            </div>
            <div class="summary-stats">
                <a href="{{ url('services/state=ok/view=details') }}" class="stat-item stat-up">
                    <span class="stat-label">{{ __('OK') }}</span>
                    <span class="stat-value">{{ $services['ok'] }}</span>
                </a>
                <a href="{{ url('services/state=critical/view=details') }}" class="stat-item stat-down">
                    <span class="stat-label">{{ __('Critical') }}</span>
                    <span class="stat-value">{{ $services['critical'] }}</span>
                </a>
                <a href="{{ url('services/ignore=1/view=details') }}" class="stat-item stat-ignored">
                    <span class="stat-label">{{ __('Ignored') }}</span>
                    <span class="stat-value">{{ $services['ignored'] }}</span>
                </a>
                <a href="{{ url('services/disabled=1/view=details') }}" class="stat-item stat-shutdown">
                    <span class="stat-label">{{ __('Disabled') }}</span>
                    <span class="stat-value">{{ $services['disabled'] }}</span>
                </a>
            </div>
        </div>
        @endif

        @if($show_sensors)
        <!-- Health/Sensors Card -->
        <div class="summary-card">
            <div class="summary-card-header">
                <a href="{{ url('health') }}" class="summary-card-title">
                    <i class="fa-solid fa-heart-pulse" aria-hidden="true"></i>
                    {{ __('Health') }}
                </a>
                <a href="{{ url('health') }}" class="summary-total">{{ $sensors['total'] }}</a>
            </div>
            <div class="summary-stats">
                <a href="{{ url('health') }}" class="stat-item stat-up">
                    <span class="stat-label">{{ __('OK') }}</span>
                    <span class="stat-value">{{ $sensors['ok'] }}</span>
                </a>
                <a href="{{ url('health') }}" class="stat-item stat-down">
                    <span class="stat-label">{{ __('Critical') }}</span>
                    <span class="stat-value">{{ $sensors['critical'] }}</span>
                </a>
                <a href="{{ url('health') }}" class="stat-item stat-disabled">
                    <span class="stat-label">{{ __('Alert Disabled') }}</span>
                    <span class="stat-value">{{ $sensors['disable_notify'] }}</span>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.device-summary-horiz-modern {
    padding: 8px;
}

.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}

.summary-card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    overflow: hidden;
}

.summary-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.summary-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #e0e0e0;
}

.summary-card-title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    text-decoration: none;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
    transition: color 0.2s ease;
}

.summary-card-title:hover {
    color: #2c539e;
}

.summary-card-title i {
    font-size: 16px;
    color: #6c757d;
}

.summary-total {
    font-size: 18px;
    font-weight: 700;
    color: #2c539e;
    text-decoration: none;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
    transition: color 0.2s ease;
}

.summary-total:hover {
    color: #1e3a6f;
}

.summary-stats {
    display: flex;
    flex-direction: column;
    padding: 6px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 10px;
    margin: 2px 0;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s ease;
    background: #f8f9fa;
}

.stat-item:hover {
    background: #e9ecef;
    transform: translateX(4px);
}

.stat-label {
    font-size: 11px;
    font-weight: 500;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.stat-value {
    font-size: 15px;
    font-weight: 700;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.stat-item.stat-up .stat-value {
    color: #28a745;
}

.stat-item.stat-down .stat-value {
    color: #dc3545;
}

.stat-item.stat-ignored .stat-value {
    color: #007bff;
}

.stat-item.stat-disabled .stat-value {
    color: #6c757d;
}

.stat-item.stat-shutdown .stat-value {
    color: #343a40;
}

.stat-item.stat-error .stat-value {
    color: #dc3545;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
    
    .summary-card-header {
        padding: 12px 14px;
    }
    
    .summary-card-title {
        font-size: 14px;
    }
    
    .summary-total {
        font-size: 18px;
    }
}
</style>
