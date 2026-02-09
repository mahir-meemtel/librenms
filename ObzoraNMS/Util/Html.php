<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;
use ObzoraNMS\Enum\PowerState;
use ObzoraNMS\Enum\Severity;

class Html
{
    /**
     * return icon and color for application state
     *
     * @param  string  $app_state
     * @return array
     */
    public static function appStateIcon($app_state)
    {
        switch ($app_state) {
            case 'OK':
                $icon = '';
                $color = '';
                $hover_text = 'OK';
                break;
            case 'ERROR':
                $icon = 'fa-close';
                $color = '#cc1122';
                $hover_text = 'Error';
                break;
            case 'LEGACY':
                $icon = 'fa-warning';
                $color = '#eebb00';
                $hover_text = 'legacy Agent Script';
                break;
            case 'UNSUPPORTED':
                $icon = 'fa-flash';
                $color = '#ff9900';
                $hover_text = 'Unsupported Agent Script Version';
                break;
            default:
                $icon = 'fa-question';
                $color = '#777777';
                $hover_text = 'Unknown State';
                break;
        }

        return ['icon' => $icon, 'color' => $color, 'hover_text' => $hover_text];
    }

    /**
     * Print or return a row of graphs
     *
     * @param  array  $graph_array
     * @param  bool  $print
     * @return array
     */
    public static function graphRow($graph_array, $print = false)
    {
        if (session('widescreen')) {
            if (! array_key_exists('height', $graph_array)) {
                $graph_array['height'] = '110';
            }

            if (! array_key_exists('width', $graph_array)) {
                $graph_array['width'] = '215';
            }

            $periods = ObzoraConfig::get('graphs.mini.widescreen');
        } else {
            if (! array_key_exists('height', $graph_array)) {
                $graph_array['height'] = '100';
            }

            if (! array_key_exists('width', $graph_array)) {
                $graph_array['width'] = '215';
            }

            $periods = ObzoraConfig::get('graphs.mini.normal');
        }

        $screen_width = session('screen_width');
        if ($screen_width) {
            if ($screen_width < 1024 && $screen_width > 700) {
                $graph_array['width'] = round(($screen_width - 90) / 2, 0);
            } elseif ($screen_width > 1024) {
                $graph_array['width'] = round(($screen_width - 90) / count($periods) + 1, 0);
            } else {
                $graph_array['width'] = $screen_width - 70;
            }
        }

        $graph_array['height'] = round($graph_array['width'] / 2.15);

        $graph_data = [];
        foreach ($periods as $period => $period_text) {
            $graph_array['from'] = ObzoraConfig::get("time.$period");
            $graph_array_zoom = $graph_array;
            $graph_array_zoom['height'] = '150';
            $graph_array_zoom['width'] = '400';

            $link_array = $graph_array;
            $link_array['page'] = 'graphs';
            unset($link_array['height'], $link_array['width']);
            $link = Url::generate($link_array);

            $full_link = Url::overlibLink($link, Url::lazyGraphTag($graph_array), Url::graphTag($graph_array_zoom));
            $graph_data[] = $full_link;

            if ($print) {
                echo "<div class='col-md-3'>$full_link</div>";
            }
        }

        return $graph_data;
    }

