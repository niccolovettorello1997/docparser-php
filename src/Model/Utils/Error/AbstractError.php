<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Error;

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
}
