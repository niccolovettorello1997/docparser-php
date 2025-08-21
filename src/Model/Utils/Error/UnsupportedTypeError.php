<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Error;

class UnsupportedTypeError extends AbstractError
{
    public function __construct(string $subject)
    {
        parent::__construct(message: "The typee '{$subject}' is not supported.");
    }
}
