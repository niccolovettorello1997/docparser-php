<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Warning;

abstract class AbstractWarning
{
    protected string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}

