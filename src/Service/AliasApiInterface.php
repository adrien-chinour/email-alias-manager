<?php

namespace App\Service;

interface AliasApiInterface
{

    /**
     * @return array
     */
    public function getEmails(): array;

    /**
     * @param string $email
     *
     * @return array
     */
    public function getAlias(string $email): array;

    /**
     * @param string $email
     * @param string $alias
     *
     * @return bool
     */
    public function addAlias(string $email, string $alias): bool;

    /**
     * @param string $email
     * @param string $alias
     *
     * @return bool
     */
    public function deleteAlias(string $email, string $alias): bool;
}
