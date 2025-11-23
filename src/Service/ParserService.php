<?php

declare(strict_types=1);

namespace DocparserPhp\Service;

use DocparserPhp\Config\Config;
use DocparserPhp\Model\Core\Parser\Node;
use DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use DocparserPhp\Service\Utils\ParserComponentFactoryWrapper;
use DocparserPhp\Service\Utils\Query;
use Symfony\Component\Yaml\Exception\ParseException;

class ParserService
{
    public function __construct(
        private readonly ?Config $config = null,
        private readonly ?ParserComponentFactoryWrapper $parserComponentFactoryWrapper = null
    ) {
    }

    /**
     * Get parser version.
     *
     * @return string|null
     */
    public function getVersion(): ?string
    {
        /** @var string|null $version */
        $version = $this->config?->get(key: 'APP_VERSION');

        return $version;
    }

    /**
     * Parse the content.
     *
     * @param Query $query
     *
     * @return Node|null
     */
    public function parse(Query $query): ?Node
    {
        $parserComponent = null;

        // Get ParserComponent and run parsing
        try {
            if (null !== $this->parserComponentFactoryWrapper) {
                $parserComponent = $this->parserComponentFactoryWrapper->createParserComponent(
                    context: $query->getContext(),
                    inputType: $query->getInputType()->value
                );
            } else {
                $parserComponent = ParserComponentFactory::getParserComponent(
                    context: $query->getContext(),
                    inputType: $query->getInputType()->value
                );
            }
        } catch (\RuntimeException|ParseException $e) {
            return null;
        }

        return $parserComponent->run();
    }
}
