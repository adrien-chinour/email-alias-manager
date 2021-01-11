<?php

namespace App\Mercure;

use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class MercureNotifier
{
    private PublisherInterface $publisher;

    public function __construct(PublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    public function send(Notification $notification)
    {
        ($this->publisher)(new Update('notifications', json_encode($notification)));
    }
}
