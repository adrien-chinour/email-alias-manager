<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use App\Entity\Email;
use App\Service\AliasApiInterface;

final class EmailDataPersister implements ContextAwareDataPersisterInterface
{

    private $decorated;

    private $api;

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
        return $data instanceof Email;
    }

    /**
     * @param Email $data
     * @param array $context
     *
     * @return object|void
     */
    public function persist($data, array $context = [])
    {
        $result = $this->decorated->persist($data, $context);

        if (($context['collection_operation_name'] ?? null) === 'post') {
            $this->api->addAlias($data->getTarget(), $data->getAlias());
        }

        return $result;
    }

    /**
     * @param Email $data
     * @param array $context
     *
     * @return object|null
     */
    public function remove($data, array $context = [])
    {
        $result = $this->decorated->remove($data, $context);

        if (($context['collection_operation_name'] ?? null) === 'delete') {
            $this->api->deleteAlias($data->getTarget(), $data->getAlias());
        }

        return $result;
    }
}
