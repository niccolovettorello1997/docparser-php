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

    /**
     * Check for empty paragraph elements.
     * 
     * @param  string $content
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function checkEmptyParagraphs(string $content, ElementValidationResult $elementValidationResult): void
    {
        $patternEmptyParagraph = '/<p\b[^>]*>\s*<\/p>/is';

        if (preg_match(
            pattern: $patternEmptyParagraph,
            subject: $content
        )) {
            $elementValidationResult->setWarning(
                warning: new EmptyElementWarning(
                    subject: self::ELEMENT_NAME,
                )
            );
        }
    }

    /**
     * Check if paragraph tags are balanced.
     *
     * @param  string $content
     * @return bool
     */
    private function checkBalancedParagraphTags(string $content): bool
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
                    return false;
                }
                array_pop($stack);
            } else {
                $stack[] = $tag;
            }
        }

        return empty($stack);
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
        if (!$this->checkBalancedParagraphTags(content: $this->sharedContext->getContext())) {
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
        $this->checkEmptyParagraphs(
            content: $this->sharedContext->getContext(),
            elementValidationResult: $elementValidationResult
        );

        return $elementValidationResult;
    }
}
