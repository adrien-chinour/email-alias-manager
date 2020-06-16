<?php

namespace App\Provider\Gandi;

use App\Exception\ApiCallException;
use App\Service\AliasApiInterface;
use App\Service\EmailTools;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

class GandiAliasApi implements AliasApiInterface
{
    private const ENV_API_KEY = 'GANDI_API_KEY';

    private const BASE_URL = 'https://api.gandi.net/v5';

    private LoggerInterface $apiLoger;

    private HttpClientInterface $httpClient;

    private string $apiKey;

    public function __construct(LoggerInterface $logger, HttpClientInterface $httpClient)
    {
        if (!isset($_ENV[self::ENV_API_KEY])) {
            throw new InvalidArgumentException(
                sprintf("You must provide an api key in your env variables with %s", self::ENV_API_KEY)
            );
        }

        $this->apiKey = $_ENV[self::ENV_API_KEY];
        $this->apiLoger = $logger;
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $options
     * @return array
     * @throws ApiCallException
     */
    private function request(string $method, string $url, array $options = []): array
    {
        $options = array_merge_recursive($options, [
            'headers' => [
                'Authorization' => 'ApiKey ' . $this->apiKey
            ]
        ]);

        try {
            $response = $this->httpClient->request($method, $url, $options);
            if ($response->getStatusCode() > 299) {
                throw new HttpException($response->getStatusCode(), $response->getContent());
            }
            $content = json_decode($response->getContent(), true);
        } catch (Throwable $exception) {
            throw new ApiCallException("Gandi", "{$method} : {$url}", $exception->getMessage());
        }
        return $content;
    }

    /**
     * @return array
     * @throws ApiCallException
     */
    private function getDomains()
    {
        // TODO CACHE
        $domains = [];
        foreach ($this->request('GET', self::BASE_URL . '/domain/domains') as $domain) {
            $domains[] = $domain['fqdn_unicode'] ?? NULL;
        }

        return array_filter($domains); // remove null domains
    }

    /**
     * @param string $domain
     * @return array
     * @throws ApiCallException
     */
    private function getMailboxes(string $domain): array
    {
        // TODO CACHE
        return $this->request('GET', sprintf("%s/email/mailboxes/%s", self::BASE_URL, $domain));
    }

    /**
     * @param array $aliases
     * @param array $mailbox
     * @throws ApiCallException
     */
    private function updateMailboxAlias(array $aliases, array $mailbox): void
    {
        $this->request(
            'PATCH',
            sprintf("%s/email/mailboxes/%s/%s", self::BASE_URL, $mailbox['domain'], $mailbox['id']),
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(["aliases" => $aliases])
            ]
        );
    }

    /**
     * @return array
     * @throws ApiCallException
     */
    public function getEmails(): array
    {
        $emails = [];
        foreach ($this->getDomains() as $domain) {
            foreach ($this->getMailboxes($domain) as $mailbox) {
                $emails[] = $mailbox['address'] ?? NULL;
            }
        }

        return $emails;
    }

    /**
     * @param string $email
     * @return array|null
     * @throws ApiCallException
     */
    private function getMailbox(string $email): ?array
    {
        $domain = EmailTools::getDomain($email);

        foreach ($this->getMailboxes($domain) as $mailbox) {
            if ($mailbox['address'] === $email) {
                return $this->request(
                    'GET',
                    sprintf("%s/email/mailboxes/%s/%s", self::BASE_URL, $domain, $mailbox['id'])
                );
            }
        }

        return NULL;
    }

    /**
     * @param string $email
     * @return array
     * @throws ApiCallException
     */
    public function getAlias(string $email): array
    {
        $mailbox = $this->getMailbox($email);

        $aliases = [];
        foreach ($mailbox['aliases'] as $alias) {
            $aliases[] = $alias . '@' . EmailTools::getDomain($email);
        }

        return $aliases;
    }

    /**
     * @param string $email
     * @param string $alias
     * @return bool
     * @throws ApiCallException
     */
    public function addAlias(string $email, string $alias): bool
    {
        $mailbox = $this->getMailbox($email);
        $aliases = $mailbox['aliases'];

        if (!array_search(EmailTools::withoutDomain($alias), $aliases)) {
            $aliases[] = EmailTools::withoutDomain($alias);
            $this->updateMailboxAlias($aliases, $mailbox);
        }

        return true;
    }

    /**
     * @param string $email
     * @param string $alias
     * @return bool
     * @throws ApiCallException
     */
    public function deleteAlias(string $email, string $alias): bool
    {
        $mailbox = $this->getMailbox($email);
        $aliases = $mailbox['aliases'];

        if (($key = array_search($alias, $aliases)) !== false) {
            unset($aliases[$key]);
            $this->updateMailboxAlias($aliases, $mailbox);
        }


        return true;
    }
}