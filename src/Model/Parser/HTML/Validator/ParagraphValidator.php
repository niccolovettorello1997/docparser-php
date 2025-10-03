<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;

class ParagraphValidator extends AbstractValidator
{
    public const ELEMENT_NAME = 'paragraph';

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
     * @param ElementValidationResult $elementValidationResult
     *
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
                $elementValidationResult->addError(
                    error: new StructuralError(
                        message: 'Nested paragraph elements are not allowed in ' . self::ELEMENT_NAME . ' element.',
                    )
                );
            }
        }
    }

    /**
     * Check for invalid tags within paragraph elements.
     * 
     * @param ElementValidationResult $elementValidationResult
     *
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
                    $elementValidationResult->addError(
                        error: new InvalidContentError(
                            message: 'Invalid tag <' . $invalidTag . '> found within ' . self::ELEMENT_NAME . ' element.',
                        )
                    );
                }
            }
        }
    }

    /**
     * Check for empty paragraph elements.
     * 
     * @param string                  $content
     * @param ElementValidationResult $elementValidationResult
     *
     * @return void
     */
    private function checkEmptyParagraphs(string $content, ElementValidationResult $elementValidationResult): void
    {
        $patternEmptyParagraph = '/<p\b[^>]*>\s*<\/p>/is';

        if (preg_match(
            pattern: $patternEmptyParagraph,
            subject: $content
        )) {
            $elementValidationResult->addWarning(
                warning: new EmptyElementWarning(
                    message: 'Empty ' . self::ELEMENT_NAME . ' element detected.',
                )
            );
        }
    }

    /**
     * Check if paragraph tags are balanced.
     *
     * @param string                  $content
     * @param ElementValidationResult $elementValidationResult
     *
     * @return void
     */
    private function checkBalancedParagraphTags(string $content, ElementValidationResult $elementValidationResult): void
    {
        $pattern = '/<\/?p\b[^>]*>/i';

        preg_match_all(
            pattern: $pattern,
            subject: $content,
            matches: $matches,
        );

        $stack = [];

        foreach ($matches[0] as $tag) {
            if (stripos(haystack: $tag, needle: '</p') === 0) {
                if (empty($stack)) {
                    $elementValidationResult->addError(
                        error: new MalformedElementError(
                            message: 'Closing tag for ' . self::ELEMENT_NAME . ' element without opening.'
                        )
                    );
                }
                array_pop($stack);
            } else {
                $stack[] = $tag;
            }
        }

        if (!empty($stack)) {
            $elementValidationResult->addError(
                error: new MalformedElementError(
                    message: 'Unclosed ' . self::ELEMENT_NAME . ' element(s) detected.',
                )
            );
        }
    }

    /**
     * Validates the paragraph element in the HTML.
     * 
     * @return ElementValidationResult
     */
    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Check if paragraph tags are balanced
        $this->checkBalancedParagraphTags(
            content: $this->sharedContext->getContext(),
            elementValidationResult: $elementValidationResult
        );

        // Check for nested paragraph elements
        $this->hasNestedParagraphs(elementValidationResult: $elementValidationResult);

        // Check for invalid tags within paragraph elements
        $this->checkInvalidTags(elementValidationResult: $elementValidationResult);

        // Check for empty paragraph elements
        $this->checkEmptyParagraphs(
            content: $this->sharedContext->getContext(),
            elementValidationResult: $elementValidationResult
        );

        return $elementValidationResult;
    }
}