    public static function percentageBar($width, $height, $percent, $left_text = '', $right_text = '', $warn = null, $shadow = null, $colors = null, $type = null)
    {
        $percent = min($percent, 100);
        
        // Get modern color scheme based on type
        if ($colors === null) {
            $colors = Color::percentage($percent, $warn ?: null, $type);
        }
        
        $default = Color::percentage(0, null, $type);
        $left_text_color = $colors['left_text'] ?? 'ffffff';
        $right_text_color = $colors['right_text'] ?? 'ffffff';
        $left_color = $colors['left'] ?? $default['left'];
        $right_color = $colors['right'] ?? $default['right'];

        $output = '<div style="width:' . $width . 'px; height:' . $height . 'px; position: relative; margin: 0 auto;">
        <div class="progress modern-progress-bar" style="background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%); height:' . $height . 'px;margin-bottom:-' . $height . 'px; border-radius: 14px; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05); position: relative; border: none; outline: none;">';

        if ($shadow !== null) {
            $shadow = min($shadow, 100);
            $middle_color = $colors['middle'] ?? $default['middle'];
            $output .= '<div class="progress-bar progress-bar-shadow" role="progressbar" aria-valuenow="' . $shadow . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $percent . '%; background: linear-gradient(135deg, #' . $middle_color . ' 0%, #' . $middle_color . 'dd 100%); border-radius: 14px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1); position: absolute; top: 0; left: 0; height: 100%; z-index: 0;">';
        }

        // Use type-specific styling if type is set, otherwise use inline colors
        $type_class = $type ? 'progress-bar-type-' . $type : '';
        $background_style = $type ? '' : 'background: linear-gradient(135deg, #' . $left_color . ' 0%, #' . $right_color . ' 100%);';
        
        $output .= '<div class="progress-bar modern-progress-fill ' . $type_class . '" role="progressbar" aria-valuenow="' . $percent . '" aria-valuemin="0" aria-valuemax="100" style="width:' . $percent . '%; ' . $background_style . ' border-radius: 14px; box-shadow: none; transition: width 0.9s cubic-bezier(0.34, 1.56, 0.64, 1); position: relative; overflow: hidden; z-index: 2; border: none; outline: none; background-image: none;">
        <div class="progress-shimmer" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.5) 30%, rgba(255, 255, 255, 0.6) 50%, rgba(255, 255, 255, 0.5) 70%, transparent 100%); animation: shimmer 3s ease-in-out infinite; pointer-events: none; will-change: transform;"></div>
        <div class="progress-glow" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(ellipse at center, rgba(255, 255, 255, 0.15) 0%, transparent 70%); pointer-events: none;"></div>
        <b class="progress-text-left" style="padding-left: 8px; position: absolute; top: 50%; transform: translateY(-50%); left: 0; color:#' . $left_text_color . '; z-index: 10; font-weight: 600; font-size: 11px; text-shadow: 0 1px 2px rgba(0,0,0,0.3); pointer-events: none; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . $left_text . '</b>
        </div></div>
        <b class="progress-text-right" style="padding-right: 8px; position: absolute; top: 50%; transform: translateY(-50%); right: 0; color:#' . $right_text_color . '; z-index: 10; font-weight: 600; font-size: 11px; text-shadow: 0 1px 2px rgba(0,0,0,0.3); pointer-events: none;">' . $right_text . '</b>
        </div>';

        return $output;
    }
    
    /**
     * Get modern color scheme based on type and percentage
     *
     * @param  float  $percent
     * @param  float|null  $warn
     * @param  string|null  $type  'processor', 'memory', 'storage', or null
     * @return array
     */
    private static function getModernColors($percent, $warn = null, $type = null)
    {
        $perc_warn = $warn ?? 75;
        
        // Determine severity level
        $is_critical = $percent > $perc_warn;
        $is_warning = $percent > 75;
        $is_moderate = $percent > 50;
        $is_low = $percent > 25;
        
        // Type-specific color schemes
        switch ($type) {
            case 'processor':
            case 'cpu':
                if ($is_critical) {
                    return ['left' => 'e74c3c', 'right' => 'c0392b', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                } elseif ($is_warning) {
                    return ['left' => 'f39c12', 'right' => 'e67e22', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                } elseif ($is_moderate) {
                    return ['left' => '3498db', 'right' => '2980b9', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                } else {
                    return ['left' => '5dade2', 'right' => '3498db', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                }
                
            case 'memory':
            case 'mempool':
                if ($is_critical) {
                    return ['left' => 'e74c3c', 'right' => 'c0392b', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                } elseif ($is_warning) {
                    return ['left' => 'f39c12', 'right' => 'e67e22', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                } elseif ($is_moderate) {
                    return ['left' => '1abc9c', 'right' => '16a085', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                } else {
                    return ['left' => '2ecc71', 'right' => '27ae60', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                }
                
            case 'storage':
            case 'disk':
                if ($is_critical) {
                    return ['left' => 'e74c3c', 'right' => 'c0392b', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                } elseif ($is_warning) {
                    return ['left' => 'f39c12', 'right' => 'e67e22', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                } elseif ($is_moderate) {
                    return ['left' => 'f1c40f', 'right' => 'f39c12', 'left_text' => '2c3e50', 'right_text' => '2c3e50'];
                } else {
                    return ['left' => 'f39c12', 'right' => 'e67e22', 'left_text' => 'ffffff', 'right_text' => 'ffffff'];
                }
                
            default:
                // Default color scheme (original behavior)
                return Color::percentage($percent, $warn);
        }
    }

    /**
     * @param  int|string  $state
     */
    public static function powerStateLabel($state): array
    {
        $state = is_string($state) ? PowerState::STATES[$state] : $state;

        switch ($state) {
            case PowerState::OFF:
                return ['OFF', 'label-default'];
            case PowerState::ON:
                return ['ON', 'label-success'];
            case PowerState::SUSPENDED:
                return ['SUSPENDED', 'label-warning'];
            default:
                return ['UNKNOWN', 'label-default'];
        }
    }

    public static function severityToLabel(Severity $severity, string $text): string
    {
        $state_label = match ($severity) {
            Severity::Ok => 'label-success',
            Severity::Info => 'label-info',
            Severity::Notice => 'label-primary',
            Severity::Warning => 'label-warning',
            Severity::Error => 'label-danger',
            default => 'label-default',
        };

        return "<span class=\"label $state_label\">$text</span>";
    }
}
