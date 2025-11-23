<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Core\Parser;

use DocparserPhp\Model\Core\Parser\Node;

interface ParserInterface
{
    /**
     * Parse the corresponding element.
     *
     * @param string $content
     *
     * @return ?Node
     */
    public function parse(string $content): ?Node;
}
