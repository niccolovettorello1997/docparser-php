<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Parser\Markdown\Element;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserInterface;

class MarkdownParser implements ParserInterface
{
    public function parse(string $content): ?Node
    {
        // Stub markdown parser
        return new Node(
            tagName: 'markdown',
            content: $content,
            attributes: [],
            children: []
        );
    }
}
