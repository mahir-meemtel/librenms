<div class="availability-map-widget">
    @if($device_totals || $services_totals)
    <div class="availability-summary">
        @if($device_totals)
        <div class="summary-group">
            <span class="summary-label">{{ __('Total hosts') }}</span>
            <div class="summary-badges">
                <a href="{{ url('devices/state=up') }}@if($device_group){{ '/group='.$device_group }}@endif" class="badge badge-success" title="{{ __('up') }}">
                    <span class="badge-value">{{ $device_totals['up'] }}</span>
                    <span class="badge-label">{{ __('up') }}</span>
                </a>
                <span class="badge badge-warning" title="{{ __('warn') }}">
                    <span class="badge-value">{{ $device_totals['warn'] }}</span>
                    <span class="badge-label">{{ __('warn') }}</span>
                </span>
                <a href="{{ url('devices/state=down') }}@if($device_group){{ '/group='.$device_group }}@endif" class="badge badge-danger" title="{{ __('down') }}">
                    <span class="badge-value">{{ $device_totals['down'] }}</span>
                    <span class="badge-label">{{ __('down') }}</span>
                </a>
                @if($device_totals['maintenance'])
                <span class="badge badge-secondary" title="{{ __('alerting.maintenance.maintenance') }}">
                    <span class="badge-value">{{ $device_totals['maintenance'] }}</span>
                    <span class="badge-label">{{ __('alerting.maintenance.maintenance') }}</span>
                </span>
                @endif
                @if($show_disabled_and_ignored)
                <a href="{{ url('devices/disable_notify=1') }}" class="badge badge-light" title="{{ __('alert-disabled') }}">
                    <span class="badge-value">{{ $device_totals['ignored'] }}</span>
                    <span class="badge-label">{{ __('alert-disabled') }}</span>
                </a>
                <a href="{{ url('devices/disabled=1') }}" class="badge badge-dark" title="{{ __('disabled') }}">
                    <span class="badge-value">{{ $device_totals['disabled'] }}</span>
                    <span class="badge-label">{{ __('disabled') }}</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        @if($services_totals)
        <div class="summary-group">
            <span class="summary-label">{{ __('Total services') }}</span>
            <div class="summary-badges">
                <span class="badge badge-success" title="{{ __('up') }}">
                    <span class="badge-value">{{ $services_totals['up'] }}</span>
                    <span class="badge-label">{{ __('up') }}</span>
                </span>
                <span class="badge badge-warning" title="{{ __('warn') }}">
                    <span class="badge-value">{{ $services_totals['warn'] }}</span>
                    <span class="badge-label">{{ __('warn') }}</span>
                </span>
                <span class="badge badge-danger" title="{{ __('down') }}">
                    <span class="badge-value">{{ $services_totals['down'] }}</span>
                    <span class="badge-label">{{ __('down') }}</span>
                </span>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="availability-items">
        @foreach($devices as $row)
            <a href="{{ $row['link'] }}" class="availability-item" title="{{$row['tooltip'] }}">
                @if($type == 0)
                    <span class="availability-badge {{ $row['labelClass'] }}">{{ $row['label'] }}</span>
                @else
                    <div class="availability-tile {{ $row['labelClass'] }}" style="width:{{ $tile_size }}px;height:{{ $tile_size }}px;"></div>
                @endif
            </a>
        @endforeach

        @foreach($services as $row)
            <a href="{{ $row['link'] }}" class="availability-item" title="{{$row['tooltip'] }}">
                @if($type == 0)
                    <span class="availability-badge {{ $row['labelClass'] }}">{{ $row['label'] }}</span>
                @else
                    <div class="availability-tile {{ $row['labelClass'] }}" style="width:{{ $tile_size }}px;height:{{ $tile_size }}px;"></div>
                @endif
            </a>
        @endforeach
    </div>
</div>

<style>
.availability-map-widget {
    padding: 8px;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.availability-summary {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e9ecef;
}

.summary-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.summary-label {
    font-size: 0.85em;
    font-weight: 600;
    color: #495057;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 90px;
}

.summary-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.summary-badges .badge {
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-width: 50px;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.summary-badges .badge:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    text-decoration: none;
}

.summary-badges .badge .badge-value {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 2px;
}

.summary-badges .badge .badge-label {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
}

.summary-badges .badge-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: #fff;
}

.summary-badges .badge-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: #212529;
}

.summary-badges .badge-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: #fff;
}

.summary-badges .badge-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
    color: #fff;
}

.summary-badges .badge-light {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #495057;
    border: 1px solid #dee2e6;
}

.summary-badges .badge-dark {
    background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
    color: #fff;
}

.availability-items {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: flex-start;
}

.availability-item {
    text-decoration: none;
    display: inline-block;
    transition: transform 0.15s ease, opacity 0.15s ease;
}

.availability-item:hover {
    transform: scale(1.1);
    opacity: 0.9;
    text-decoration: none;
    z-index: 10;
    position: relative;
}

.availability-badge {
    display: inline-block;
    padding: 4px 8px;
    margin: 2px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    min-width: 2.4em;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
}

.availability-badge.label-success {
    background: #28a745;
    color: #fff;
}

.availability-badge.label-warning {
    background: #ffc107;
    color: #212529;
}

.availability-badge.label-danger {
    background: #dc3545;
    color: #fff;
}

.availability-badge.label-default {
    background: #6c757d;
    color: #fff;
}

.availability-tile {
    border-radius: 3px;
    margin: 2px;
    transition: all 0.2s ease;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.availability-tile:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.availability-map-oldview-box-up {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
}

.availability-map-oldview-box-warn {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
}

.availability-map-oldview-box-down {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
}

.availability-map-oldview-box-ignored {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
}

.availability-map-oldview-box-ignored-up {
    background: linear-gradient(135deg, #5CB85C 0%, #4cae4c 100%) !important;
}

.availability-map-oldview-box-ignored-down {
    background: linear-gradient(135deg, #36393D 0%, #2a2d30 100%) !important;
}

@media (max-width: 768px) {
    .availability-summary {
        flex-direction: column;
        gap: 12px;
    }
    
    .summary-label {
        min-width: auto;
        width: 100%;
    }
    
    .summary-badges .badge {
        min-width: 45px;
        padding: 5px 8px;
    }
    
    .summary-badges .badge .badge-value {
        font-size: 14px;
    }
}
</style>
