<?php
namespace App\Guards;

use Illuminate\Auth\TokenGuard;

class ApiTokenGuard extends TokenGuard
{
    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest()
    {
        $token = $this->request->header('X-Auth-Token');

        if (empty($token)) {
            $token = parent::getTokenForRequest();
        }

        return $token;
    }
}
