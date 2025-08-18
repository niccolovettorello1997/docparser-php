<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class HeadingValidator extends AbstractValidator
{
    public const string ELEMENT_NAME = 'heading';

    /**
     * Return the list of invalid content tags for heading elements.
     * 
     * @return string[]
     */
    private function getInvalidContentTags(): array
    {
        return [
            'address',
            'article',
            'aside',
            'section',
            'nav',
            'header',
            'footer',
            'h1',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
            'hgroup',
            'div',
            'p',
            'main',
            'figure',
            'figcaption',
            'ul',
            'ol',
            'li',
            'dl',
            'dt',
            'dd',
            'menu',
            'table',
            'thead',
            'tbody',
            'tfoot',
            'tr',
            'th',
            'td',
            'caption',
            'colgroup',
            'col',
            'form',
            'fieldset',
            'legend',
        ];
    }

    /**
     * Returns true if opening and closing tags are balanced, false otherwise.
     * 
     * @param  string $html
     * @return bool
     */
    private function areHeadingTagsBalanced(string $html): bool {
        // Find all tags <hN> or </hN>
        preg_match_all(
            pattern: '/<\/?h[1-6]\b[^>]*>/i',
            subject: $html,
            matches: $matches,
        );

        $stack = [];

        foreach ($matches[0] as $tag) {
            if (preg_match(                 // Opening tag
                pattern: '/^<h([1-6])\b/i',
                subject: $tag,
                matches: $m)
            ) {
                $stack[] = (int)$m[1];
            } elseif (preg_match(           // Closing tag
                pattern: '/^<\/h([1-6])>/i',
                subject: $tag,
                matches: $m)
            ) {
                // Closing without opening
                if (empty($stack)) {
                    return false;
                }

                $last = array_pop(array: $stack);

                // Closing tag does not match the last opening tag
                if ($last !== (int)$m[1]) {
                    return false;
                }
            }
        }

        // If stack is not empty, there are unclosed tags
        return empty($stack);
    }

    /**
     * Checks if all heading elements have valid content.
     * 
     * @param  array $matches
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function checkHeadingElementsValidContent(array $matches, ElementValidationResult $elementValidationResult): void
    {
        foreach ($matches as $match) {
            $patternInternalTags = '/<(\/)?(' . implode(separator: '|', array: $this->getInvalidContentTags()) . ')\b[^>]*>/i';

            // The content is empty or contains invalid internal tags
            $invalidContentCondition = (trim(string: $match[3]) === '') ||
                (preg_match_all(
                    pattern: $patternInternalTags,
                    subject: $match[3],
                    matches: $matchesInternalTags
                ) > 0);

            if ($invalidContentCondition) {
                $elementValidationResult->setError(
                    error: new InvalidContentError(
                        subject: self::ELEMENT_NAME
                    )
                );
                return;
            }
        }
    }

    /**
     * Validates the heading elements in the HTML.
     *
     * @return ElementValidationResult
     */
    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Opening and closing tags must be balanced
        if (!$this->areHeadingTagsBalanced(html: $this->sharedContext->getContext())) {
            $elementValidationResult->setError(
                error: new StructuralError(
                    subject: self::ELEMENT_NAME
                )
            );

            return $elementValidationResult;
        }

        // Check for invalid content, i.e. invalid internal tags or empty content
        $patternHeadingElements = '/<(h[1-6])\b([^>]*)>(.*?)<\/\1>/is';

        preg_match_all(
            pattern: $patternHeadingElements,
            subject: $this->sharedContext->getContext(),
            matches: $matchesHeadings,
            flags: PREG_SET_ORDER
        );

        $this->checkHeadingElementsValidContent(
            matches: $matchesHeadings,
            elementValidationResult: $elementValidationResult
        );

        if (!$elementValidationResult->isValid()) {
            return $elementValidationResult;
        }

        return $elementValidationResult;
    }
}
