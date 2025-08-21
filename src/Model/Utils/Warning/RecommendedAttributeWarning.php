<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Utils\Warning;

class RecommendedAttributeWarning extends AbstractWarning
{
    public function __construct(string $subject)
    {
        parent::__construct(message: "The attribute '{$subject}' is recommended.");
    }
}

