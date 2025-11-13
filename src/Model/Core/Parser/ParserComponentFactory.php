<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Parser;

use Symfony\Component\Yaml\Exception\ParseException;

class ParserComponentFactory
{
    /**
     * Dynamically create a new instance of ParserComponent.
     *
     * @param string $context
     * @param string $inputType
     *
     * @throws \RuntimeException
     * @throws ParseException
     *
     * @return ParserComponent
     */
    public static function getParserComponent(string $context, string $inputType): ParserComponent
    {
        // Build config path
        $configPath = sprintf(
            __DIR__ . '/../../../../config/Parser/parser_%s.yaml',
            $inputType
        );

        return ParserComponent::build(
            context: $context,
            configPath: $configPath
        );
    }
}
