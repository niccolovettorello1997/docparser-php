<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

class DoctypeParser extends AbstractParser
{
    /**
     * @inheritDoc
     */
    public static function parse(SharedContext $context): array
    {
        /** @var AbstractParser[] */
        $doctype = [];

        // Extract doctype content
        $doctypeContent = substr(
            string: $context->getContext(),
            offset: 15,
        );

        // Get html child
        $html = HtmlParser::parse(context: $context);

        // Create title object
        $doctype[] = new DoctypeParser(
            elementName: 'doctype',
            content: $doctypeContent,
            children: $html,
        );

        return $doctype;
    }
}
