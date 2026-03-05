<?php

namespace App\Services\Notification;

use App\Models\User;
use App\Services\Logger\LoggerServiceInterface;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class NotificationService implements NotificationServiceInterface
{
    public function __construct(
        private LoggerServiceInterface $logger
    ) {}

    public function send(User $user, Notification $notification): void
    {
        $this->logger->info('Dispatching notification ['.class_basename($notification).'] to user queue', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        NotificationFacade::send($user, $notification);
    }
}
