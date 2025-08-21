<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Error;

class StructuralError extends AbstractError
{
    public function __construct(string $subject)
    {
        parent::__construct(message: "The element '{$subject}' has an invalid structure.");
    }
}
