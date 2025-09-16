<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\EmptyElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class TitleValidator extends AbstractValidator
{
    public const ELEMENT_NAME = 'title';

    /**
     * Check if the title element is present in the HTML document.
     * 
     * @param  array $titleMatches
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function isPresent(array $titleMatches, ElementValidationResult $elementValidationResult): void
    {
        if (empty($titleMatches[0])) {
            $elementValidationResult->addError(
                error: new MissingElementError(
                    message: 'The ' . self::ELEMENT_NAME . ' element is missing or not written properly.',
                )
            );
        }
    }

    /**
     * Check if the title element is unique.
     * 
     * @param  array $titleMatches
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function isUnique(array $titleMatches, ElementValidationResult $elementValidationResult): void
    {
        if (count(value: $titleMatches[0]) > 1) {
            $elementValidationResult->addError(
                error: new NotUniqueElementError(
                    message: 'The ' . self::ELEMENT_NAME . ' element must be unique in the HTML document.',
                )
            );
        }
    }

    /**
     * Check if the title element is not empty (at least one character different from whitespace).
     * 
     * @param  array $titleMatches
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function isNotEmpty(array $titleMatches, ElementValidationResult $elementValidationResult): void
    {
        if (count(value: $titleMatches[1]) === 1 && trim(string: $titleMatches[1][0]) === '') {
            $elementValidationResult->addError(
                error: new EmptyElementError(
                    message: 'The ' . self::ELEMENT_NAME . ' element must not be empty.',
                )
            );
        }
    }

    /**
     * Check if the title element contains only valid characters in UTF-8.
     * 
     * @param  array $titleMatches
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function isValidUTF8(array $titleMatches, ElementValidationResult $elementValidationResult): void
    {
        if (count(value: $titleMatches[1]) === 1 && !mb_check_encoding(value: $titleMatches[1][0], encoding: 'UTF-8')) {
            $elementValidationResult->addError(
                error: new InvalidContentError(
                    message: 'Invalid UTF-8 characters detected in ' . self::ELEMENT_NAME . ' element.',
                )
            );
        }
    }

    /**
     * Validates the title element of an HTML document.
     *
     * @return ElementValidationResult
     */
    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Try to find all the occurences of the title element inside the head element
        $patternTitle = '/<title>(.*?)<\/title>/is';

        preg_match_all(
            pattern: $patternTitle,
            subject: $this->sharedContext->getContext(),
            matches: $titleMatches
        );

        // Title element must be present
        $this->isPresent(
            titleMatches: $titleMatches,
            elementValidationResult: $elementValidationResult
        );

        // Title element must be unique
        $this->isUnique(
            titleMatches: $titleMatches,
            elementValidationResult: $elementValidationResult
        );

        // Title element must not be empty (at least one character different from whitespace)
        $this->isNotEmpty(
            titleMatches: $titleMatches,
            elementValidationResult: $elementValidationResult
        );

        // Title must contain only valid characters in UTF-8
        $this->isValidUTF8(
            titleMatches: $titleMatches,
            elementValidationResult: $elementValidationResult
        );

        return $elementValidationResult;
    }
}
