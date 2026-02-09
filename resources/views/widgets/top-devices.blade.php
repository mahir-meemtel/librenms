@if($rows->isNotEmpty())
<div class="top-devices-modern">
    <div class="top-devices-table-container">
        <table class="table top-devices-table">
            <thead>
            <tr>
                <th>Device</th>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $column)
                        <td>{!! $column !!}</td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="top-devices-modern">
    <div class="top-devices-empty">
        <p class="empty-message">{{ __('No devices found within interval.') }}</p>
    </div>
</div>
@endif

<style>
.top-devices-modern {
    padding: 8px;
}

.top-devices-table-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    overflow: hidden;
}

.top-devices-table {
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}

.top-devices-table thead th {
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

.top-devices-table tbody tr {
    border-bottom: 1px solid #e9ecef;
    transition: background 0.2s ease;
}

.top-devices-table tbody tr:hover {
    background: #f8f9fa;
}

.top-devices-table tbody tr:last-child {
    border-bottom: none;
}

.top-devices-table tbody td {
    padding: 10px 12px;
    font-size: 13px;
    color: #2c3e50;
    vertical-align: middle;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.top-devices-table tbody td a {
    color: #2c539e;
    text-decoration: none;
    transition: color 0.2s ease;
}

.top-devices-table tbody td a:hover {
    color: #1e3a6f;
    text-decoration: underline;
}

.top-devices-empty {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    padding: 40px 20px;
    text-align: center;
}

.empty-message {
    font-size: 14px;
    color: #6c757d;
    margin: 0;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

@media (max-width: 768px) {
    .top-devices-table thead th,
    .top-devices-table tbody td {
        padding: 8px 10px;
        font-size: 12px;
    }
}
</style>
