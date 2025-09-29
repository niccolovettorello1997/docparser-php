<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

class HeadParser implements AbstractParser
{
    /**
     * Parse head content and attributes.
     * 
     * @param  string $content
     * @return array
     */
    private function parseHead(string $content): array
    {
        // Parse the head element
        $patternHeadElement = '/<head\b([^>]*)>(.*?)<\/head>/is';

        preg_match(
            pattern: $patternHeadElement,
            subject: $content,
            matches: $headElement
        );

        $headContent = $headElement[2];

        $resultingAttributes = [];

        // Parse attributes
        if (preg_match_all(
            pattern: "/(\w+)\s*=\s*\"([^\"]*)\"/",
            subject: $headElement[1],
            matches: $attributes,
            flags: PREG_SET_ORDER
        )) {
            foreach ($attributes as $attribute) {
                $resultingAttributes[$attribute[1]] = $attribute[2];
            }
        }

        return [
            $headContent,
            $resultingAttributes,
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(string $content): ?Node
    {
        // Parse head element
        list($headContent, $attributes) = $this->parseHead($content);

        // Get title child
        $titleParser = new TitleParser();
        $titleNode = $titleParser->parse(content: $headContent);

        return new Node(
            tagName: HtmlElementType::HEAD->value,
            content: null,
            attributes: $attributes,
            children: [$titleNode]
        );
    }
}
