<?php

namespace App\Provider\Ovh;

use App\Service\AliasApiInterface;
use GuzzleHttp\Exception\ClientException;
use InvalidArgumentException;
use Ovh\Api;

class OvhAliasApi implements AliasApiInterface
{
    private string $baseUrl;

    private Api $api;

    public function __construct()
    {
        // check environment variables is defined
        foreach (['OVH_KEY', 'OVH_SECRET', 'OVH_CONSUMER', 'OVH_ENDPOINT', 'OVH_SERVICE'] as $key) {
            if (!isset($_ENV[$key]) || is_null($_ENV[$key])) {
                throw new InvalidArgumentException("Environment variable $key must be defined to inject this service.");
            }
        }

        $this->baseUrl = '/email/pro/'.$_ENV['OVH_SERVICE'];
        $this->api = new Api($_ENV['OVH_KEY'], $_ENV['OVH_SECRET'], $_ENV['OVH_ENDPOINT'], $_ENV['OVH_CONSUMER']);
    }

    public function getEmails(): array
    {
        return $this->api->get("{$this->baseUrl}/account");
    }

    public function getAlias(string $email): array
    {
        return $this->api->get("{$this->baseUrl}/account/$email/alias");
    }

    public function addAlias(string $email, string $alias): bool
    {
        try {
            $this->api->post("{$this->baseUrl}/account/$email/alias", ['alias' => $alias]);
        } catch (ClientException $exception) {
            return false;
        }

        return true;
    }

    public function deleteAlias(string $email, string $alias): bool
    {
        try {
            $this->api->delete("{$this->baseUrl}/account/$email/alias/$alias", ['alias' => $alias]);
        } catch (ClientException $exception) {
            return false;
        }

        return true;
    }
}
