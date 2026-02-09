<?php
namespace ObzoraNMS\Alert\Transport;

use App\Models\User;
use App\Notifications\AlertNotification;
use ObzoraNMS\Alert\Transport;
use Notification;

class Browserpush extends Transport
{
    protected string $name = 'Browser Push';

    public function deliverAlert(array $alert_data): bool
    {
        $users = User::when($this->config['user'] ?? 0, function ($query, $user_id) {
            return $query->where('user_id', $user_id);
        })->get();

        Notification::send($users, new AlertNotification(
            $alert_data['alert_id'],
            $alert_data['title'],
            $alert_data['msg'],
        ));

        return true;
    }

    public static function configTemplate(): array
    {
        $users = [__('All Users') => 0];
        foreach (User::get(['user_id', 'username', 'realname']) as $user) {
            $users[htmlentities($user->realname ?: $user->username)] = $user->user_id;
        }

        return [
            'config' => [
                [
                    'title' => 'User',
                    'name' => 'user',
                    'descr' => 'ObzoraNMS User',
                    'type' => 'select',
                    'options' => $users,
                ],
            ],
            'validation' => [
                'user' => 'required|zero_or_exists:users,user_id',
            ],
        ];
    }

    public function displayDetails(): string
    {
        if ($this->config['user'] == 0) {
            $count = \DB::table('push_subscriptions')->count();

            return "All users: $count subscriptions";
        } elseif ($user = User::find($this->config['user'])) {
            $count = $user->pushSubscriptions()->count();

            return "User: $user->username ($count subscriptions)";
        }

        return 'User not found';
    }
}
