<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\HTML\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserInterface;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\HtmlElementType;

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

        return new Node(
            tagName: HtmlElementType::DOCTYPE->value,
            content: null,
            attributes: [],
            children: [$htmlNode]
        );
    }
}
