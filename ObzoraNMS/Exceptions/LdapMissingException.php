<?php
namespace ObzoraNMS\Exceptions;

use Exception;

class LdapMissingException extends AuthenticationException
{
    private const DEFAULT_MESSAGE = 'PHP does not support LDAP, please install or enable the PHP LDAP extension';

    public function __construct(
        string $message = self::DEFAULT_MESSAGE,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, false, $code, $previous);
    }

    /**
     * Render the exception into an HTTP or JSON response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render(\Illuminate\Http\Request $request)
    {
        $title = trans('exceptions.ldap_missing.title');
        $message = ($this->message == self::DEFAULT_MESSAGE) ? trans('exceptions.ldap_missing.message') : $this->getMessage();

        return $request->wantsJson() ? response()->json([
            'status' => 'error',
            'message' => "$title: $message",
        ], 500) : response()->view('errors.generic', [
            'title' => $title,
            'content' => $message,
        ], 500);
    }
}
