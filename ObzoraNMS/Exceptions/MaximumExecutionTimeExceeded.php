<?php
namespace ObzoraNMS\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use ObzoraNMS\Interfaces\Exceptions\UpgradeableException;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Throwable;

class MaximumExecutionTimeExceeded extends \Exception implements UpgradeableException
{
    /**
     * Try to convert the given Exception to a FilePermissionsException
     */
    public static function upgrade(Throwable $exception): ?static
    {
        // cannot write to storage directory
        if ($exception instanceof FatalError &&
            Str::startsWith($exception->getMessage(), 'Maximum execution time of')) {
            return new static($exception->getMessage(), $exception->getCode(), $exception);
        }

        return null;
    }

    /**
     * Render the exception into an HTTP or JSON response.
     */
    public function render(Request $request): Response|JsonResponse
    {
        $title = preg_match('/ (\d+) /', $this->message, $matches)
            ? trans_choice('exceptions.maximum_execution_time_exceeded.title', (int) $matches[1], ['seconds' => (int) $matches[1]])
            : $this->getMessage();

        $message = trans('exceptions.maximum_execution_time_exceeded.message');

        return $request->wantsJson() ? response()->json([
            'status' => 'error',
            'message' => "$title: $message",
        ], 500) : response()->view('errors.generic', [
            'title' => $title,
            'content' => $message,
        ], 500);
    }
}
