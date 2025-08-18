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
    public const string ELEMENT_NAME = 'title';

    // TODO: incapsulate all checks into separate more readable methods
    // TODO: add custom exception to fail fast
    /**
     * Validates the title element of an HTML document.
     *
     * @return ElementValidationResult
     */
    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Extract the head element from the HTML
        $patternHead = '/<head>(.*?)<\/head>/is';

        preg_match(
            pattern: $patternHead,
            subject: $this->sharedContext->getContext(),
            matches: $headMatches
        );

        // Try to find all the occurences of the title element inside the head element
        $patternTitle = '/<title>(.*?)<\/title>/is';

        preg_match_all(
            pattern: $patternTitle,
            subject: $headMatches[1] ?? '',
            matches: $titleMatches
        );

        // Title element must be present
        if (empty($titleMatches[0])) {
            $elementValidationResult->setError(
                error: new MissingElementError(
                    subject: self::ELEMENT_NAME,
                )
            );

            return $elementValidationResult;
        }

        // Title element must be unique
        if (count(value: $titleMatches[0]) > 1) {
            $elementValidationResult->setError(
                error: new NotUniqueElementError(
                    subject: self::ELEMENT_NAME,
                )
            );

            return $elementValidationResult;
        }

        // Title element must not be empty (at least one character different from whitespace)
        if (count(value: $titleMatches[1]) === 1 && trim(string: $titleMatches[1][0]) === '') {
            $elementValidationResult->setError(
                error: new EmptyElementError(
                    subject: self::ELEMENT_NAME,
                )
            );

            return $elementValidationResult;
        }

        // Title must contain only valid characters in UTF-8
        if (count(value: $titleMatches[1]) === 1 && !mb_check_encoding(value: $titleMatches[1][0], encoding: 'UTF-8')) {
            $elementValidationResult->setError(
                error: new InvalidContentError(
                    subject: self::ELEMENT_NAME,
                )
            );
        }

        return $elementValidationResult;
    }
}
