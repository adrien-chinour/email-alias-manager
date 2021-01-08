<?php

namespace App\Provider\Fake;

use App\Provider\Fake\Models\Alias;
use App\Provider\Fake\Models\Email;
use App\Provider\AliasApiInterface;

class FakeAliasApi implements AliasApiInterface
{
    private FilesystemStorage $storage;

    public function __construct()
    {
        $this->storage = new FilesystemStorage();
    }

    public function getEmails(): array
    {
        return array_map(function (Email $item) {
            return $item->email;
        }, $this->storage->readEmails());
    }

    public function getAlias(string $email): array
    {
        $aliases = array_filter($this->storage->readAliases(), function (Alias $item) use ($email) {
            return $item->email === $email;
        });

        return array_map(function (Alias $item) {
            return $item->alias;
        }, $aliases);
    }

    public function addAlias(string $email, string $alias): bool
    {
        $aliases = $this->storage->readAliases();
        $aliases[] = new Alias($email, $alias);
        $this->storage->writeAliases($aliases);

        return true;
    }

    public function deleteAlias(string $email, string $alias): bool
    {
        $aliases = array_filter($this->storage->readAliases(), function (Alias $item) use ($email, $alias) {
            return $item->email !== $email && $item->alias !== $alias;
        });
        $this->storage->writeAliases($aliases);

        return true;
    }
}
