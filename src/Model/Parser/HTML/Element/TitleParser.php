<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

class TitleParser extends AbstractParser
{
    /**
     * @inheritDoc
     */
    public static function parse(SharedContext $context): array
    {
        /** @var AbstractParser[] */
        $title = [];

        // Get the title element
        $patternTitleElement = '/<title>(.*?)<\/title>/is';

        preg_match(
            pattern: $patternTitleElement,
            subject: $context->getContext(),
            matches: $titleElement
        );

        // Create title object
        $title[] = new TitleParser(
            elementName: 'title',
            content: $titleElement[1],
        );

        return $title;
    }
}
