<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Core\Parser;

use DocparserPhp\Model\Utils\Parser\SharedContext;

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
     * @param SharedContext $sharedContext
     *
     * @return Node|null
     */
    public function parse(SharedContext $sharedContext): ?Node
    {
        if (empty($this->rootElements)) {
            return null;
        }

        /** @var Node[] */
        $result = [];

        foreach ($this->rootElements as $rootElement) {
            /** @var ParserInterface $rootElementObject */
            $rootElementObject = new $rootElement();
            $currentParsingTree = $rootElementObject->parse(content: $sharedContext->getContext());

            if (null !== $currentParsingTree) {
                $result[] = $currentParsingTree;
            }
        }

        return new Node(
            tagName: 'root',
            content: null,
            attributes: [],
            children: $result
        );
    }
}
