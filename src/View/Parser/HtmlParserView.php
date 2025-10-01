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
     * Render an array into html.
     * 
     * @param  array $input
     * @return string
     */
    private function arrayToHtml(array $input): string
    {
        $html = '<ul>';

        foreach ($input as $key => $value) {
            $html .= '<li>';

            if (is_array(value: $value)) {
                $html .= htmlspecialchars(string: (string)$key) . ': ' . $this->arrayToHtml(input: $value);
            } else {
                $html .= htmlspecialchars(string: (string)$key) . ': ' . htmlspecialchars(string: (string)$value);
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Render the node tree in html.
     * 
     * @return string
     */
    public function render(): string
    {
        $result = '<div><strong>Parsing result: </strong>';
        $result .= $this->arrayToHtml(input: $this->tree->toArray());
        $result .= '</div>';

        return $result;
    }
}
