<?php
namespace App\Http\Middleware;

use App\Facades\ObzoraConfig;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ObzoraNMS\Exceptions\InvalidIpException;
use ObzoraNMS\Util\IP;

class AuthenticateGraph
{
    /** @var string[] */
    protected $auth = [
        LegacyExternalAuth::class,
        Authenticate::class,
        VerifyTwoFactor::class,
        LoadUserPreferences::class,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  string|null  $relative
     *
     * @throws AuthenticationException
     */
    public function handle(Request $request, Closure $next, $relative = null): Response
    {
        // if user is logged in, allow
        if (\Auth::check()) {
            return $next($request);
        }

        // bypass normal auth if signed
        if ($request->hasValidSignature($relative !== 'relative')) {
            return $next($request);
        }

        // bypass normal auth if ip is allowed (or all IPs)
        if ($this->isAllowed($request)) {
            return $next($request);
        }

        // unauthenticated, force login
        throw new AuthenticationException('Unauthenticated.');
    }

    protected function isAllowed(Request $request): bool
    {
        if (ObzoraConfig::get('allow_unauth_graphs', false)) {
            d_echo("Unauthorized graphs allowed\n");

            return true;
        }

        $ip = $request->getClientIp();
        try {
            $client_ip = IP::parse($ip);
            foreach (ObzoraConfig::get('allow_unauth_graphs_cidr', []) as $range) {
                if ($client_ip->inNetwork($range)) {
                    d_echo("Unauthorized graphs allowed from $range\n");

                    return true;
                }
            }
        } catch (InvalidIpException $e) {
            d_echo("Client IP ($ip) is invalid.\n");
        }

        return false;
    }
}
