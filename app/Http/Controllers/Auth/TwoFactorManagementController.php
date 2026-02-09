<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwoFactorManagementRequest;
use App\Models\User;
use App\Models\UserPref;

class TwoFactorManagementController extends Controller
{
    public function unlock(TwoFactorManagementRequest $request, User $user)
    {
        $twofactor = UserPref::getPref($user, 'twofactor');
        $twofactor['fails'] = 0;

        if (UserPref::setPref($user, 'twofactor', $twofactor)) {
            return response()->json(['status' => 'ok', 'msg' => __('Two-Factor unlocked.')]);
        }

        return response()->json(['error' => __('Failed to unlock Two-Factor.')]);
    }

    public function destroy(TwoFactorManagementRequest $request, User $user)
    {
        if (UserPref::forgetPref($user, 'twofactor')) {
            return response()->json(['msg' => __('Two-Factor removed.')]);
        }

        return response()->json(['error' => __('Failed to remove Two-Factor.')]);
    }
}
