<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

class HeadingParser extends AbstractParser
{
    /**
     * @inheritDoc
     */
    public static function parse(SharedContext $context): array
    {
        /** @var AbstractParser[] */
        $headings = [];

        // Get all headings and their content
        $patternHeadingElement = '/<(h[1-6])\b([^>]*)>(.*?)<\/\1>/is';

        preg_match_all(
            pattern: $patternHeadingElement,
            subject: $context->getContext(),
            matches: $headingElements
        );

        // Create corresponding objects
        foreach($headingElements[3] as $headingContent) {
            $heading = new HeadingParser(
                elementName: 'heading',
                content: $headingContent
            );

            $headings[] = $heading;
        }

        return $headings;
    }
}
