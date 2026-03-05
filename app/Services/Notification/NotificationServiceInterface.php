<?php

namespace App\Services\Notification;

use App\Models\User;
use Illuminate\Notifications\Notification;

interface NotificationServiceInterface
{
    public function send(User $user, Notification $notification): void;
}
