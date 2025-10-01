<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserInterface;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

class BodyParser implements ParserInterface
{
    /**
     * Parse body content and attributes.
     * 
     * @param  string $content
     * @return array
     */
    private function parseBody(string $content): array
    {
        // Parse the head element
        $patternBodyElement = '/<body\b([^>]*)>(.*?)<\/body>/is';

        preg_match(
            pattern: $patternBodyElement,
            subject: $content,
            matches: $bodyElement
        );

        $bodyContent = $bodyElement[2];

        $resultingAttributes = [];

        // Parse attributes
        if (preg_match_all(
            pattern: "/(\w+)\s*=\s*\"([^\"]*)\"/",
            subject: $bodyElement[1],
            matches: $attributes,
            flags: PREG_SET_ORDER
        )) {
            foreach ($attributes as $attribute) {
                $resultingAttributes[$attribute[1]] = $attribute[2];
            }
        }

        return [
            $bodyContent,
            $resultingAttributes,
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(string $content): ?Node
    {
        // Parse the body element
        list($bodyContent, $attributes) = $this->parseBody(content: $content);

        // Get paragraph children
        $paragraphParser = new ParagraphParser();
        $paragraphNode = $paragraphParser->parse(content: $bodyContent);

        // Get heading children
        $headingParser = new HeadingParser();
        $headingNode = $headingParser->parse(content: $bodyContent);

        return new Node(
            tagName: HtmlElementType::BODY->value,
            content: null,
            attributes: $attributes,
            children: [$paragraphNode, $headingNode]
        );
    }
}
