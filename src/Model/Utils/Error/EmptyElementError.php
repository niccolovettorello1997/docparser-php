<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Error;

class EmptyElementError extends AbstractError
{
    public function __construct(string $subject)
    {
        parent::__construct(message: "The element '{$subject}' must not be empty.");
    }
}
