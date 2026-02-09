<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiToken extends BaseModel
{
    public $timestamps = false;
    protected $table = 'api_tokens';

    // ---- Helper Functions ----

    /**
     * Check if the given token is valid
     *
     * @param  string  $token
     * @return bool
     */
    public static function isValid($token, $user_id = null)
    {
        $query = self::query()->isEnabled()->where('token_hash', $token);

        if (! is_null($user_id)) {
            $query->where('user_id', $user_id);
        }

        return $query->exists();
    }

    /**
     * Get User model based on the given API token (or null if invalid)
     *
     * @param  string  $token
     * @return User|null
     */
    public static function userFromToken($token)
    {
        return User::find(self::idFromToken($token));
    }

    public static function generateToken(User $user, $description = '')
    {
        $token = new static;
        $token->user_id = $user->user_id;
        $token->token_hash = $bytes = bin2hex(random_bytes(16));
        $token->description = $description;
        $token->disabled = false;
        $token->save();

        return $token;
    }

    /**
     * Get the user_id for the given token.
     *
     * @param  string  $token
     * @return int
     */
    public static function idFromToken($token)
    {
        return self::query()->isEnabled()->where('token_hash', $token)->value('user_id');
    }

    // ---- Query scopes ----

    public function scopeIsEnabled($query)
    {
        return $query->where('disabled', 0);
    }

    // ---- Define Relationships ----
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
