<?php

namespace App\Service;

interface AliasApiInterface
{

    public function getEmails(): array;

    public function getAlias(string $email): array;

    public function addAlias(string $email, string $alias): void;

    public function deleteAlias(string $email, string $alias): void;

}
