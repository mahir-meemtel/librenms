<?php
namespace ObzoraNMS\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use ObzoraNMS\Interfaces\Exceptions\UpgradeableException;
use ObzoraNMS\ValidationResult;
use ObzoraNMS\Validations\Database;
use ObzoraNMS\Validator;
use Throwable;

class DatabaseInconsistentException extends \Exception implements UpgradeableException
{
    /**
     * @var \ObzoraNMS\ValidationResult[]
     */
    private $validationResults;

    public function __construct($validationResults, $message = '', $code = 0, ?Throwable $previous = null)
    {
        $this->validationResults = $validationResults;
        parent::__construct($message, $code, $previous);
    }

    public static function upgrade(Throwable $exception): ?static
    {
        if ($exception instanceof QueryException || $exception->getPrevious() instanceof QueryException) {
            try {
                $validator = new Validator();
                (new Database())->validate($validator);

                // get only failed results
                $results = array_filter($validator->getResults('database'), function (ValidationResult $result) {
                    return $result->getStatus() === ValidationResult::FAILURE;
                });

                if ($results) {
                    return new static($results, $exception->getMessage(), 0, $exception);
                }
            } catch (\Exception) {
                return null;
            }
        }

        return null;
    }

    /**
     * Render the exception into an HTTP or JSON response.
     */
    public function render(Request $request): Response|JsonResponse
    {
        $message = trans('exceptions.database_inconsistent.title');
        if (isset($this->validationResults[0])) {
            $message .= ': ' . $this->validationResults[0]->getMessage();
        }

        return $request->wantsJson() ? response()->json([
            'status' => 'error',
            'message' => $message,
        ], 500) : response()->view('errors.db_inconsistent', [
            'results' => $this->validationResults,
        ], 500);
    }
}
