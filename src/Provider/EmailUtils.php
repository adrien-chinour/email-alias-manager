<?php

namespace App\Provider;

trait EmailUtils
{
    private function explode(string $email): array
    {
        $parts = explode('@', $email);

        if (!isset($parts[1]) || 2 !== count($parts)) {
            throw new \InvalidArgumentException("Wrong email format for '$email'");
        }

        return $parts;
    }

    public function getDomain(string $email): string
    {
        return $this->explode($email)[1];
    }

    public function withoutDomain(string $email)
    {
        return $this->explode($email)[0];
    }
}
