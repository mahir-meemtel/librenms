<?php
namespace ObzoraNMS\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ObzoraNMS\Interfaces\Exceptions\UpgradeableException;

class DatabaseConnectException extends \Exception implements UpgradeableException
{
    /**
     * Try to convert the given Exception to a DatabaseConnectException
     *
     * @param  \Exception  $exception
     * @return static|null
     */
    public static function upgrade($exception): ?static
    {
        // connect exception, convert to our standard connection exception
        return $exception instanceof QueryException && in_array($exception->getCode(), [1044, 1045, 2002]) ?
            new static(
                config('app.debug') ? $exception->getMessage() : $exception->getPrevious()->getMessage(),
                $exception->getCode(),
                $exception
            ) :
            null;
    }

    /**
     * Render the exception into an HTTP or JSON response.
     */
    public function render(Request $request): Response|JsonResponse
    {
        $title = trans('exceptions.database_connect.title');

        return $request->wantsJson() ? response()->json([
            'status' => 'error',
            'message' => "$title: " . $this->getMessage(),
        ], 503) : response()->view('errors.generic', [
            'title' => $title,
            'content' => $this->getMessage(),
        ], 503);
    }
}
