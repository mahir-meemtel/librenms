<div class="device-summary-vert-modern">
    <div class="summary-cards-grid">
        <!-- Up Status Card -->
        <div class="summary-status-card">
            <div class="summary-status-header stat-up-bg">
                <span class="status-indicator status-up"></span>
                <span class="status-title">{{ __('Up') }}</span>
            </div>
            <div class="summary-status-values">
                <a href="{{ url('devices/format=list_detail/state=up') }}" class="status-value stat-up">
                    <span class="value-label">{{ __('Devices') }}</span>
                    <span class="value-number">{{ $devices['up'] }}</span>
                </a>
                <a href="{{ url('ports/format=list_detail/state=up') }}" class="status-value stat-up">
                    <span class="value-label">{{ __('Ports') }}</span>
                    <span class="value-number">{{ $ports['up'] }}</span>
                </a>
                @if($show_services)
                    <a href="{{ url('services/view=details/state=ok') }}" class="status-value stat-up">
                        <span class="value-label">{{ __('Services') }}</span>
                        <span class="value-number">{{ $services['ok'] }}</span>
                    </a>
                @endif
                @if($show_sensors)
                    <a href="{{ url('health') }}" class="status-value stat-up">
                        <span class="value-label">{{ __('Health') }}</span>
                        <span class="value-number">{{ $sensors['ok'] }}</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Down Status Card -->
        <div class="summary-status-card">
            <div class="summary-status-header stat-down-bg">
                <span class="status-indicator status-down"></span>
                <span class="status-title">{{ __('Down') }}</span>
            </div>
            <div class="summary-status-values">
                <a href="{{ url('devices/format=list_detail/state=down') }}" class="status-value stat-down">
                    <span class="value-label">{{ __('Devices') }}</span>
                    <span class="value-number">{{ $devices['down'] }}</span>
                </a>
                <a href="{{ url('ports/format=list_detail/state=down') }}" class="status-value stat-down">
                    <span class="value-label">{{ __('Ports') }}</span>
                    <span class="value-number">{{ $ports['down'] }}</span>
                </a>
                @if($show_services)
                    <a href="{{ url('services/view=details/state=critical') }}" class="status-value stat-down">
                        <span class="value-label">{{ __('Services') }}</span>
                        <span class="value-number">{{ $services['critical'] }}</span>
                    </a>
                @endif
                @if($show_sensors)
                    <a href="{{ url('health') }}" class="status-value stat-down">
                        <span class="value-label">{{ __('Health') }}</span>
                        <span class="value-number">{{ $sensors['critical'] }}</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Ignored Status Card -->
        <div class="summary-status-card">
            <div class="summary-status-header stat-ignored-bg">
                <span class="status-indicator status-ignored"></span>
                <span class="status-title">{{ __('Ignored tag') }}</span>
            </div>
            <div class="summary-status-values">
                <a href="{{ url('devices/format=list_detail/ignore=1') }}" class="status-value stat-ignored">
                    <span class="value-label">{{ __('Devices') }}</span>
                    <span class="value-number">{{ $devices['ignored'] }}</span>
                </a>
                <a href="{{ url('ports/format=list_detail/ignore=1') }}" class="status-value stat-ignored">
                    <span class="value-label">{{ __('Ports') }}</span>
                    <span class="value-number">{{ $ports['ignored'] }}</span>
                </a>
                @if($show_services)
                    <a href="{{ url('services/view=details/ignore=1') }}" class="status-value stat-ignored">
                        <span class="value-label">{{ __('Services') }}</span>
                        <span class="value-number">{{ $services['ignored'] }}</span>
                    </a>
                @endif
                @if($show_sensors)
                    <div class="status-value stat-disabled">
                        <span class="value-label">{{ __('Health') }}</span>
                        <span class="value-number">-</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Alert Disabled Status Card -->
        <div class="summary-status-card">
            <div class="summary-status-header stat-disabled-bg">
                <span class="status-indicator status-disabled"></span>
                <span class="status-title">{{ __('Alert disabled') }}</span>
            </div>
            <div class="summary-status-values">
                <a href="{{ url('devices/format=list_detail/disable_notify=1') }}" class="status-value stat-disabled">
                    <span class="value-label">{{ __('Devices') }}</span>
                    <span class="value-number">{{ $devices['disable_notify'] }}</span>
                </a>
                <div class="status-value stat-disabled">
                    <span class="value-label">{{ __('Ports') }}</span>
                    <span class="value-number">-</span>
                </div>
                @if($show_services)
                    <div class="status-value stat-disabled">
                        <span class="value-label">{{ __('Services') }}</span>
                        <span class="value-number">-</span>
                    </div>
                @endif
                @if($show_sensors)
                    <a href="{{ url('health') }}" class="status-value stat-disabled">
                        <span class="value-label">{{ __('Health') }}</span>
                        <span class="value-number">{{ $sensors['disable_notify'] }}</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Disabled/Shutdown Status Card -->
        <div class="summary-status-card">
            <div class="summary-status-header stat-shutdown-bg">
                <span class="status-indicator status-shutdown"></span>
                <span class="status-title">{{ __('Disabled') }}/{{ __('Shutdown') }}</span>
            </div>
            <div class="summary-status-values">
                <a href="{{ url('devices/format=list_detail/disabled=1') }}" class="status-value stat-shutdown">
                    <span class="value-label">{{ __('Devices') }}</span>
                    <span class="value-number">{{ $devices['disabled'] }}</span>
                </a>
                <a href="{{ url('ports/format=list_detail/state=admindown') }}" class="status-value stat-shutdown">
                    <span class="value-label">{{ __('Ports') }}</span>
                    <span class="value-number">{{ $ports['shutdown'] }}</span>
                </a>
                @if($show_services)
                    <a href="{{ url('services/view=details/disabled=1') }}" class="status-value stat-shutdown">
                        <span class="value-label">{{ __('Services') }}</span>
                        <span class="value-number">{{ $services['disabled'] }}</span>
                    </a>
                @endif
                @if($show_sensors)
                    <div class="status-value stat-disabled">
                        <span class="value-label">{{ __('Health') }}</span>
                        <span class="value-number">-</span>
                    </div>
                @endif
            </div>
        </div>

        @if($summary_errors)
        <!-- Errored Status Card -->
        <div class="summary-status-card">
            <div class="summary-status-header stat-error-bg">
                <span class="status-indicator status-error"></span>
                <span class="status-title">{{ __('Errored') }}</span>
            </div>
            <div class="summary-status-values">
                <div class="status-value stat-disabled">
                    <span class="value-label">{{ __('Devices') }}</span>
                    <span class="value-number">-</span>
                </div>
                <a href="{{ url('ports/format=list_detail/errors=1') }}" class="status-value stat-error">
                    <span class="value-label">{{ __('Ports') }}</span>
                    <span class="value-number">{{ $ports['errored'] }}</span>
                </a>
                @if($show_services)
                    <div class="status-value stat-disabled">
                        <span class="value-label">{{ __('Services') }}</span>
                        <span class="value-number">-</span>
                    </div>
                @endif
                @if($show_sensors)
                    <div class="status-value stat-disabled">
                        <span class="value-label">{{ __('Health') }}</span>
                        <span class="value-number">-</span>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Total Status Card -->
        <div class="summary-status-card">
            <div class="summary-status-header stat-total-bg">
                <span class="status-indicator status-total"></span>
                <span class="status-title">{{ __('Total') }}</span>
            </div>
            <div class="summary-status-values">
                <a href="{{ url('devices') }}" class="status-value stat-total">
                    <span class="value-label">{{ __('Devices') }}</span>
                    <span class="value-number">{{ $devices['total'] }}</span>
                </a>
                <a href="{{ url('ports') }}" class="status-value stat-total">
                    <span class="value-label">{{ __('Ports') }}</span>
                    <span class="value-number">{{ $ports['total'] }}</span>
                </a>
                @if($show_services)
                    <a href="{{ url('services') }}" class="status-value stat-total">
                        <span class="value-label">{{ __('Services') }}</span>
                        <span class="value-number">{{ $services['total'] }}</span>
                    </a>
                @endif
                @if($show_sensors)
                    <a href="{{ url('health') }}" class="status-value stat-total">
                        <span class="value-label">{{ __('Health') }}</span>
                        <span class="value-number">{{ $sensors['total'] }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.device-summary-vert-modern {
    padding: 8px;
}

.summary-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}

