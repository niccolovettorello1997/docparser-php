<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

class TitleParser implements AbstractParser
{
    /**
     * Parse title content.
     * 
     * @param  string $content
     * @return string
     */
    private function parseTitle(string $content): string
    {
        // Parse the title element
        $patternTitleElement = '/<title>(.*?)<\/title>/is';

        preg_match(
            pattern: $patternTitleElement,
            subject: $content,
            matches: $titleElement
        );

        return $titleElement[1];
    }

    /**
     * @inheritDoc
     */
    public function parse(string $content): ?Node
    {
        // Parse the title element
        $titleContent = $this->parseTitle(content: $content);

        return new Node(
            tagName: HtmlElementType::TITLE->value,
            content: $titleContent,
            attributes: [],
            children: []
        );
    }
}
