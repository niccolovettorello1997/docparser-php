<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Error\MissingElementError;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Utils\Warning\RecommendedAttributeWarning;

class HtmlValidator extends AbstractValidator
{
    public const string ELEMENT_NAME = 'html';

    /**
     * Validates the structure of the html tag.
     * 
     * @param  array $matchesHtml
     * @param  ElementValidationResult $elementValidationResult
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
            $prefix = trim(
                string: preg_replace(
                    pattern: $patternAllowedPrefix,
                    replacement: ' ',
                    subject: $matchesHtmlOpeningTag[1],
                ),
            );

            // Compute the suffix after the html element closing tag
            $suffix = trim(string: $matchesHtmlClosingTag[1]);

            // If there is any prefix or suffix, that means the html element is not correctly placed
            if (!empty($prefix) || !empty($suffix)) {
                $elementValidationResult->setError(
                    error: new StructuralError(
                        subject: self::ELEMENT_NAME,
                    ),
                );
            }
        }
    }

    /**
     * Check for the html element presence and if it has a closing tag.
     * 
     * @param  array $matchesHtml
     * @param  ElementValidationResult $elementValidationResult
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
                $elementValidationResult->setError(
                    error: new MalformedElementError(
                        subject: self::ELEMENT_NAME,
                    ),
                );
            } else {
                $elementValidationResult->setError(
                    error: new MissingElementError(
                        subject: self::ELEMENT_NAME,
                    ),
                );
            }
        }
    }

    /**
     * Check if the html element is unique.
     * 
     * @param  array $matchesHtml
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function isUnique(array $matchesHtml, ElementValidationResult $elementValidationResult): void
    {
        if (count(value: $matchesHtml[0]) > 1) {
            $elementValidationResult->setError(
                error: new NotUniqueElementError(
                    subject: self::ELEMENT_NAME,
                ),
            );
        }
    }

    /**
     * Check if the html element has both head and body elements. Body must be after head.
     * 
     * @param  array $matchesHtml
     * @param  ElementValidationResult $elementValidationResult
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
                $elementValidationResult->setError(
                    error: new MissingElementError(
                        subject: 'head',
                    ),
                );

                return;
            }

            $patternBody = '/<body(.*?)>(.*?)<\/body>/is';

            if (!preg_match(
                pattern: $patternBody,
                subject: $matchesHtml[2][0],
                matches: $matchesBody,
            )) {
                $elementValidationResult->setError(
                    error: new MissingElementError(
                        subject: 'body',
                    ),
                );

                return;
            }

            if (!empty($matchesHead[0]) && !empty($matchesBody[0])) {
                $patternBodyAfterHead = '/<head(.*?)>(.*?)<\/head>(.*?)<body(.*?)>/is';

                if (!preg_match(
                    pattern: $patternBodyAfterHead,
                    subject: $matchesHtml[2][0],
                )) {
                    $elementValidationResult->setError(
                        error: new StructuralError(
                            subject: self::ELEMENT_NAME,
                        ),
                    );

                    return;
                }
            }
        }
    }

    /**
     * Check if the html element has a lang attribute.
     * 
     * @param  array $matchesHtml
     * @param  ElementValidationResult $elementValidationResult
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
                $elementValidationResult->setWarning(
                    warning: new RecommendedAttributeWarning(
                        subject: 'lang',
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

        // html element must have a closing tag
        // html element must be present
        $this->htmlElementPresenceAndClosingTag(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        if (!$elementValidationResult->isValid()) {
            return $elementValidationResult;
        }

        // html element must be unique
        $this->isUnique(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        if (!$elementValidationResult->isValid()) {
            return $elementValidationResult;
        }

        // Only DOCTYPE and comments are allowed before the html element
        // html element must not have any other elements after it, except for whitespaces
        $this->validateHtmlStructure(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        if (!$elementValidationResult->isValid()) {
            return $elementValidationResult;
        }

        // html element must have a head and a body element, and body must be after head
        $this->hasHeadAndBodyElements(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        if (!$elementValidationResult->isValid()) {
            return $elementValidationResult;
        }

        // html element should have lang attribute
        $this->shouldHaveLangAttribute(
            matchesHtml: $matchesHtml,
            elementValidationResult: $elementValidationResult,
        );

        return $elementValidationResult;
    }
}
