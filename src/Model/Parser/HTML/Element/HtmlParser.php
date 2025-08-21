<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

class HtmlParser extends AbstractParser
{
    /**
     * @inheritDoc
     */
    public static function parse(SharedContext $context): array
    {
        /** @var AbstractParser[] */
        $html = [];

        // Get the html element
        $patternHtmlElement = '/<html\s*(.*?)>(.*?)<\/html>/is';

        preg_match(
            pattern: $patternHtmlElement,
            subject: $context->getContext(),
            matches: $htmlElement
        );

        // Get html content
        $htmlContent = $htmlElement[2];

        // Get head children
        $head = HeadParser::parse(context: $context);

        // Get body children
        $body = BodyParser::parse(context: $context);

        // Create html object
        $html[] = new HtmlParser(
            elementName: 'html',
            content: $htmlContent,
            children: array_merge($head, $body)
        );

        return $html;
    }
}
