<?php

namespace App\Twig;

use App\Service\AliasService;

class AliasCounterVariable
{
    private AliasService $aliasService;

    public function __construct(AliasService $aliasService)
    {
        $this->aliasService = $aliasService;
    }

    public function __toString(): string
    {
        return (string) ($this->aliasService->countAliases() ?? 0);
    }
}
