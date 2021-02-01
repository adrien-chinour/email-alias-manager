<?php

namespace App\Mercure;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class MercureJwtProvider
{
    private string $mercureSecretKey;

    public function __construct(string $mercureSecretKey)
    {
        $this->mercureSecretKey = $mercureSecretKey;
    }

    public function __invoke(): string
    {
        $key = InMemory::plainText($this->mercureSecretKey);
        $configuration = Configuration::forSymmetricSigner(new Sha256(), $key);

        return $configuration->builder()
            ->withClaim('mercure', ['publish' => ['*']])
            ->getToken($configuration->signer(), $configuration->signingKey())
            ->toString();
    }
}
