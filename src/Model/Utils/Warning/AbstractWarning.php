<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Utils\Warning;

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
