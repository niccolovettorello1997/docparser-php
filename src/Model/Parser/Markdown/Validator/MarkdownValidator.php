<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Parser\Markdown\Validator;

use DocparserPhp\Model\Core\Validator\AbstractValidator;
use DocparserPhp\Model\Core\Validator\ElementValidationResult;

class MarkdownValidator extends AbstractValidator
{
    public function validate(): ElementValidationResult
    {
        // Stub markdown validator, alway correct
        return new ElementValidationResult();
    }
}
