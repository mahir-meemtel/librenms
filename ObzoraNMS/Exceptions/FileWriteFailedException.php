<?php
namespace ObzoraNMS\Exceptions;

use Throwable;

class FileWriteFailedException extends \Exception
{
    /** @var string */
    protected $file_path;

    public function __construct($file, $code = 0, ?Throwable $previous = null)
    {
        $this->file_path = $file;
        parent::__construct("Failed to write file: $file", $code, $previous);
    }

    /**
     * Render the exception into an HTTP or JSON response.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function render(\Illuminate\Http\Request $request)
    {
        $title = trans('exceptions.file_write_failed.title');
        $message = trans('exceptions.file_write_failed.message', ['file' => $this->file_path]);

        return $request->wantsJson() ? response()->json([
            'status' => 'error',
            'message' => "$title: $message",
        ], 500) : response()->view('errors.generic', [
            'title' => $title,
            'content' => $message,
        ], 500);
    }
}
