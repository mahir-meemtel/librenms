<?php
namespace ObzoraNMS\Util;

use App\Models\BgpPeer;
use App\Models\Device;
use App\Models\Port;

class Color
{
    /**
     * Get colors for a percentage bar based on current percentage
     *
     * @param  int|float  $percentage
     * @param  int|float  $component_perc_warn
     * @param  string|null  $type
     * @return string[]
     */
    public static function percentage($percentage, $component_perc_warn = null, $type = null): array
    {
        // Use type-specific colors if type is provided
        if ($type !== null) {
            return self::percentageByType($percentage, $component_perc_warn, $type);
        }

        $perc_warn = 75;

        if (isset($component_perc_warn)) {
            $perc_warn = round($component_perc_warn, 0);
        }

        if ($percentage > $perc_warn) {
            return [
                'left' => 'c4323f',
                'right' => 'c96a73',
                'middle' => 'c75862',
            ];
        }

        if ($percentage > 75) {
            return [
                'left' => 'bf5d5b',
                'right' => 'd39392',
                'middle' => 'c97e7d',
            ];
        }

        if ($percentage > 50) {
            return [
                'left' => 'bf875b',
                'right' => 'd3ae92',
                'middle' => 'cca07e',
            ];
        }

        if ($percentage > 25) {
            return [
                'left' => '5b93bf',
                'right' => '92b7d3',
                'middle' => '7da8c9',
            ];
        }

        return [
            'left' => '9abf5b',
            'right' => 'bbd392',
            'middle' => 'afcc7c',
        ];
    }

    /**
     * Get type-specific colors for percentage bars
     * Memory: Blue tones, Processor: Purple/Indigo tones, Storage: Teal/Cyan tones
     *
     * @param  int|float  $percentage
     * @param  int|float  $component_perc_warn
     * @param  string  $type
     * @return string[]
     */
    public static function percentageByType($percentage, $component_perc_warn = null, $type = 'default'): array
    {
        $perc_warn = 75;

        if (isset($component_perc_warn)) {
            $perc_warn = round($component_perc_warn, 0);
        }

        // Define base color schemes for each type
        $colorSchemes = [
            'memory' => [
                'low' => ['10b981', '34d399', '22c55e'],      // Green
                'medium' => ['059669', '10b981', '0d9488'],   // Darker Green
                'high' => ['047857', '059669', '052e16'],     // Dark Green
                'critical' => ['13a87a', '15c492', '14b967'], // Very Dark Green
            ],
            'processor' => [
                'low' => ['8b5cf6', 'a78bfa', '9770f8'],      // Purple
                'medium' => ['7c3aed', '8b5cf6', '8248f1'],   // Darker Purple
                'high' => ['6d28d9', '7c3aed', '7481e3'],     // Dark Purple
                'critical' => ['5b21b6', '6d28d9', '6424c7'], // Very Dark Purple
            ],
            'storage' => [
                'low' => ['14b8a6', '5eead4', '36c9b8'],      // Teal
                'medium' => ['0d9488', '14b8a6', '10a896'],   // Darker Teal
                'high' => ['0f766e', '0d9488', '0e8579'],     // Dark Teal
                'critical' => ['134e4a', '0f766e', '11625a'], // Very Dark Teal
            ],
        ];

        // Get the appropriate color scheme
        $scheme = $colorSchemes[$type] ?? $colorSchemes['memory'];

        // Determine which color level to use based on percentage
        if ($percentage > $perc_warn) {
            return [
                'left' => $scheme['critical'][0],
                'right' => $scheme['critical'][1],
                'middle' => $scheme['critical'][2],
            ];
        }

        if ($percentage > 75) {
            return [
                'left' => $scheme['high'][0],
                'right' => $scheme['high'][1],
                'middle' => $scheme['high'][2],
            ];
        }

        if ($percentage > 50) {
            return [
                'left' => $scheme['medium'][0],
                'right' => $scheme['medium'][1],
                'middle' => $scheme['medium'][2],
            ];
        }

        if ($percentage > 25) {
            return [
                'left' => $scheme['low'][0],
                'right' => $scheme['low'][1],
                'middle' => $scheme['low'][2],
            ];
        }

        return [
            'left' => $scheme['low'][0],
            'right' => $scheme['low'][1],
            'middle' => $scheme['low'][2],
        ];
    }

    public static function percent(int|float|null $numerator = null, int|float|null $denominator = null, int|float|null $percent = null): string
    {
        $percent = $percent ? round($percent) : Number::calculatePercent($numerator, $denominator, 0);
        $r = min(255, 5 * ($percent - 25));
        $b = max(0, 255 - (5 * ($percent + 25)));

        return sprintf('#%02x%02x%02x', $r, $b, $b);
    }

    /**
     * Get highlight color based on device status
     */
    public static function forDeviceStatus(Device $device): string
    {
        if ($device->disabled) {
            return '#808080';
        }

        if ($device->ignore) {
            return '#000000';
        }

        return $device->status ? '#008000' : '#ff0000';
    }

    /**
     * Get highlight color based on Port status
     */
    public static function forPortStatus(Port $port): string
    {
        // Ignored ports
        if ($port->ignore) {
            return '#000000';
        }

        // Shutdown ports
        if ($port->ifAdminStatus === 'down') {
            return '#808080';
        }

        // Down Ports
        if ($port->ifOperStatus !== 'up') {
            return '#ff0000';
        }

        // Errored ports
        if ($port->ifInErrors_delta > 0 || $port->ifOutErrors_delta > 0) {
            return '#ffa500';
        }

        // Up ports
        return '#008000';
    }

    /**
     * Get highlight color based on BgpPeer status
     */
    public static function forBgpPeerStatus(BgpPeer $peer): string
    {
        // Session inactive
        if ($peer->bgpPeerAdminStatus !== 'start') {
            return '#000000';
        }

        // Session active but errored
        if ($peer->bgpPeerState !== 'established') {
            return '#ffa500';
        }

        // Session Up
        return '#008000';
    }
}
