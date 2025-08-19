<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Model\Utils\Parser\Validator;

class SharedContext
{
    public function __construct(
        private readonly string $context
    ) {
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
