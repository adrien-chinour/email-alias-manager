<?php

namespace App\Service;

abstract class EmailTools
{
    private static function explode(string $email): array
    {
        $parts = explode('@', $email);

        if (!isset($parts[1]) || 2 !== count($parts)) {
            throw new \InvalidArgumentException("Wrong email format for '$email'");
        }

        return $parts;
    }

    public static function getDomain(string $email): string
    {
        return self::explode($email)[1];
    }

    public static function withoutDomain(string $email)
    {
        return self::explode($email)[0];
    }
}
