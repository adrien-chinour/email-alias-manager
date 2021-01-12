<?php

namespace App\Service;

use App\Entity\Alias;
use App\Provider\AliasApiInterface;
use App\Repository\AliasRepository;

class AliasService
{
    private AliasRepository $repository;

    private AliasApiInterface $api;

    private ?int $counter = null;

    public function __construct(AliasRepository $repository, AliasApiInterface $api)
    {
        $this->repository = $repository;
        $this->api = $api;
    }

    public function add(Alias $alias)
    {
        $this->repository->save($alias);
        $this->api->addAlias($alias->getRealEmail(), $alias->getAliasEmail());
        $this->counter++;
    }

    public function delete(Alias $alias)
    {
        $this->api->deleteAlias($alias->getRealEmail(), $alias->getAliasEmail());
        $this->repository->delete($alias);
        $this->counter--;
    }

    public function countAliases(): ?int
    {
        if (null === $this->counter) {
            dump("init");
            $this->counter = $this->repository->count([]);
        }

        return $this->counter;
    }
}
