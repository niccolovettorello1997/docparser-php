<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class DoctypeValidator extends AbstractValidator
{
    public const string ELEMENT_NAME = 'doctype';

    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Check if the doctype is present at the beginning of the HTML document
        $patternDoctype = '/<!DOCTYPE\s+html>/i';

        if (!preg_match(
            pattern: $patternDoctype,
            subject: substr(
                string: $this->sharedContext->getContext(),
                offset: 0,
                length: 15 // Length of "<!DOCTYPE html>"
            ),
        )) {
            $elementValidationResult->setError(
                error: new MissingElementError(
                    subject: self::ELEMENT_NAME
                )
            );
        }

        return $elementValidationResult;
    }
}
