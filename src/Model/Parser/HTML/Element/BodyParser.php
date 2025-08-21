<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

class BodyParser extends AbstractParser
{
    /**
     * @inheritDoc
     */
    public static function parse(SharedContext $context): array
    {
        /** @var AbstractParser[] */
        $body = [];

        // Get the body element
        $patternBodyElement = '/<body\b([^>]*)>(.*?)<\/body>/is';

        preg_match(
            pattern: $patternBodyElement,
            subject: $context->getContext(),
            matches: $bodyElement
        );

        // Get body content
        $bodyContent = $bodyElement[2];

        // Get paragraph children
        $paragraphs = ParagraphParser::parse(context: $context);

        // Get heading children
        $headings = HeadingParser::parse(context: $context);

        // Create body object
        $body[] = new BodyParser(
            elementName: 'body',
            content: $bodyContent,
            children: array_merge($headings, $paragraphs)
        );

        return $body;
    }
}
