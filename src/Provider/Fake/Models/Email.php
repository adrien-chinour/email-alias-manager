<?php

namespace App\Provider\Fake\Models;

class Email
{
    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public string $email;
}
