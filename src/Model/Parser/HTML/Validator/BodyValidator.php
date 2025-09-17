<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Utils\Warning\EmptyElementWarning;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class BodyValidator extends AbstractValidator
{
    public const ELEMENT_NAME = 'body';

    /**
     * Returns the array of invalid tags that are not allowed within the body element.
     * 
     * @return string[]
     */
    private function getInvalidTags(): array
    {
        return [
            'head',
            'html',
            'title',
            'meta',
            'link',
            'noscript',
            'style',
            '!DOCTYPE',
        ];
    }

    /**
     * Return the array of invalid attributes that are not allowed in the body element.
     * 
     * @return string[]
     */
    private function getInvalidAttributes(): array
    {
        return [
            'alink',
            'background',
            'bgcolor',
            'link',
            'text',
            'vlink',
        ];
    }

    /**
     * Checks if the body element has valid attributes.
     * 
     * @param  string $bodyAttributes
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function checkInvalidAttributes(string $bodyAttributes, ElementValidationResult $elementValidationResult): void
    {
        foreach ($this->getInvalidAttributes() as $attribute) {
            if (stripos(haystack: $bodyAttributes, needle: $attribute) !== false) {
                $elementValidationResult->addError(
                    error: new MalformedElementError(
                        message: 'Invalid attribute ' . $attribute . ' detected in ' . self::ELEMENT_NAME . ' element.',
                    )
                );
            }
        }
    }

    /**
     * Checks if the body element contains any invalid tags.
     *
     * @param  string $bodyContent
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function checkInvalidTags(string $bodyContent, ElementValidationResult $elementValidationResult): void
    {
        foreach ($this->getInvalidTags() as $tag) {
            $patternInternalTag = '/<(\/)?(' . $tag . ')\b[^>]*>/i';

            if (preg_match(
                pattern: $patternInternalTag,
                subject: $bodyContent,
            )) {
                $elementValidationResult->addError(
                    error: new InvalidContentError(
                        message: 'Invalid tag <' . $tag . '> detected in ' . self::ELEMENT_NAME . ' element.',
                    )
                );
            }
        }
    }

    /**
     * Checks if the tag is unique.
     * 
     * @param  array $matchesBody
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function isUnique(array $matchesBody, ElementValidationResult $elementValidationResult): void
    {
        if (count(value: $matchesBody[0]) > 1) {
            $elementValidationResult->addError(
                error: new NotUniqueElementError(
                    message: 'The ' . self::ELEMENT_NAME . ' element must be unique in the HTML document.',
                )
            );
        }
    }

    /**
     * Checks if the body element is not empty.
     * 
     * @param  string $elementContent
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function isNotEmpty(string $elementContent, ElementValidationResult $elementValidationResult): void
    {
        if (trim(string: $elementContent) === '') {
            $elementValidationResult->addWarning(
                warning: new EmptyElementWarning(
                    message: self::ELEMENT_NAME . ' element should not be empty.'
                )
            );
        }
    }

    /**
     * Validates the body element in the HTML.
     *
     * @return ElementValidationResult The result of the validation.
     */
    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Extract body element from the HTML
        $patternBody = '/<body\b([^>]*)>(.*?)<\/body>/is';

        preg_match_all(
            pattern: $patternBody,
            subject: $this->sharedContext->getContext(),
            matches: $matchesBody
        );

        // body element must be unique
        $this->isUnique(
            matchesBody: $matchesBody,
            elementValidationResult: $elementValidationResult
        );

        // body element must have valid content
        $this->checkInvalidTags(
            bodyContent: $matchesBody[2][0],
            elementValidationResult: $elementValidationResult
        );

        // body element must have valid attributes
        $this->checkInvalidAttributes(
            bodyAttributes: $matchesBody[1][0],
            elementValidationResult: $elementValidationResult
        );

        // body element should not be empty
        $this->isNotEmpty(
            elementContent: $matchesBody[2][0],
            elementValidationResult: $elementValidationResult
        );

        return $elementValidationResult;
    }
}
