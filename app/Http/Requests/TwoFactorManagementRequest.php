<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TwoFactorManagementRequest extends FormRequest
{
    public function rules(): array
    {
        return [];
    }

    public function authorize(): bool
    {
        $user = $this->route()->parameter('user');
        $auth_user = auth()->user();

        // don't allow admins to bypass security for themselves
        return $auth_user->isAdmin() && ! $auth_user->is($user);
    }
}
