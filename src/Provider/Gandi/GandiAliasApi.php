<?php

namespace App\Provider\Gandi;

use App\Exception\ApiCallException;
use App\Service\AliasApiInterface;
use App\Service\EmailTools;
use DateInterval;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
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

    private FilesystemAdapter $cache;

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
        $this->cache = new FilesystemAdapter('gandi');
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
            throw new ApiCallException(
                "Gandi",
                sprintf("%s : %s with options: %s", $method, $url, json_encode($options)),
                $exception->getMessage());
        }

        return $content;
    }

    /**
     * @return array
     * @throws ApiCallException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getDomains()
    {
        $item = $this->cache->getItem('domains');
        if ($item->isHit()) {
            return $item->get();
        }

        $domains = [];
        foreach ($this->request('GET', sprintf('%s/domain/domains', self::BASE_URL)) as $domain) {
            $domains[] = $domain['fqdn_unicode'];
        }

        // set user domains in cache
        $item->set($domains);
        $item->expiresAfter(DateInterval::createFromDateString('1 hour'));
        $this->cache->save($item);

        return $domains;
    }

    /**
     * @param string $domain
     * @return array
     * @throws ApiCallException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getMailboxes(string $domain): array
    {
        $item = $this->cache->getItem("mailboxes.$domain");
        if ($item->isHit()) {
            return $item->get();
        }

        $mailboxes = $this->request('GET', sprintf("%s/email/mailboxes/%s", self::BASE_URL, $domain));

        // Set domain mailboxes in cache
        $item->set($mailboxes);
        $item->expiresAfter(DateInterval::createFromDateString('1 hour'));
        $this->cache->save($item);

        return $mailboxes;
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
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getEmails(): array
    {
        $emails = [];
        foreach ($this->getDomains() as $domain) {
            foreach ($this->getMailboxes($domain) as $mailbox) {
                $emails[] = $mailbox['address'];
            }
        }

        return $emails;
    }

    /**
     * @param string $email
     * @return array|null
     * @throws ApiCallException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getMailboxDetails(string $email): ?array
    {
        $item = $this->cache->getItem(sprintf("mailbox.%s", md5($email)));
        if ($item->isHit()) {
            return $item->get();
        }

        $domain = EmailTools::getDomain($email);
        foreach ($this->getMailboxes($domain) as $mailbox) {
            if ($mailbox['address'] === $email) {

                $mailbox = $this->request(
                    'GET',
                    sprintf("%s/email/mailboxes/%s/%s", self::BASE_URL, $domain, $mailbox['id'])
                );

                // Set email mailbox in cache
                $item->set($mailbox);
                $item->expiresAfter(DateInterval::createFromDateString('1 hour'));
                $this->cache->save($item);

                return $mailbox;
            }
        }

        return NULL;
    }

    /**
     * @param string $email
     * @return array
     * @throws ApiCallException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAlias(string $email): array
    {
        $mailbox = $this->getMailboxDetails($email);

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
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addAlias(string $email, string $alias): bool
    {
        $mailbox = $this->getMailboxDetails($email);
        $this->cache->delete(sprintf("mailbox.%s", md5($email)));
        $aliases = $mailbox['aliases'];

        if (!array_search(EmailTools::withoutDomain($alias), $aliases)) {
            $aliases[] = EmailTools::withoutDomain($alias);
            $this->updateMailboxAlias(array_values($aliases), $mailbox);
        }

        return true;
    }

    /**
     * @param string $email
     * @param string $alias
     * @return bool
     * @throws ApiCallException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteAlias(string $email, string $alias): bool
    {
        $mailbox = $this->getMailboxDetails($email);
        $this->cache->delete(sprintf("mailbox.%s", md5($email)));
        $aliases = $mailbox['aliases'];

        if (($key = array_search(EmailTools::withoutDomain($alias), $aliases)) !== false) {
            unset($aliases[$key]);
            $this->updateMailboxAlias(array_values($aliases), $mailbox);
        }

        return true;
    }
}