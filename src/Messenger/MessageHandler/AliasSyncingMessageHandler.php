<?php

namespace App\Messenger\MessageHandler;

use App\Entity\AliasDiff;
use App\Messenger\Message\AliasSyncingMessage;
use App\Provider\AliasApiInterface;
use App\Repository\AliasDiffRepository;
use App\Repository\AliasRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AliasSyncingMessageHandler implements MessageHandlerInterface
{
    private AliasDiffRepository $diffRepository;

    private AliasRepository $aliasRepository;

    private AliasApiInterface $api;

    public function __construct(AliasDiffRepository $diffRepository, AliasRepository $aliasRepository, AliasApiInterface $api)
    {
        $this->diffRepository = $diffRepository;
        $this->aliasRepository = $aliasRepository;
        $this->api = $api;
    }

    public function __invoke(AliasSyncingMessage $message)
    {
        foreach ($this->diffRepository->findAll() as $diff) {
            $diff->isDistant() ? $this->addAlias($diff) : $this->api->addAlias($diff->getEmail(), $diff->getAlias());
            $this->diffRepository->delete($diff);
        }
    }

    protected function addAlias(AliasDiff $diff)
    {
        if (null === $this->aliasRepository->findOneBy(['aliasEmail' => $diff->getAlias()])) {
            $this->aliasRepository->save($diff->toAlias());
        }
    }
}
