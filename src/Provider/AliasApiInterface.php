<?php

namespace App\Provider;

interface AliasApiInterface
{
    public function getEmails(): array;

    public function getAlias(string $email): array;

    public function addAlias(string $email, string $alias): bool;

    public function deleteAlias(string $email, string $alias): bool;
}
