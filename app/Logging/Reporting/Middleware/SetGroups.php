<?php
namespace App\Logging\Reporting\Middleware;

use ObzoraNMS\Util\Version;
use Spatie\FlareClient\Report;

class SetGroups implements \Spatie\FlareClient\FlareMiddleware\FlareMiddleware
{
    /**
     * Middleware to set ObzoraNMS and Tools grouping data
     *
     * @return mixed
     */
    public function handle(Report $report, \Closure $next)
    {
        try {
            $version = Version::get();

            $report->group('ObzoraNMS', [
                'Git version' => $version->name(),
                'App version' => Version::VERSION,
            ]);

            $report->group('Tools', [
                'Database' => $version->databaseServer(),
                'Net-SNMP' => $version->netSnmp(),
                'Python' => $version->python(),
                'RRDtool' => $version->rrdtool(),

            ]);
        } catch (\Exception $e) {
        }

        return $next($report);
    }
}
