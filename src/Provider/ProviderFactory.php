<?php

namespace App\Provider;

use App\Provider\Fake\FakeAliasApi;
use App\Provider\Gandi\GandiAliasApi;
use App\Provider\Ovh\OvhAliasApi;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProviderFactory
{
    private HttpClientInterface $httpClient;

    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function __invoke(): AliasApiInterface
    {
        if (!isset($_ENV['PROVIDER_NAME'])) {
            throw new \InvalidArgumentException('You must define env variable "PROVIDER_NAME".');
        }

        switch ($_ENV['PROVIDER_NAME']) {
            case 'gandi':
                return new GandiAliasApi($this->logger, $this->httpClient);
            case 'ovh':
                return new OvhAliasApi();
            default:
                return new FakeAliasApi();
        }
    }

}
