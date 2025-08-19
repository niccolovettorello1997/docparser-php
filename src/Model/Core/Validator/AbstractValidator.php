<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Validator;

use Niccolo\DocparserPhp\Model\Utils\Parser\Validator\SharedContext;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

abstract class AbstractValidator
{
    public function __construct(
        protected readonly SharedContext $sharedContext,
    ) {
    }

    /**
     * Validates the HTML content.
     * 
     * @return ElementValidationResult
     */
    abstract public function validate(): ElementValidationResult;
}
