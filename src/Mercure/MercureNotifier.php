<?php

namespace App\Mercure;

use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Contracts\Translation\TranslatorInterface;

class MercureNotifier
{
    private PublisherInterface $publisher;

    private TranslatorInterface $translator;

    public function __construct(PublisherInterface $publisher, TranslatorInterface $translator)
    {
        $this->publisher = $publisher;
        $this->translator = $translator;
    }

    public function send(Notification $notification)
    {
        $notification = $this->translateNotification($notification);
        ($this->publisher)(new Update('notifications', json_encode($notification)));
    }

    protected function translateNotification(Notification $notification): Notification
    {
        return new Notification($notification->type(), $this->translator->trans($notification->message()));
    }
}
