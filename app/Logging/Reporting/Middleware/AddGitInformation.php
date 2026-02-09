<?php
namespace App\Logging\Reporting\Middleware;

use ObzoraNMS\Util\Git;
use Spatie\FlareClient\Report;

class AddGitInformation implements \Spatie\FlareClient\FlareMiddleware\FlareMiddleware
{
    /**
     * @return mixed
     */
    public function handle(Report $report, \Closure $next)
    {
        $git = Git::make(180);

        $report->group('git', [
            'hash' => $git->commitHash(),
            'message' => $git->message(),
            'tag' => $git->shortTag(),
            'remote' => $git->remoteUrl(),
        ]);

        return $next($report);
    }
}
