<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Core\Validator;

use DocparserPhp\Model\Core\Validator\ElementValidationResult;
use DocparserPhp\Model\Utils\Parser\SharedContext;

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
