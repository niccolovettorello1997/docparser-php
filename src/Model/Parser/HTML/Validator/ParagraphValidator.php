<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class ParagraphValidator extends AbstractValidator
{
    public const string ELEMENT_NAME = 'paragraph';

    /**
     * Return an array of tags that are considered invalid for paragraph element.
     * 
     * @return string[]
     */
    private function getInvalidTags(): array
    {
        return [
            'address',
            'article',
            'aside',
            'blockquote',
            'div',
            'dl',
            'fieldset',
            'figure',
            'footer',
            'form',
            'h[1-6]',
            'header',
            'hr',
            'main',
            'nav',
            'ol',
            'pre',
            'section',
            'table',
            'ul',
        ];
    }

    /**
     * Check if there are nested paragraphs.
     *
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    function hasNestedParagraphs(ElementValidationResult $elementValidationResult): void
    {
        $patternParagraphs = '/<p\b[^>]*>(.*?)<\/p>/is';

        // Find all paragraphs and thei content
        preg_match_all(
            pattern: $patternParagraphs,
            subject: $this->sharedContext->getContext(),
            matches: $matchesParagraphs
        );

        foreach ($matchesParagraphs[1] as $paragraph) {
            // Check is there are nested paragraphs
            if (preg_match(
                pattern: '/<p\b/i',
                subject: $paragraph)
            ) {
                $elementValidationResult->setError(
                    error: new StructuralError(
                        subject: self::ELEMENT_NAME
                    )
                );

                return;
            }
        }

        return;
    }

    /**
     * Check for invalid tags within paragraph elements.
     * 
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function checkInvalidTags(ElementValidationResult $elementValidationResult): void
    {
        $patternParagraphElement = '/<p\b[^>]*>(.*?)<\/p>/is';

        preg_match_all(
            pattern: $patternParagraphElement,
            subject: $this->sharedContext->getContext(),
            matches: $paragraphElements
        );

        foreach ($paragraphElements[1] as $paragraphContent) {
            foreach ($this->getInvalidTags() as $invalidTag) {
                $patternInvalidTag = '/<' . $invalidTag . '\b[^>]*>/i';

                if (preg_match(pattern: $patternInvalidTag, subject: $paragraphContent)) {
                    $elementValidationResult->setError(
                        error: new InvalidContentError(
                            subject: self::ELEMENT_NAME,
                        )
                    );

                    return;
                }
            }
        }
    }

    // TODO: incapsulate all checks into separate more readable methods
    // TODO: add custom exception to fail fast
    /**
     * Validates the paragraph element in the HTML.
     * 
     * @return ElementValidationResult
     */
    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Extract all paragraph opening and closing tags
        $patternParagraphOpeningTag = '/<p\b[^>]*>/i';
        $patternParagraphClosingTag = '/<\/p>/i';

        preg_match_all(
            pattern: $patternParagraphOpeningTag,
            subject: $this->sharedContext->getContext(),
            matches: $openingTags
        );

        preg_match_all(
            pattern: $patternParagraphClosingTag,
            subject: $this->sharedContext->getContext(),
            matches: $closingTags
        );

        // TODO: use linear parsing + stack for a more robust validation
        // Check if the number of opening and closing tags match
        if (count(value: $openingTags[0]) !== count(value: $closingTags[0])) {
            $elementValidationResult->setError(
                error: new MalformedElementError(
                    subject: self::ELEMENT_NAME,
                )
            );

            return $elementValidationResult;
        }

        // Check for nested paragraph elements
        $this->hasNestedParagraphs(elementValidationResult: $elementValidationResult);

        if (!$elementValidationResult->isValid()) {
            return $elementValidationResult;
        }

        // Check for invalid tags within paragraph elements
        $this->checkInvalidTags(elementValidationResult: $elementValidationResult);

        if (!$elementValidationResult->isValid()) {
            return $elementValidationResult;
        }

        // Check for empty paragraph elements
        $patternEmptyParagraph = '/<p\b[^>]*>\s*<\/p>/is';

        if (preg_match(
            pattern: $patternEmptyParagraph,
            subject: $this->sharedContext->getContext()
        )) {
            $elementValidationResult->setWarning(
                warning: new EmptyElementWarning(
                    subject: self::ELEMENT_NAME,
                )
            );
        }

        return $elementValidationResult;
    }
}
