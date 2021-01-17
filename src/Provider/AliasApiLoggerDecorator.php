<?php

namespace App\Provider;

use Psr\Log\LoggerInterface;

class AliasApiLoggerDecorator implements AliasApiInterface
{
    private AliasApiInterface $api;

    private LoggerInterface $apiLogger;

    private $className;

    public function __construct(AliasApiInterface $api, LoggerInterface $apiLogger)
    {
        $this->api = $api;
        $this->apiLogger = $apiLogger;
        $this->className = get_class($this->api);
    }

    /**
     * @throws \Throwable
     */
    public function getEmails(): array
    {
        try {
            $emails = $this->api->getEmails();
            $this->apiLogger->debug(
                sprintf('Call %s::getEmails() return %s', $this->className, json_encode($emails))
            );
        } catch (\Throwable $exception) {
            $this->apiLogger->error(
                sprintf('Call %s::getEmails() return error : %s', $this->className, $exception->getMessage())
            );
            throw $exception;
        }

        return $emails;
    }

    /**
     * @throws \Throwable
     */
    public function getAlias(string $email): array
    {
        try {
            $aliases = $this->api->getAlias($email);
            $this->apiLogger->debug(
                sprintf('Call %s::getAlias(%s) return %s', $this->className, $email, json_encode($aliases))
            );
        } catch (\Throwable $exception) {
            $this->apiLogger->error(
                sprintf('Call %s::getAlias(%s) return error : %s', $this->className, $email, $exception->getMessage())
            );
            throw $exception;
        }

        return $aliases;
    }

    /**
     * @throws \Throwable
     */
    public function addAlias(string $email, string $alias): bool
    {
        try {
            $bool = $this->api->addAlias($email, $alias);
            $this->apiLogger->debug(
                sprintf('Call %s::addAlias(%s,%s) return %s', $this->className, $email, $alias, $bool)
            );
        } catch (\Throwable $exception) {
            $this->apiLogger->error(
                sprintf('Call %s::addAlias(%s,%s) return error : %s', $this->className, $email, $alias, $exception->getMessage())
            );
            throw $exception;
        }

        return $bool;
    }

    /**
     * @throws \Throwable
     */
    public function deleteAlias(string $email, string $alias): bool
    {
        try {
            $bool = $this->api->deleteAlias($email, $alias);
            $this->apiLogger->debug(
                sprintf('Call %s::deleteAlias(%s,%s) return %s', $this->className, $email, $alias, $bool)
            );
        } catch (\Throwable $exception) {
            $this->apiLogger->error(
                sprintf('Call %s::deleteAlias(%s,%s) return error : %s', $this->className, $email, $alias, $exception->getMessage())
            );
            throw $exception;
        }

        return $bool;
    }
}
