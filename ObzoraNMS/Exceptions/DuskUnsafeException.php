<?php
namespace ObzoraNMS\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ObzoraNMS\Interfaces\Exceptions\UpgradeableException;
use Throwable;

class DuskUnsafeException extends \Exception implements UpgradeableException
{
    /**
     * Try to convert the given Exception to this exception
     */
    public static function upgrade(Throwable $exception): ?static
    {
        return $exception->getMessage() == 'It is unsafe to run Dusk in production.' ?
            new static($exception->getMessage(), $exception->getCode(), $exception) :
            null;
    }

    /**
     * Render the exception into an HTTP or JSON response.
     */
    public function render(Request $request): Response|JsonResponse
    {
        $title = trans('exceptions.dusk_unsafe.title');
        $message = trans('exceptions.dusk_unsafe.message', ['command' => './scripts/composer_wrapper.php install --no-dev']);

        return $request->wantsJson() ? response()->json([
            'status' => 'error',
            'message' => "$title: $message",
        ], 500) : response()->view('errors.generic', [
            'title' => $title,
            'content' => $message,
        ], 500);
    }
}
