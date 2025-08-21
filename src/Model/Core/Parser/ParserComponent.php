<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Model\Core\Parser;

use Symfony\Component\Yaml\Yaml;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserAdapter;
use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;

class ParserComponent
{
    public function __construct(
        private readonly SharedContext $sharedContext,
        /** @var string[] */
        private readonly array $rootElements = [],
    ) {
    }

    public function getRootElements(): array
    {
        return $this->rootElements;
    }

    /**
     * Given a context and a parser configuration path, returns a new ParserComponent.
     * 
     * @param  string $context
     * @param  string $configPath
     * @throws \RuntimeException
     * @return ParserComponent
     */
    public static function build(string $context, string $configPath): ParserComponent
    {
        // Create shared context
        $sharedContext = new SharedContext(context: $context);

        // Parse configuration file
        $config = Yaml::parseFile(filename: $configPath);

        // Get the list of root elements
        $rootElements = [];

        foreach ($config['rootElements'] as $rootElement) {
            if (!class_exists(class: $rootElement)) {
                throw new \RuntimeException(message: "Class not found: $rootElement");
            }

            $rootElements[] = $rootElement;
        }

        // Create corresponding ParserComponent
        return new ParserComponent(
            sharedContext: $sharedContext,
            rootElements: $rootElements
        );
    }

    /**
     * Run the parsing process.
     * 
     * @return AbstractParser[]
     */
    public function run(): array
    {
        $parserAdapter = new ParserAdapter(
            rootElements: $this->getRootElements(),
        );

        return $parserAdapter->parse(
            sharedContext: $this->sharedContext
        );
    }
}
