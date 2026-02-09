<?php
namespace ObzoraNMS\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ObzoraNMS\Interfaces\Exceptions\UpgradeableException;
use Throwable;

class UnserializableRouteCache extends \Exception implements UpgradeableException
{
    protected $cli_php_version;
    protected $web_php_version;

    public function __construct($message = '', $code = 0, ?Throwable $previous = null, $cli_php_version = null, $web_php_version = null)
    {
        $this->cli_php_version = $cli_php_version;
        $this->web_php_version = $web_php_version;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Try to convert the given Exception to this exception
     */
    public static function upgrade(Throwable $exception): ?static
    {
        $errorMessage = "Erroneous data format for unserializing 'Symfony\Component\Routing\CompiledRoute'";
        if ($exception instanceof \ErrorException && $exception->message == $errorMessage) {
            $cli = rtrim(shell_exec('php -r "echo PHP_VERSION;"'));
            if (version_compare($cli, PHP_VERSION, '!=')) {
                return new static($exception->getMessage(), $exception->getCode(), $exception, $cli, PHP_VERSION);
            }
        }

        return null;
    }

    /**
     * Render the exception into an HTTP or JSON response.
     */
    public function render(Request $request): Response|JsonResponse
    {
        $title = trans('exceptions.unserializable_route_cache.title');
        $message = trans('exceptions.unserializable_route_cache.message', ['cli_version' => $this->cli_php_version, 'web_version' => $this->web_php_version]);

        return $request->wantsJson() ? response()->json([
            'status' => 'error',
            'message' => "$title: $message",
        ], 500) : response()->view('errors.generic', [
            'title' => $title,
            'content' => $message,
        ], 500);
    }
}
