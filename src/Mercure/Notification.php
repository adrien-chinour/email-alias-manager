<?php

namespace App\Mercure;

class Notification implements \JsonSerializable
{
    private string $type;

    private string $message;

    public function __construct(string $type, string $message)
    {
        $this->type = $type;
        $this->message = $message;
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
        return ['type' => $this->type(), 'message' => $this->message()];
    }
}
