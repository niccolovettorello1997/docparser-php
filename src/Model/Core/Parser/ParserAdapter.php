<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Parser;

use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

class ParserAdapter
{
    public function __construct(
        /** @var string[] */
        private readonly array $rootElements,
    ) {
    }

    /**
     * Invoke parse method on root element classes.
     * 
     * @param  SharedContext $sharedContext
     * @return AbstractParser[]
     */
    public function parse(SharedContext $sharedContext): array
    {
        /** @var AbstractParser[] */
        $result = [];

        foreach ($this->rootElements as $rootElement) {
            $currentParsingTree = $rootElement::parse(context: $sharedContext);

            $result = array_merge(
                $result,
                $currentParsingTree
            );
        }

        return $result;
    }
}