.summary-status-card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    overflow: hidden;
}

.summary-status-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.summary-status-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    font-size: 13px;
    font-weight: 600;
    color: #ffffff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.stat-up-bg {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.stat-down-bg {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.stat-ignored-bg {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.stat-disabled-bg {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
}

.stat-shutdown-bg {
    background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
}

.stat-error-bg {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.stat-total-bg {
    background: linear-gradient(135deg, #2c539e 0%, #1e3a6f 100%);
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
    background: rgba(255, 255, 255, 0.9);
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
}

.status-title {
    flex: 1;
}

.summary-status-values {
    display: flex;
    flex-direction: column;
    padding: 6px;
}

.status-value {
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

.status-value:hover {
    background: #e9ecef;
    transform: translateX(4px);
}

.value-label {
    font-size: 11px;
    font-weight: 500;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.value-number {
    font-size: 15px;
    font-weight: 700;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.stat-up .value-number {
    color: #28a745;
}

.stat-down .value-number {
    color: #dc3545;
}

.stat-ignored .value-number {
    color: #007bff;
}

.stat-disabled .value-number {
    color: #6c757d;
}

.stat-shutdown .value-number {
    color: #343a40;
}

.stat-error .value-number {
    color: #dc3545;
}

.stat-total .value-number {
    color: #2c539e;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .summary-cards-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .summary-status-header {
        padding: 8px 10px;
        font-size: 12px;
    }
    
    .status-value {
        padding: 6px 8px;
    }
    
    .value-label {
        font-size: 10px;
    }
    
    .value-number {
        font-size: 14px;
    }
}
</style>
