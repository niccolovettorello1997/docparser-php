<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Utils\Error\InternalError;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Warning\RecommendedAttributeWarning;

class HtmlValidator extends AbstractValidator
{
    public const ELEMENT_NAME = 'html';

    /**
     * Validates the structure of the html tag.
     * 
     * @param array<int,array<int,string>> $matchesHtml
     * @param ElementValidationResult      $elementValidationResult
     *
     * @return void
     */
    private function validateHtmlStructure(array $matchesHtml, ElementValidationResult $elementValidationResult): void
    {
        if (!empty($matchesHtml[0]) && count(value: $matchesHtml[0]) === 1) {
            preg_match(
                pattern: '/^(.*?)<html(.*?)>/is',
                subject: $this->sharedContext->getContext(),
                matches: $matchesHtmlOpeningTag,
            );

            preg_match(
                pattern: '/<\/html>(.*)$/is',
                subject: $this->sharedContext->getContext(),
                matches: $matchesHtmlClosingTag,
            );

            $patternAllowedPrefix = '/\A(?:\s|<!--[\s\S]*?-->|<!DOCTYPE\b[^>]*>)+/is';

            // Compute the prefix before the html element opening tag and remove any allowed prefixes
            $replacedPrefix = preg_replace(
                pattern: $patternAllowedPrefix,
                replacement: ' ',
                subject: $matchesHtmlOpeningTag[1],
            );

            // Handle the case in which the preg replace fails
            if (null === $replacedPrefix) {
                $elementValidationResult->addError(
                    error: new InternalError(
                        message: 'An error occurred while parsing the prefix of the ' . self::ELEMENT_NAME . ' element.',
                    ),
                );

                $replacedPrefix = '';
            }

            $prefix = trim(
                string: $replacedPrefix,
            );

            // Compute the suffix after the html element closing tag
            $suffix = trim(string: $matchesHtmlClosingTag[1]);

            // If there is any prefix or suffix, that means the html element is not correctly placed
            if (!empty($prefix) || !empty($suffix)) {
                $elementValidationResult->addError(
                    error: new StructuralError(
                        message: 'Not allowed content before or after the ' . self::ELEMENT_NAME . ' element.',
                    ),
                );
            }
        }
    }

    /**
     * Check for the html element presence and if it has a closing tag.
     * 
     * @param array<int,array<int,string>> $matchesHtml
     * @param ElementValidationResult      $elementValidationResult
     *
     * @return void
     */
    private function htmlElementPresenceAndClosingTag(array $matchesHtml, ElementValidationResult $elementValidationResult): void
    {
        if (empty($matchesHtml[0])) {
            $patternHtmlOpeningTag = '/<html(.*?)>/is';

            if (preg_match(
                pattern: $patternHtmlOpeningTag,
                subject: $this->sharedContext->getContext(),
                matches: $matchesHtmlOpeningTag,
            )) {
                $elementValidationResult->addError(
                    error: new MalformedElementError(
                        message: 'The ' . self::ELEMENT_NAME . ' element is missing a closing tag.',
                    ),
                );
            } else {
                $elementValidationResult->addError(
                    error: new MissingElementError(
                        message: 'The required element ' . self::ELEMENT_NAME . ' is missing or incorrectly written.',
                    ),
                );
            }
        }
    }

    /**
     * Check if the html element is unique.
     * 
     * @param array<int,array<int,string>> $matchesHtml
     * @param ElementValidationResult      $elementValidationResult
     *
     * @return void
     */
    private function isUnique(array $matchesHtml, ElementValidationResult $elementValidationResult): void
    {
        if (count(value: $matchesHtml[0]) > 1) {
            $elementValidationResult->addError(
                error: new NotUniqueElementError(
                    message: 'The ' . self::ELEMENT_NAME . ' element must be unique in the HTML document.',
                ),
            );
        }
    }

    /**
     * Check if the html element has both head and body elements. Body must be after head.
     * 
     * @param array<int,array<int,string>> $matchesHtml
     * @param ElementValidationResult      $elementValidationResult
     *
     * @return void
     */
    private function hasHeadAndBodyElements(array $matchesHtml, ElementValidationResult $elementValidationResult): void
    {
        if (!empty($matchesHtml[0]) && count(value: $matchesHtml[0]) === 1) {
            $patternHead = '/<head(.*?)>(.*?)<\/head>/is';

            if (!preg_match(
                pattern: $patternHead,
                subject: $matchesHtml[2][0],
                matches: $matchesHead,
            )) {
                $elementValidationResult->addError(
                    error: new MissingElementError(
                        message: 'head element in the ' . self::ELEMENT_NAME . ' element is missing or incorrectly written.',
                    ),
                );
            }

            $patternBody = '/<body(.*?)>(.*?)<\/body>/is';

            if (!preg_match(
                pattern: $patternBody,
                subject: $matchesHtml[2][0],
                matches: $matchesBody,
            )) {
                $elementValidationResult->addError(
                    error: new MissingElementError(
                        message: 'body element in the ' . self::ELEMENT_NAME . ' element is missing or incorrectly written.',
                    ),
                );
            }

            if (!empty($matchesHead[0]) && !empty($matchesBody[0])) {
                $patternBodyAfterHead = '/<head(.*?)>(.*?)<\/head>(.*?)<body(.*?)>/is';

                if (!preg_match(
                    pattern: $patternBodyAfterHead,
                    subject: $matchesHtml[2][0],
                )) {
                    $elementValidationResult->addError(
                        error: new StructuralError(
                            message: 'The body element must be after the head element in the ' . self::ELEMENT_NAME . ' element.',
                        ),
                    );
                }
            }
        }
    }

    /**
     * Check if the html element has a lang attribute.
     * 
     * @param array<int,array<int,string>> $matchesHtml
     * @param ElementValidationResult      $elementValidationResult
     *
     * @return void
     */
    private function shouldHaveLangAttribute(array $matchesHtml, ElementValidationResult $elementValidationResult): void
    {
        if (!empty($matchesHtml[0]) && count(value: $matchesHtml[0]) === 1) {
            $patternLangAttribute = '/lang="(.*?)"/i';

            if (!preg_match(
                pattern: $patternLangAttribute,
                subject: $matchesHtml[1][0],
            )) {
                $elementValidationResult->addWarning(
                    warning: new RecommendedAttributeWarning(
                        message: self::ELEMENT_NAME . ' element should have a lang attribute.',
                    ),
                );
            }
        }
    }

    /**
     * Validates the html element of a HTML document.
     *
     * @return ElementValidationResult
     */
    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Extract the html element from the HTML
        $patternHtml = '/<html\s*(.*?)>(.*?)<\/html>/is';

        preg_match_all(
            pattern: $patternHtml,
            subject: $this->sharedContext->getContext(),
            matches: $matchesHtml,
        );

        // html element must have a closing tag and must be present
        $this->htmlElementPresenceAndClosingTag(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        // html element must be unique
        $this->isUnique(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        // Only DOCTYPE and comments are allowed before the html element
        // html element must not have any other elements after it, except for whitespaces
        $this->validateHtmlStructure(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        // html element must have a head and a body element, and body must be after head
        $this->hasHeadAndBodyElements(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        // html element should have lang attribute
        $this->shouldHaveLangAttribute(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        return $elementValidationResult;
    }
}
