<?php
namespace App\Providers;

use App\Models\ApiToken;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

class TokenUserProvider extends LegacyUserProvider implements UserProvider
{
    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return Authenticatable|null
     */
    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param  Authenticatable  $user
     * @param  string  $token
     * @return void
     */
    public function updateRememberToken(Authenticatable $user, $token): void
    {
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (! ApiToken::isValid($credentials['api_token'])) {
            return null;
        }

        $user = ApiToken::userFromToken($credentials['api_token']);
        if (! is_null($user)) {
            return $user;
        }

        // missing user for existing token, create it assuming legacy auth_id
        $api_token = ApiToken::where('token_hash', $credentials['api_token'])->first();
        /** @var \App\Models\User|null */
        $user = $this->retrieveByLegacyId($api_token->user_id);

        // update token user_id
        if ($user) {
            $api_token->user_id = $user->user_id;
            $api_token->save();
        }

        return $user;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        /** @var \App\Models\User $user */
        return ApiToken::isValid($credentials['api_token'], $user->user_id);
    }
}
