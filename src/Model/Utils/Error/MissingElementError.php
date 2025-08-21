<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Error;

class MissingElementError extends AbstractError
{
    public function __construct(string $subject)
    {
        parent::__construct(message: "The required element '{$subject}' is missing.");
    }
}
