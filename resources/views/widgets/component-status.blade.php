<div class="component-status-modern">
    <div class="component-status-grid">
        @foreach($status as $item)
            <div class="component-status-card">
                <div class="status-header status-{{ strtolower($item['text']) }}">
                    <span class="status-indicator status-{{ strtolower($item['text']) }}-dot"></span>
                    <span class="status-title">{{ $item['text'] }}</span>
                </div>
                <div class="status-value">
                    <span class="value-number">{{ $item['total'] }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
.component-status-modern {
    padding: 8px;
}

.component-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
}

.component-status-card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    overflow: hidden;
}

.component-status-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.status-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
    border-bottom: 2px solid #e0e0e0;
}

.status-header.status-ok {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #2c3e50;
}

.status-header.status-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
}

.status-header.status-critical {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

.status-indicator.status-ok-dot {
    background: #28a745;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
}

.status-indicator.status-warning-dot {
    background: #ffc107;
    box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.2);
}

.status-indicator.status-critical-dot {
    background: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
}

.status-title {
    flex: 1;
}

.status-value {
    padding: 16px 12px;
    text-align: center;
}

.value-number {
    font-size: 28px;
    font-weight: 700;
    font-family: 'Segoe UI', Roboto, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
}

.status-header.status-ok + .status-value .value-number {
    color: #28a745;
}

.status-header.status-warning + .status-value .value-number {
    color: #ffc107;
}

.status-header.status-critical + .status-value .value-number {
    color: #dc3545;
}

@media (max-width: 768px) {
    .component-status-grid {
        grid-template-columns: 1fr;
    }
    
    .status-header {
        padding: 8px 10px;
        font-size: 11px;
    }
    
    .value-number {
        font-size: 24px;
    }
}
</style>
