<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\StructuralError;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Utils\Error\MalformedElementError;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class AnchorValidator extends AbstractValidator
{
    public const string ELEMENT_NAME = 'anchor';

    /**
     * Validate href attribute.
     * 
     * @param  array $attributes
     * @param  ElementValidationResult $elementValidationResult
     * @param  string $content
     * @return void
     */
    private function validateHrefAttribute(
        array $attributes,
        ElementValidationResult $elementValidationResult,
        string $content
    ): void {
        // Check if the href attribute is present
        $hrefExists = array_filter(
            array: $attributes,
            callback: fn($match): bool => $match[1] === 'href'
        );

        if (count(value: $hrefExists) === 1) {
            // Check if the url is valid
            $url = $hrefExists[0][2] ?? $hrefExists[0][3] ?? $hrefExists[0][4] ?? '';
            $validatedUrl = filter_var(
                value: $url,
                filter: FILTER_VALIDATE_URL,
                options: FILTER_NULL_ON_FAILURE
            );

            if ($validatedUrl === null) {
                $elementValidationResult->setError(
                    error: new StructuralError(
                        subject: self::ELEMENT_NAME,
                    )
                );

                return;
            }

            // Check if the content is empty
            if (trim(string: $content) === '') {
                $elementValidationResult->setError(
                    error: new InvalidContentError(
                        subject: self::ELEMENT_NAME,
                    )
                );
            }
        }
    }

    /**
     * Check for duplicate attributes in an anchor element.
     * 
     * @param  array $attributes
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    private function checkDuplicateAttributes(array $attributes, ElementValidationResult $elementValidationResult): void
    {
        $attributeNames = array_map(
            callback: fn($match): string => $match[1],
            array: $attributes
        );

        if (count(value: $attributeNames) !== count(value: array_unique(array: $attributeNames))) {
            $elementValidationResult->setError(
                error: new StructuralError(
                    subject: self::ELEMENT_NAME,
                )
            );
        }
    }

    /**
     * Check if there are nested anchors.
     *
     * @param  ElementValidationResult $elementValidationResult
     * @return void
     */
    function hasNestedAnchors(ElementValidationResult $elementValidationResult): void
    {
        $patternAnchors = '/<a\b[^>]*>(.*?)<\/a>/is';

        // Find all paragraphs and thei content
        preg_match_all(
            pattern: $patternAnchors,
            subject: $this->sharedContext->getContext(),
            matches: $matchesAnchors
        );

        foreach ($matchesAnchors[1] as $anchor) {
            // Check is there are nested anchors
            if (preg_match(
                pattern: '/<a\b/i',
                subject: $anchor)
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

    // TODO: incapsulate all checks into separate more readable methods
    // TODO: add custom exception to fail fast
    /**
     * Validates the anchor element in the HTML.
     * 
     * @return ElementValidationResult
     */
    public function validate(): ElementValidationResult
    {
        $elementValidationResult = new ElementValidationResult();

        // Extract all anchor opening and closing tags
        $patternAnchorOpeningTag = '/<a\s+[^>]*>/i';
        $patternAnchorClosingTag = '/<\/a>/i';

        preg_match_all(
            pattern: $patternAnchorOpeningTag,
            subject: $this->sharedContext->getContext(),
            matches: $openingTags
        );

        preg_match_all(
            pattern: $patternAnchorClosingTag,
            subject: $this->sharedContext->getContext(),
            matches: $closingTags
        );

        // TODO: use linear parsing + stack for a more robust validation
        // Check if the number of opening and closing tags match
        if (count(value: $openingTags[0]) !== count(value: $closingTags[0])) {
            $elementValidationResult->setError(
                error: new MalformedElementError(
                    subject: self::ELEMENT_NAME
                )
            );

            return $elementValidationResult;
        }

        // Check for nested anchor elements
        $this->hasNestedAnchors(elementValidationResult: $elementValidationResult);

        if (!$elementValidationResult->isValid()) {
            return $elementValidationResult;
        }

        // Get all anchor elements
        $patternAnchorElement = '/<a\b([^>]*)>(.*?)<\/a>/is';

        preg_match_all(
            pattern: $patternAnchorElement,
            subject: $this->sharedContext->getContext(),
            matches: $anchorElements,
            flags: PREG_SET_ORDER,
        );

        // Validate each anchor element
        foreach ($anchorElements as $anchorElement) {
            $attributes = trim(string: $anchorElement[1]);
            $content = $anchorElement[2];

            // Compute the list of attributes and relative value used in the anchor element
            $patternAttributeName = '/([a-zA-Z_:][-a-zA-Z0-9_:.]*)\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i';

            preg_match_all(
                pattern: $patternAttributeName,
                subject: $attributes,
                matches: $attributeMatches,
                flags: PREG_SET_ORDER
            );

            // Check if there are duplicate attributes
            $this->checkDuplicateAttributes(
                attributes: $attributeMatches,
                elementValidationResult: $elementValidationResult
            );

            if (!$elementValidationResult->isValid()) {
                return $elementValidationResult;
            }

            // If href is present, validate its url
            // If href is present, the content must not be empty
            $this->validateHrefAttribute(
                attributes: $attributeMatches,
                elementValidationResult: $elementValidationResult,
                content: $content
            );

            if (!$elementValidationResult->isValid()) {
                return $elementValidationResult;
            }
        }

        return $elementValidationResult;
    }
}
