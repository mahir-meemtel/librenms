<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use ObzoraNMS\Util\EnvHelper;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installed = ! config('obzora.install') && file_exists(base_path('.env'));
        $is_install_route = $request->is('install*');

        // further middleware will fail without an app key, init one
        if (empty(config('app.key'))) {
            config(['app.key' => EnvHelper::init()]);
        }

        if (! $installed && ! $is_install_route) {
            // redirect to install if not installed
            return redirect()->route('install');
        } elseif ($installed && $is_install_route) {
            // in case someone refreshes on the finish step
            if ($request->routeIs('install.finish')) {
                return redirect()->route('home');
            }
            throw new AuthorizationException('This should only be called during install');
        }

        return $next($request);
    }
}
