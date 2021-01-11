<?php

namespace App\Messenger\Message;

use App\Mercure\Notification;

class AliasChangeMessage implements WithNotificationInterface
{
    public function onReceived(): ?Notification
    {
        return new Notification('info', 'Searching differences between provider and local aliases');
    }

    public function onHandled(): ?Notification
    {
        return new Notification('success', 'Finish to search differences between provider and local aliases');
    }
}
