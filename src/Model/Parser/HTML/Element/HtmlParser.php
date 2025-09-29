<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

class HtmlParser implements AbstractParser
{
    /**
     * Get html element content and attributes.
     * 
     * @param  string $content
     * @return array
     */
    private function parseHtmlElement(string $content): array
    {
        // Get the html element
        $patternHtmlElement = '/<html\s*(.*?)>(.*?)<\/html>/is';

        preg_match(
            pattern: $patternHtmlElement,
            subject: $content,
            matches: $htmlElement
        );

        // Get content
        $htmlContent = $htmlElement[2];

        // Extract attributes
        $attributes = [];
        $patternAttributes = "/([a-zA-Z_:][a-zA-Z0-9_:.-]*)\s*=\s*\"([^\"]*)\"|([a-zA-Z_:][a-zA-Z0-9_:.-]*)\s*=\s*'([^']*)'|([a-zA-Z_:][a-zA-Z0-9_:.-]*)\s*=\s*([^\s\"'>]+)/";

        if (preg_match_all(
            pattern: $patternAttributes,
            subject: $htmlElement[1],
            matches: $attributesMatches,
            flags: PREG_SET_ORDER
        )) {
            foreach ($attributesMatches as $m) {
                $name  = $m[1] ?: $m[3] ?: $m[5];
                $value = $m[2] ?: $m[4] ?: $m[6];

                $attributes[$name] = $value;
            }
        }

        return [$htmlContent, $attributes];
    }

    /**
     * @inheritDoc
     */
    public function parse(string $content): ?Node
    {
        // Parse html element
        list($htmlContent, $attributes) = $this->parseHtmlElement(content: $content);

        // Get head node
        $headParser = new HeadParser();
        $headNode = $headParser->parse(content: $htmlContent);

        // Get body node
        $bodyParser = new BodyParser();
        $bodyNode = $bodyParser->parse(content: $htmlContent);

        return new Node(
            tagName: HtmlElementType::HTML->value,
            content: null,
            attributes: $attributes,
            children: [$headNode, $bodyNode]
        );
    }
}
