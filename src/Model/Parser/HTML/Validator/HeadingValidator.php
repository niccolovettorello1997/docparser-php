<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;

class HeadingValidator extends AbstractValidator
{
    public const ELEMENT_NAME = 'heading';

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
            'meta',
        ];
    }

    /**
     * Returns true if opening and closing tags are balanced, false otherwise.
     *
     * @param string                  $content
     * @param ElementValidationResult $elementValidationResult
     *
     * @return void
     */
    private function areHeadingTagsBalanced(string $content, ElementValidationResult $elementValidationResult): void
    {
        // Find all tags <hN> or </hN>
        preg_match_all(
            pattern: '/<\/?h[1-6]\b[^>]*>/i',
            subject: $content,
            matches: $matches,
        );

        $stack = [];

        foreach ($matches[0] as $tag) {
            if (preg_match(                 // Opening tag
                pattern: '/^<h([1-6])\b/i',
                subject: $tag,
                matches: $m
            )
            ) {
                $stack[] = (int)$m[1];
            } elseif (preg_match(           // Closing tag
                pattern: '/^<\/h([1-6])>/i',
                subject: $tag,
                matches: $m
            )
            ) {
                // Closing without opening
                if (empty($stack)) {
                    $elementValidationResult->addError(
                        error: new StructuralError(
                            message: 'Closing tag for ' . self::ELEMENT_NAME . ' element <h' . (int)$m[1] . '> without opening.'
                        )
                    );
                }

                $last = array_pop(array: $stack);

                // Closing tag does not match the last opening tag
                if ($last !== (int)$m[1]) {
                    $elementValidationResult->addError(
                        error: new StructuralError(
                            message: 'Closing tag for ' . self::ELEMENT_NAME . ' element <h' . (int)$m[1] . '> does not match the last opening tag.'
                        )
                    );
                }
            }
        }

        // If stack is not empty, there are unclosed tags
        if (!empty($stack)) {
            $elementValidationResult->addError(
                error: new StructuralError(
                    message: 'Unclosed ' . self::ELEMENT_NAME . ' element(s) detected.'
                )
            );
        }
    }

    /**
     * Checks if all heading elements have valid content.
     *
     * @param string                  $content
     * @param ElementValidationResult $elementValidationResult
     *
     * @return void
     */
    private function checkHeadingElementsValidContent(string $content, ElementValidationResult $elementValidationResult): void
    {
        $patternHeadingElements = '/<(h[1-6])\b([^>]*)>(.*?)<\/\1>/is';

        preg_match_all(
            pattern: $patternHeadingElements,
            subject: $content,
            matches: $matchesHeadings,
            flags: PREG_SET_ORDER
        );

        foreach ($matchesHeadings as $match) {
            // Check if the heading content is empty
            if (trim(string: $match[3]) === '') {
                $elementValidationResult->addError(
                    error: new InvalidContentError(
                        message: 'Empty content inside ' . self::ELEMENT_NAME . ' element <' . $match[1] . '>.'
                    )
                );
            }

            foreach ($this->getInvalidContentTags() as $tag) {
                $patternTag = '/<(\/)?' . $tag . '\b[^>]*>/i';

                // Check if the heading content contains any invalid tags
                if (preg_match_all(
                    pattern: $patternTag,
                    subject: $match[3],
                    matches: $matchesTag
                ) > 0) {
                    $elementValidationResult->addError(
                        error: new InvalidContentError(
                            message: 'Invalid content inside ' . self::ELEMENT_NAME . ' element <' . $match[1] . '> : contains <' . $tag . '> tag.'
                        )
                    );
                }
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
        $this->areHeadingTagsBalanced(
            content: $this->sharedContext->getContext(),
            elementValidationResult: $elementValidationResult
        );

        // Check for invalid content, i.e. invalid internal tags or empty content
        $this->checkHeadingElementsValidContent(
            content: $this->sharedContext->getContext(),
            elementValidationResult: $elementValidationResult
        );

        return $elementValidationResult;
    }
}
