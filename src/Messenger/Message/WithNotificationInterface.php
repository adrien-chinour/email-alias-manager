<?php

namespace App\Messenger\Message;

use App\Mercure\Notification;

interface WithNotificationInterface
{
    public function onReceived(): ?Notification;

    public function onHandled(): ?Notification;
}
