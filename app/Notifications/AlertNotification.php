<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class AlertNotification extends Notification
{
    /**
     * @var WebPushMessage
     */
    public $message;

    public function __construct(int $alert_id, string $title, string $body)
    {
        $this->message = (new WebPushMessage)
            ->title($title)
            ->icon(asset('/images/mstile-144x144.png'))
            ->body(substr($body, 0, 3500))
            ->action('Acknowledge', 'alert.acknowledge')
            ->action('View', 'alert.view')
            ->options(['TTL' => 2000])
            ->data(['id' => $alert_id]);
        // ->badge()
        // ->dir()
        // ->image()
        // ->lang()
        // ->renotify()
        // ->requireInteraction()
        // ->tag()
        // ->vibrate()
    }

    /**
     * @param  mixed  $notifiable
     * @return string[]
     */
    public function via($notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     * @return WebPushMessage
     */
    public function toWebPush($notifiable, Notification $notification): WebPushMessage
    {
        return $this->message;
    }
}
