<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Service;

use Niccolo\DocparserPhp\Service\Utils\Query;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponent;
use Symfony\Component\Yaml\Exception\ParseException;

class ParserService
{
    /**
     * Parse the content.
     *
     * @param  Query $query
     *
     * @return Node|null
     */
    public function parse(Query $query): ?Node
    {
        // Get ParserComponent and run parsing
        try {
            $parserComponent = ParserComponentFactory::getParserComponent(
                context: $query->getContext(),
                inputType: $query->getInputType()->value,
            );
        } catch (\RuntimeException|ParseException $e) {
            return null;
        }

        return $parserComponent->run();
    }
}
