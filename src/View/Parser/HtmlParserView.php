<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\View\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\View\RenderableInterface;

class HtmlParserView implements RenderableInterface
{
    public function __construct(
        private readonly ?Node $tree,
    ) {
    }

    /**
     * Dynamically creates the HTML code to render an HTML Parser and returns it.
     * 
     * @param  Node|null $node
     * @return string
     */
    private function renderHtmlParser(?Node $node): string
    {
        // For html, there is only one root
        if (null === $node) {
            return '';
        }

        $result = '<ul>';

        // Render name
        $result .= "<li><strong>Element name -> </strong>{$node->getTagName()}</li>";

        // Render content if present
        if (null !== $node->getContent()) {
            $content = htmlspecialchars(string: $node->getContent());

            $result .= "<li><strong>Element content -> </strong>{$content}</li>";
        }

        // Render attributes if present
        if (!empty($node->getAttributes())) {
            $result .= '<li><strong>Attributes: </strong>';

            foreach ($node->getAttributes() as $key => $value) {
                $result .= "{$key} => {$value} ";
            }

            $result .= '</li>';
        }

        // Render children
        foreach ($node->getChildren() as $childNode) {
            $result .= $this->renderHtmlParser(node: $childNode);
        }

        $result .= '</ul>';

        return $result;
    }

    /**
     * Render the HTML Parser.
     * 
     * @return string
     */
    public function render(): string
    {
        $result = '<div><ul>';
        $result .= $this->renderHtmlParser(node: $this->tree);
        $result .= '</ul></div>';

        return $result;
    }
}
