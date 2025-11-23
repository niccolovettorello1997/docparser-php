<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Utils\Error;

abstract class AbstractError
{
    public function __construct(
        private readonly string $message,
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
