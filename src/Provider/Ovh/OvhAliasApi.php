<?php

namespace App\Provider\Ovh;

use App\Service\AliasApiInterface;
use Ovh\Api;

class OvhAliasApi implements AliasApiInterface
{

    private $baseUrl;

    private $api;

    /**
     * OvhAliasApi constructor.
     *
     * @throws \Ovh\Exceptions\InvalidParameterException
     */
    public function __construct()
    {
        // check environment variables is defined
        foreach (['OVH_KEY', 'OVH_SECRET', 'OVH_CONSUMER', 'OVH_ENDPOINT', 'OVH_SERVICE'] as $key) {
            if (!isset($_ENV[$key]) || is_null($_ENV[$key])) {
                throw new \InvalidArgumentException("Environment variable $key must be defined to inject this service.");
            }
        }

        $this->baseUrl = '/email/pro/' . $_ENV['OVH_SERVICE'];
        $this->api = new Api($_ENV['OVH_KEY'], $_ENV['OVH_SECRET'], $_ENV['OVH_ENDPOINT'], $_ENV['OVH_CONSUMER']);
    }

    public function getEmails(): array
    {
        return $this->api->get("{$this->baseUrl}/account");
    }

    /**
     * @param $email
     *
     * @return array
     */
    public function getAlias(string $email): array
    {
        return $this->api->get("{$this->baseUrl}/account/$email/alias");
    }

    /**
     * @param $email
     * @param $alias
     */
    public function addAlias(string $email, string $alias): void
    {
        $this->api->post("{$this->baseUrl}/account/$email/alias",
            ['alias' => $alias]);
    }

    /**
     * @param $email
     * @param $alias
     */
    public function deleteAlias(string $email, string $alias): void
    {
        $this->api->delete("{$this->baseUrl}/account/$email/alias/$alias",
            ['alias' => $alias]);
    }
}
