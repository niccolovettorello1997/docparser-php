<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Parser\HTML\Element;

use DocparserPhp\Model\Core\Parser\Node;
use DocparserPhp\Model\Core\Parser\ParserInterface;
use DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

class DoctypeParser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    public function parse(string $content): ?Node
    {
        // Get html child
        $htmlParser = new HtmlParser();
        $htmlNode = $htmlParser->parse(
            content: substr(
                string: $content,
                offset: 15
            )
        );

        /** @var array<Node> $children */
        $children = [];

        if ($htmlNode !== null) {
            $children[] = $htmlNode;
        }

        return new Node(
            tagName: HtmlElementType::DOCTYPE->value,
            content: null,
            attributes: [],
            children: $children,
        );
    }
}
