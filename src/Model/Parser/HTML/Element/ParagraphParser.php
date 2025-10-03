<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserInterface;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

class ParagraphParser implements ParserInterface
{
    /**
     * Parse paragraphs content and attributes.
     * 
     * @param string $content
     *
     * @return array
     */
    private function parseParagraphs(string $content): array
    {
        $result = [];

        $patternParagraphs = '/<p\b([^>]*)>(.*?)<\/p>/is';

        if (preg_match_all(
            pattern: $patternParagraphs,
            subject: $content,
            matches: $matches,
            flags: PREG_SET_ORDER
        )) {
            foreach ($matches as $paragraph) {
                $rawAttributes = $paragraph[1];
                $paragraphContent = $paragraph[2];

                $paragraphAttributes = [];

                if (preg_match_all(
                    pattern: "/(\w+)\s*=\s*\"([^\"]*)\"/",
                    subject: $rawAttributes,
                    matches: $attr_matches,
                    flags: PREG_SET_ORDER
                )) {
                    foreach ($attr_matches as $attr) {
                        $paragraphAttributes[$attr[1]] = $attr[2];
                    }
                }

                $result[] = new Node(
                    tagName: HtmlElementType::P->value,
                    content: $paragraphContent,
                    attributes: $paragraphAttributes,
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
            tagName: HtmlElementType::PARAGRAPHS->value,
            content: null,
            attributes: [],
            children: $this->parseParagraphs(content: $content),
        );
    }
}
