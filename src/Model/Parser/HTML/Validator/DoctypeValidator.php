<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class DoctypeValidator extends AbstractValidator
{
    public const ELEMENT_NAME = 'doctype';

    /**
     * Validates the presence and correctness of the DOCTYPE declaration in an HTML document.
     * 
     * @param  string $content
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function isPresent(string $content, ElementValidationResult $elementValidationResult): void
    {
        if (stripos(haystack: $content, needle: '<!DOCTYPE') !== 0) {
            $elementValidationResult->addError(
                error: new MissingElementError(
                    message: 'The ' . self::ELEMENT_NAME . ' element is missing or not written properly.',
                )
            );
        }
    }

    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Check if the doctype is present at the beginning of the HTML document
        $this->isPresent(
            content: $this->sharedContext->getContext(),
            elementValidationResult: $elementValidationResult
        );

        return $elementValidationResult;
    }
}
