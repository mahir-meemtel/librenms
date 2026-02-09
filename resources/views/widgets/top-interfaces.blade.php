<div class="top-interfaces-modern">
    <div class="top-interfaces-header">
        <h4 class="top-interfaces-title">Top {{ $interface_count }} interfaces polled within {{ $time_interval }} minutes</h4>
    </div>
    <div class="top-interfaces-table-container">
        <table class="table top-interfaces-table">
            <thead>
            <tr>
                <th>{{ __('Device') }}</th>
                <th>{{ __('Interface') }}</th>
                <th>{{ __('Total traffic') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ports as $port)
                <tr>
                    <td><x-device-link :device="$port->device">{{$port->device->shortDisplayName() }}</x-device-link></td>
                    <td><x-port-link :port="$port">{{ $port->getShortLabel() }}</x-port-link></td>
                    <td><x-port-link :port="$port"><x-graph :port="$port" type="port_bits" width="150" height="21"></x-graph></x-port-link></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.top-interfaces-modern {
    padding: 8px;
}

.top-interfaces-header {
    margin-bottom: 12px;
}

.top-interfaces-title {
    font-size: 14px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.top-interfaces-table-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    overflow: hidden;
}

.top-interfaces-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.top-interfaces-table thead th {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #2c3e50;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 12px;
    border: none;
    border-bottom: 2px solid #e0e0e0;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
    text-align: left;
}

.top-interfaces-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: background 0.2s ease;
}

.top-interfaces-table tbody tr:hover {
    background: #f8f9fa;
}

.top-interfaces-table tbody tr:last-child {
    border-bottom: none;
}

.top-interfaces-table tbody td {
    padding: 10px 12px;
    font-size: 13px;
    color: #2c3e50;
    vertical-align: middle;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.top-interfaces-table tbody td a {
    color: #2c539e;
    text-decoration: none;
    transition: color 0.2s ease;
}

.top-interfaces-table tbody td a:hover {
    color: #1e3a6f;
    text-decoration: underline;
}

@media (max-width: 768px) {
    .top-interfaces-table thead th,
    .top-interfaces-table tbody td {
        padding: 8px 10px;
        font-size: 12px;
    }
}
</style>
