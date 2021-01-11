<?php

namespace App\Mercure;

use Symfony\Contracts\Translation\TranslatorTrait;

class Notification implements \JsonSerializable
{
    use TranslatorTrait;

    private string $type;

    private string $message;

    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $this->trans($message);
    }

    public function type(): string
    {
        return $this->type;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function jsonSerialize(): array
    {
        return ['type' => $this->type, 'message' => $this->message];
    }
}
