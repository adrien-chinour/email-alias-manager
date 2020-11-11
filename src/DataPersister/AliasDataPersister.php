<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Alias;
use App\Service\AliasApiInterface;

final class AliasDataPersister implements ContextAwareDataPersisterInterface
{

    private ContextAwareDataPersisterInterface $decorated;

    private AliasApiInterface $api;

    public function __construct(ContextAwareDataPersisterInterface $decorated, AliasApiInterface $api)
    {
        $this->decorated = $decorated;
        $this->api = $api;
    }

    /**
     * @inheritDoc
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Alias;
    }

    /**
     * @param Alias $data
     * @param array $context
     *
     * @return object|void
     */
    public function persist($data, array $context = [])
    {
        $result = $this->decorated->persist($data, $context);

        if (($context['collection_operation_name'] ?? null) === 'post') {
            $this->api->addAlias($data->getRealEmail(), $data->getAliasEmail());
        }

        return $result;
    }

    /**
     * @param Alias $data
     * @param array $context
     *
     * @return object|null
     */
    public function remove($data, array $context = [])
    {
        $result = $this->decorated->remove($data, $context);

        if (($context['collection_operation_name'] ?? null) === 'delete') {
            $this->api->deleteAlias($data->getRealEmail(), $data->getAliasEmail());
        }

        return $result;
    }
}
