<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Parser;

use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;

class ParserComponentFactory
{
    /**
     * Dynamically create a new instance of ParserComponent.
     * 
     * @param  string $context
     * @param  string $type
     * @throws \InvalidArgumentException
     * @return ParserComponent
     */
    public static function getParserComponent(string $context, string $type): ParserComponent
    {
        // Check if the type is supported
        $inputType = InputType::tryFrom(value: $type);

        // If not, throw an exception
        if($inputType === null) {
            throw new \InvalidArgumentException(message: 'Input type not supported.');
        }

        // Build config path
        $configPath = sprintf(
            __DIR__ . '/../../../../config/Parser/parser_%s.yaml',
            $inputType->value
        );

        return ParserComponent::build(
            context: $context,
            configPath: $configPath
        );
    }
}
