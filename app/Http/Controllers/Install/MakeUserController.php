<?php
namespace App\Http\Controllers\Install;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use ObzoraNMS\Interfaces\InstallerStep;

class MakeUserController extends InstallationController implements InstallerStep
{
    protected $step = 'user';

    public function index(Request $request)
    {
        if (! $this->initInstallStep()) {
            return $this->redirectToIncomplete();
        }

        if (session('install.database')) {
            $user = User::adminOnly()->first();
        }

        if (isset($user)) {
            $this->markStepComplete();

            return view('install.user-created', $this->formatData([
                'user' => $user,
            ]));
        }

        return view('install.make-user', $this->formatData([
            'messages' => Arr::wrap(session('message')),
        ]));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $message = trans('install.user.failure');

        try {
            // only allow the first admin to be created
            if (! $this->complete()) {
                $this->configureDatabase();
                $user = new User($request->only(['username', 'password', 'email']));
                $user->setPassword($request->get('password'));
                $res = $user->save();

                $user->assignRole('admin');

                if ($res) {
                    $message = trans('install.user.success');
                    $this->markStepComplete();
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return redirect()->back()->with('message', $message);
    }

    public function complete(): bool
    {
        if ($this->stepCompleted('user')) {
            return true;
        }

        try {
            if ($this->stepCompleted('database')) {
                $exists = User::adminOnly()->exists();
                if ($exists) {
                    $this->markStepComplete();
                }

                return $exists;
            }
        } catch (QueryException $e) {
            //
        }

        return false;
    }

    public function enabled(): bool
    {
        return $this->stepCompleted('database');
    }

    public function icon(): string
    {
        return 'fa-solid fa-key';
    }
}
