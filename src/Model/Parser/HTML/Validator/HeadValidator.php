<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class HeadValidator extends AbstractValidator
{
    public const string ELEMENT_NAME = 'head';

    // TODO: incapsulate all checks into separate more readable methods
    // TODO: add custom exception to fail fast
    /**
     * Validates the head element in the HTML.
     *
     * @return ElementValidationResult
     */
    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Extract the head element from the HTML
        $patternHead = '/<head\b[^>]*>(.*?)<\/head>/is';

        preg_match_all(
            pattern: $patternHead,
            subject: $this->sharedContext->getContext(),
            matches: $matchesHead
        );

        // head element must be unique
        if (count(value: $matchesHead[0]) > 1) {
            $elementValidationResult->setError(
                error: new NotUniqueElementError(
                    subject: self::ELEMENT_NAME
                )
            );

            return $elementValidationResult;
        }

        // Before head element and after html element only whitespaces are allowed
        $patternBetweenHeadAndHtml = '/<html\b[^>]*>(.*?)<head\b[^>]*>/is';

        if (preg_match(
            pattern: $patternBetweenHeadAndHtml,
            subject: $this->sharedContext->getContext(),
            matches: $matchesBetweenHeadAndHtml
        ) && trim(string: $matchesBetweenHeadAndHtml[1]) !== '') {
            $elementValidationResult->setError(
                error: new StructuralError(
                    subject: self::ELEMENT_NAME
                )
            );

            return $elementValidationResult;
        }

        // head element must have a closing tag
        $patternHeadClosing = '/<\/head>/i';

        if (!preg_match(
            pattern: $patternHeadClosing,
            subject: $this->sharedContext->getContext()
        )) {
            $elementValidationResult->setError(
                error: new MalformedElementError(
                    subject: self::ELEMENT_NAME
                )
            );

            return $elementValidationResult;
        }

        // head element cannot contain nested html element
        $patternHtmlElement = '/<html\s*(.*?)>(.*?)<\/html>/is';

        if (preg_match(
            pattern: $patternHtmlElement,
            subject: $matchesHead[1][0],
        )) {
            $elementValidationResult->setError(
                error: new StructuralError(
                    subject: self::ELEMENT_NAME,
                )
            );

            return $elementValidationResult;
        }

        // head element cannot contain nested body element
        $patternBodyElement = '/<body\s*(.*?)>(.*?)<\/body>/is';

        if (preg_match(
            pattern: $patternBodyElement,
            subject: $matchesHead[1][0],
        )) {
            $elementValidationResult->setError(
                error: new StructuralError(
                    subject: self::ELEMENT_NAME,
                )
            );
        }

        return $elementValidationResult;
    }
}
