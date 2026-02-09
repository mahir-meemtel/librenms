<?php
namespace App\Http\Interfaces;

use Illuminate\Session\SessionManager;

class ToastInterface
{
    public function __construct(
        private SessionManager $session
    ) {
    }

    public function info(string $title, ?string $message = null, ?array $options = null): static
    {
        return $this->message('info', $title, $message, $options);
    }

    public function success(string $title, ?string $message = null, ?array $options = null): static
    {
        return $this->message('success', $title, $message, $options);
    }

    public function error(string $title, ?string $message = null, ?array $options = null): static
    {
        return $this->message('error', $title, $message, $options);
    }

    public function warning(string $title, ?string $message = null, ?array $options = null): static
    {
        return $this->message('warning', $title, $message, $options);
    }

    public function message(string $level, string $title, ?string $message = null, ?array $options = null): static
    {
        $notifications = $this->session->get('toasts', []);
        array_push($notifications, [
            'level' => $level,
            'title' => $message === null ? '' : $title,
            'message' => $message ?? $title,
            'options' => $options ?? [],
        ]);
        $this->session->flash('toasts', $notifications);

        return $this;
    }
}
