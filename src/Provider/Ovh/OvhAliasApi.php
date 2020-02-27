<?php

namespace App\Provider\Ovh;

use App\Service\AliasApiInterface;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use Ovh\Api;
use Psr\Log\LoggerInterface;

class OvhAliasApi implements AliasApiInterface
{

    private $baseUrl;

    private $api;

    private $apiLogger;

    /**
     * OvhAliasApi constructor.
     *
     * @param \Psr\Log\LoggerInterface $apiLogger
     *
     * @throws \Ovh\Exceptions\InvalidParameterException
     */
    public function __construct(LoggerInterface $apiLogger)
    {
        // check environment variables is defined
        foreach (['OVH_KEY', 'OVH_SECRET', 'OVH_CONSUMER', 'OVH_ENDPOINT', 'OVH_SERVICE'] as $key) {
            if (!isset($_ENV[$key]) || is_null($_ENV[$key])) {
                throw new InvalidArgumentException("Environment variable $key must be defined to inject this service.");
            }
        }

        $this->baseUrl = '/email/pro/' . $_ENV['OVH_SERVICE'];
        $this->api = new Api($_ENV['OVH_KEY'], $_ENV['OVH_SECRET'], $_ENV['OVH_ENDPOINT'], $_ENV['OVH_CONSUMER']);
        $this->apiLogger = $apiLogger;
    }

    public function getEmails(): array
    {
        $endpoint = "{$this->baseUrl}/account";
        $this->apiLogger->debug("GET $endpoint");

        return $this->api->get($endpoint);
    }

    /**
     * @param $email
     *
     * @return array
     */
    public function getAlias(string $email): array
    {
        $endpoint = "{$this->baseUrl}/account/$email/alias";
        $this->apiLogger->debug("GET $endpoint");

        return $this->api->get($endpoint);
    }

    /**
     * @param $email
     * @param $alias
     *
     * @return bool
     */
    public function addAlias(string $email, string $alias): bool
    {
        $endpoint = "{$this->baseUrl}/account/$email/alias";
        $this->apiLogger->debug("POST $endpoint with alias $alias");

        try {
            $this->api->post($endpoint, ['alias' => $alias]);
        } catch (ClientException $exception) {
            $this->apiLogger->error($exception->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @param $email
     * @param $alias
     *
     * @return bool
     */
    public function deleteAlias(string $email, string $alias): bool
    {
        $endpoint = "{$this->baseUrl}/account/$email/alias/$alias";
        $this->apiLogger->debug("DELETE $endpoint with alias $alias");

        try {
            $this->api->delete($endpoint, ['alias' => $alias]);
        } catch (ClientException $exception) {
            $this->apiLogger->error($exception->getMessage());
            return false;
        }
        return true;
    }
}
