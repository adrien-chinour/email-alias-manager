<?php

namespace App\Messenger\MessageHandler;

use App\Entity\Alias;
use App\Entity\AliasDiff;
use App\Messenger\Message\AliasChangeMessage;
use App\Provider\AliasApiInterface;
use App\Repository\AliasDiffRepository;
use App\Repository\AliasRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AliasChangeMessageHandler implements MessageHandlerInterface
{
    private AliasApiInterface $api;

    private AliasRepository $aliasRepository;

    private AliasDiffRepository $diffRepository;

    public function __construct(AliasApiInterface $api, AliasRepository $aliasRepository, AliasDiffRepository $diffRepository)
    {
        $this->api = $api;
        $this->aliasRepository = $aliasRepository;
        $this->diffRepository = $diffRepository;
    }

    public function __invoke(AliasChangeMessage $message)
    {
        $this->diffRepository->reset();
        foreach ($this->api->getEmails() as $email) {

            $local = array_map(function (Alias $alias) {
                return $alias->getAliasEmail();
            }, $this->aliasRepository->getAlias($email));

            $distant = $this->api->getAlias($email);

            foreach (array_diff($distant, $local) as $alias) {
                $diff = (new AliasDiff())
                    ->setAlias($alias)
                    ->setEmail($email)
                    ->setExistOn(AliasDiff::IS_DISTANT);
                $this->diffRepository->save($diff);
            }

            foreach (array_diff($local, $distant) as $alias) {
                $diff = (new AliasDiff())
                    ->setAlias($alias)
                    ->setEmail($email)
                    ->setExistOn(AliasDiff::IS_LOCAL);
                $this->diffRepository->save($diff);
            }
        }
    }
}
