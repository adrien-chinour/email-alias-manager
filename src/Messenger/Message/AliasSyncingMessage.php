<?php

namespace App\Messenger\Message;

use App\Mercure\Notification;

class AliasSyncingMessage implements WithNotificationInterface
{
    public function onReceived(): ?Notification
    {
        return new Notification('info', 'Syncing aliases with provider');
    }

    public function onHandled(): ?Notification
    {
        return new Notification('success', 'Finish to synchronize aliases with provider');
    }
}
