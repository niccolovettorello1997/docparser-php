<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Service;

use Niccolo\DocparserPhp\Service\Utils\Query;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponent;

class ParserService
{
    /**
     * Parse the content.
     *
     * @param  Query|null $query
     *
     * @throws \InvalidArgumentException
     *
     * @return Node|null
     */
    public function parse(?Query $query): ?Node
    {
        if (null === $query) {
            throw \InvalidArgumentException(message: 'Error while reading input');
        }

        // Get ParserComponent and run parsing
        $parserComponent = ParserComponentFactory::getParserComponent(
            context: $query->getContext(),
            inputType: $query->getInputType()->value,
        );

        return $parserComponent->run();
    }
}
