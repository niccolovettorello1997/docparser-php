<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\View;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;

class ParserViewFactory
{
    /**
     * Dynamically create a parser view element, based on the type of data to be parsed.
     * 
     * @param  string $type
     * @param  Node|null $tree
     * @throws \InvalidArgumentException
     * @return RenderableInterface
     */
    public static function getParserView(string $type, ?Node $tree): RenderableInterface
    {
        // Check if the type is supported
        $inputType = InputType::tryFrom(value: $type);

        switch ($inputType->value) {
            case InputType::HTML->value:
                return new HtmlParserView(tree: $tree);
            default:
                throw new \InvalidArgumentException(message: 'Input type not supported.');
        }
    }
}
