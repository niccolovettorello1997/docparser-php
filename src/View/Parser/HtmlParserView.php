<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\View\Parser;

use Niccolo\DocparserPhp\View\RenderableInterface;
use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\ElementType;
use Niccolo\DocparserPhp\Model\Parser\HTML\Element\DoctypeParser;

class HtmlParserView implements RenderableInterface
{
    public function __construct(
        private readonly DoctypeParser $doctypeParser,
    ) {
    }

    /**
     * Dynamically creates the HTML code to render an HTML Parser and returns it.
     * 
     * @param  AbstractParser $abstractParser
     * @return string
     */
    private function renderHtmlParser(AbstractParser $abstractParser): string
    {
        switch ($abstractParser->getElementName()) {
            // Doctype -> just make a recursive call
            // Head -> just make a recursive call
            case ElementType::DOCTYPE->value:
            case ElementType::HEAD->value:
                return $this->renderHtmlParser(
                    abstractParser: $abstractParser->getChildren()[0]
                );
            // Html -> join the renders for head and body
            case ElementType::HTML->value:
                $headRender = $this->renderHtmlParser(
                    abstractParser: array_values(
                        array: array_filter(
                            array: $abstractParser->getChildren(),
                            callback: fn (AbstractParser $parser): bool => $parser->getElementName() === ElementType::HEAD->value
                        )
                    )[0]
                );
                $bodyRender = $this->renderHtmlParser(
                    abstractParser: array_values(
                        array: array_filter(
                            array: $abstractParser->getChildren(),
                            callback: fn (AbstractParser $parser): bool => $parser->getElementName() === ElementType::BODY->value
                        )
                    )[0]
                );

                return $headRender . $bodyRender;
            // Title -> render title
            case ElementType::TITLE->value:
                return "<div><strong>Title => </strong>{$abstractParser->getContent()}</div>";
            // Body -> set up lists to be filled with corresponding elements
            case ElementType::BODY->value:
                $paragraphs = array_values(
                    array: array_filter(
                        array: $abstractParser->getChildren(),
                        callback: fn (AbstractParser $parser): bool => $parser->getElementName() === ElementType::PARAGRAPH->value
                    )
                );

                $headings = array_values(
                    array: array_filter(
                        array: $abstractParser->getChildren(),
                        callback: fn (AbstractParser $parser): bool => $parser->getElementName() === ElementType::HEADING->value
                    )
                );

                $bodyRender = "<div><strong>Paragraphs: </strong><ul>";

                foreach ($paragraphs as $paragraph) {
                    $bodyRender .= $this->renderHtmlParser(abstractParser: $paragraph);
                }

                $bodyRender .= "</ul></div><div><strong>Headings: </strong><ul>";

                foreach ($headings as $heading) {
                    $bodyRender .= $this->renderHtmlParser(abstractParser: $heading);
                }

                $bodyRender .= "</ul></div>";

                return $bodyRender;
            // Heading -> render content as a list element
            // Paragraph -> render content as a list element
            case ElementType::HEADING->value:
            case ElementType::PARAGRAPH->value:
                return "<li>{$abstractParser->getContent()}</li>";
            default:
                return 'Unexpected error while rendering parser!';
        }
    }

    /**
     * Render the HTML Parser.
     * 
     * @return string
     */
    public function render(): string
    {
        $result = $this->renderHtmlParser(abstractParser: $this->doctypeParser);

        return $result;
    }
}
