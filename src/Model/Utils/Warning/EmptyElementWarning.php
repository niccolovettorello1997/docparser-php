<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Warning;

class EmptyElementWarning extends AbstractWarning
{
    public function __construct(string $subject)
    {
        parent::__construct(message: "The element '{$subject}' should not be empty.");
    }
}
