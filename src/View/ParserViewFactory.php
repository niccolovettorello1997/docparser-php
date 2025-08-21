<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\View;

use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;

class ParserViewFactory
{
    /**
     * Dynamically create a parser view element, based on the type of data to be parsed.
     * 
     * @param  string $type
     * @param  AbstractParser[] $parsers
     * @throws \InvalidArgumentException
     * @return RenderableInterface
     */
    public static function getParserView(string $type, array $parsers): RenderableInterface
    {
        // Check if the type is supported
        $inputType = InputType::tryFrom(value: $type);

        switch ($inputType->value) {
            case InputType::HTML->value:
                return new HtmlParserView(doctypeParser: $parsers[0]);
            default:
                throw new \InvalidArgumentException(message: 'Input type not supported.');
        }
    }
}
