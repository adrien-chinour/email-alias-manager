<?php

namespace App\Provider\Fake\Models;

class Alias
{
    public function __construct(string $email, string $alias)
    {
        $this->email = $email;
        $this->alias = $alias;
    }

    public string $email;

    public string $alias;
}
