<?php

namespace App\EventSubscriber;

use App\Mercure\MercureNotifier;
use App\Mercure\Notification;
use App\Messenger\Message\WithNotificationInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;

class MessengerEventSubscriber implements EventSubscriberInterface
{
    private MercureNotifier $notification;

    public function __construct(MercureNotifier $notification)
    {
        $this->notification = $notification;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageHandledEvent::class => 'onHandledMessage',
            WorkerMessageReceivedEvent::class => 'onReceivedMessage',
            WorkerStartedEvent::class => 'onWorkerStarted',
            WorkerStoppedEvent::class => 'onWorkerStopped'
        ];
    }

    public function onHandledMessage(WorkerMessageHandledEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if ($message instanceof WithNotificationInterface) {
            null === $message->onHandled() ?: $this->notification->send($message->onHandled());
        }
    }

    public function onReceivedMessage(WorkerMessageReceivedEvent $event)
    {
        $message = $event->getEnvelope()->getMessage();
        if ($message instanceof WithNotificationInterface) {
            null === $message->onReceived() ?: $this->notification->send($message->onReceived());
        }
    }

    public function onWorkerStarted()
    {
        $this->notification->send(new Notification('success', 'Worker has been started'));
    }

    public function onWorkerStopped()
    {
        $this->notification->send(new Notification('danger', 'Worker has been stopped'));
    }
}
