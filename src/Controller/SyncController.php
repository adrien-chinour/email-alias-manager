<?php

namespace App\Controller;

use App\Mercure\MercureNotifier;
use App\Mercure\Notification;
use App\Messenger\Message\AliasChangeMessage;
use App\Messenger\Message\AliasSyncingMessage;
use App\Repository\AliasDiffRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sync")
 */
final class SyncController extends AbstractController
{
    private AliasDiffRepository $repository;

    public function __construct(AliasDiffRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/", name="sync_index")
     */
    public function index(): Response
    {
        return $this->render('sync/index.html.twig', ['diff' => $this->repository->findAll()]);
    }

    /**
     * @Route("/change", name="sync_change")
     */
    public function change(MessageBusInterface $bus, MercureNotifier $notifier): Response
    {
        $notifier->send(new Notification('warning', 'Send your message to worker'));
        $bus->dispatch(new AliasChangeMessage(), [new DelayStamp(5000),]);

        return new Response("ok");
    }

    /**
     * @Route("/syncing", name="sync_syncing")
     */
    public function sync(MessageBusInterface $bus, MercureNotifier $notifier): Response
    {
        $notifier->send(new Notification('warning', 'Send your message to worker'));
        $bus->dispatch(new AliasSyncingMessage());

        return new Response("ok");
    }
}
