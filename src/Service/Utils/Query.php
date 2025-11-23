<?php

declare(strict_types=1);

namespace DocparserPhp\Service\Utils;

use DocparserPhp\Model\Utils\Parser\Enum\InputType;
use DocparserPhp\Model\Utils\Parser\Enum\RenderingType;

class Query
{
    public function __construct(
        private readonly string $context,
        private readonly InputType $inputType,
    ) {
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getInputType(): InputType
    {
        return $this->inputType;
    }
}
