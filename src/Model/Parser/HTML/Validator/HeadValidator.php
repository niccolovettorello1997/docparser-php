<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;

class HeadValidator extends AbstractValidator
{
    public const ELEMENT_NAME = 'head';

    /**
     * Checks if the tag is unique.
     * 
     * @param array<int,array<int,string>> $matchesHead
     * @param ElementValidationResult      $elementValidationResult
     *
     * @return void
     */
    private function isUnique(array $matchesHead, ElementValidationResult $elementValidationResult): void
    {
        if (count(value: $matchesHead[0]) > 1) {
            $elementValidationResult->addError(
                error: new NotUniqueElementError(
                    message: 'The ' . self::ELEMENT_NAME . ' element must be unique in the HTML document.',
                )
            );
        }
    }

    /**
     * Checks if the content before the head element and after the html element contains only whitespaces.
     * 
     * @param string                  $content
     * @param ElementValidationResult $elementValidationResult
     *
     * @return void
     */
    private function hasValidPrefix(string $content, ElementValidationResult $elementValidationResult): void
    {
        $patternBetweenHeadAndHtml = '/<html\b[^>]*>(.*?)<head\b[^>]*>/is';

        if (preg_match(
            pattern: $patternBetweenHeadAndHtml,
            subject: $content,
            matches: $matchesBetweenHeadAndHtml
        ) && trim(string: $matchesBetweenHeadAndHtml[1]) !== '') {
            $elementValidationResult->addError(
                error: new StructuralError(
                    message: 'Only whitespaces are allowed before the ' . self::ELEMENT_NAME . ' element and after the html element.'
                )
            );
        }
    }

    /**
     * Checks if the head element has a closing tag.
     * 
     * @param string                  $content
     * @param ElementValidationResult $elementValidationResult
     *
     * @return void
     */
    private function hasClosingTag(string $content, ElementValidationResult $elementValidationResult): void
    {
        $patternHeadClosing = '/<\/head>/i';

        if (!preg_match(
            pattern: $patternHeadClosing,
            subject: $content
        )) {
            $elementValidationResult->addError(
                error: new MalformedElementError(
                    message: self::ELEMENT_NAME . ' element is missing a closing tag.'
                )
            );
        }
    }

    /**
     * Checks if the head element contains nested elements.
     * 
     * @param array<int,array<int,string>> $matchesHead
     * @param ElementValidationResult      $elementValidationResult
     *
     * @return void
     */
    private function checkNestedElements(array $matchesHead, ElementValidationResult $elementValidationResult): void
    {
        // head element cannot contain nested html element
        $patternHtmlElement = '/<html\s*(.*?)>(.*?)<\/html>/is';

        if (preg_match(
            pattern: $patternHtmlElement,
            subject: $matchesHead[1][0] ?? '',
        )) {
            $elementValidationResult->addError(
                error: new StructuralError(
                    message: 'Nested html element detected in ' . self::ELEMENT_NAME . ' element.'
                )
            );
        }

        // head element cannot contain nested body element
        $patternBodyElement = '/<body\s*(.*?)>(.*?)<\/body>/is';

        if (preg_match(
            pattern: $patternBodyElement,
            subject: $matchesHead[1][0] ?? '',
        )) {
            $elementValidationResult->addError(
                error: new StructuralError(
                    message: 'Nested body element detected in ' . self::ELEMENT_NAME . ' element.'
                )
            );
        }
    }

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
        $this->isUnique(matchesHead: $matchesHead, elementValidationResult: $elementValidationResult);

        // Before head element and after html element only whitespaces are allowed
        $this->hasValidPrefix(
            content: $this->sharedContext->getContext(),
            elementValidationResult: $elementValidationResult
        );

        // head element must have a closing tag
        $this->hasClosingTag(
            content: $this->sharedContext->getContext(),
            elementValidationResult: $elementValidationResult
        );

        // head element cannot contain nested html element
        $this->checkNestedElements(
            matchesHead: $matchesHead,
            elementValidationResult: $elementValidationResult
        );

        return $elementValidationResult;
    }
}
