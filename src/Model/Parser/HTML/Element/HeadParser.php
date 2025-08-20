<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

class HeadParser extends AbstractParser
{
    /**
     * @inheritDoc
     */
    public static function parse(SharedContext $context): array
    {
        /** @var AbstractParser[] */
        $head = [];

        // Get the head element
        $patternHeadElement = '/<head\b[^>]*>(.*?)<\/head>/is';

        preg_match(
            pattern: $patternHeadElement,
            subject: $context->getContext(),
            matches: $headElement
        );

        // Get head content
        $headContent = $headElement[1];

        // Get title child
        $title = TitleParser::parse(context: $context);

        // Create body object
        $head[] = new HeadParser(
            elementName: 'head',
            content: $headContent,
            children: $title,
        );

        return $head;
    }
}
