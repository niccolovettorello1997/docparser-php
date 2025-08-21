<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Error;

class NotUniqueElementError extends AbstractError
{
    public function __construct(string $subject)
    {
        parent::__construct(message: "The element '{$subject}' is present multiple times.");
    }
}
