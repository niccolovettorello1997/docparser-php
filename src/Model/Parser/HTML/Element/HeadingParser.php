<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Parser\HTML\Element;

use DocparserPhp\Model\Core\Parser\Node;
use DocparserPhp\Model\Core\Parser\ParserInterface;
use DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

class HeadingParser implements ParserInterface
{
    /**
     * Parse headings content and attributes.
     *
     * @param string $content
     *
     * @return array<Node>
     */
    private function parseHeadings(string $content): array
    {
        $result = [];

        $patternHeadings = '/<(h[1-6])\b([^>]*)>(.*?)<\/\1>/is';

        if (preg_match_all(
            pattern: $patternHeadings,
            subject: $content,
            matches: $matches,
            flags: PREG_SET_ORDER
        )) {
            foreach ($matches as $heading) {
                $level = $heading[1];
                $rawAttributes = $heading[2];
                $headingContent = $heading[3];

                $headingAttributes = [];

                if (preg_match_all(
                    pattern: "/(\w+)\s*=\s*\"([^\"]*)\"/",
                    subject: $rawAttributes,
                    matches: $attr_matches,
                    flags: PREG_SET_ORDER
                )) {
                    foreach ($attr_matches as $attr) {
                        $headingAttributes[$attr[1]] = $attr[2];
                    }
                }

                $result[] = new Node(
                    tagName: $level,
                    content: $headingContent,
                    attributes: $headingAttributes,
                    children: []
                );
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function parse(string $content): ?Node
    {
        return new Node(
            tagName: HtmlElementType::HEADINGS->value,
            content: null,
            attributes: [],
            children: $this->parseHeadings(content: $content)
        );
    }
}
