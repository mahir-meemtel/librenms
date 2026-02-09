<?php
namespace App\Logging\Reporting\Middleware;

use Spatie\FlareClient\Report;

class CleanContext implements \Spatie\FlareClient\FlareMiddleware\FlareMiddleware
{
    /**
     * Middleware to remove sensitive data from the context.
     *
     * @return mixed
     */
    public function handle(Report $report, \Closure $next)
    {
        try {
            $report->setApplicationPath('');
            $context = $report->allContext();

            if (isset($context['request']['url'])) {
                $context['request']['url'] = str_replace($context['headers']['host'] ?? '', 'obzora', $context['request']['url']);
            }

            if (isset($context['session']['url']['intended'])) {
                $context['session']['url']['intended'] = str_replace($context['headers']['host'] ?? '', 'obzora', $context['session']['url']['intended']);
            }

            if (isset($context['session']['_previous']['url'])) {
                $context['session']['_previous']['url'] = str_replace($context['headers']['host'] ?? '', 'obzora', $context['session']['_previous']['url']);
            }

            $context['headers']['host'] = null;
            $context['headers']['referer'] = null;

            $report->userProvidedContext($context);
        } catch (\Exception $e) {
        }

        return $next($report);
    }
}
