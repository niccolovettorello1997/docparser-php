<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\Markdown\Validator;

use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class MarkdownValidator extends AbstractValidator
{
    public function validate(): ElementValidationResult
    {
        // Stub markdown validator, alway correct
        return new ElementValidationResult();
    }
}
