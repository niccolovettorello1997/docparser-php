<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Model\Core\Validator;

use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;

class ValidatorComponentFactory
{
    /**
     * Dynamically create a new instance of ValidatorComponent, based on the provided parameters.
     * 
     * @param  string $context
     * @param  string $type
     * @throws \InvalidArgumentException
     * @return ValidatorComponent
     */
    public static function getValidatorComponent(string $context, string $type): ValidatorComponent
    {
        // Check if the type is supported
        $inputType = InputType::tryFrom(value: $type);

        // If not, throw an exception
        if($inputType === null) {
            throw new \InvalidArgumentException(message: 'Input type not supported.');
        }

        // Build config path
        $configPath = sprintf(
            __DIR__ . '/../../../../config/Validator/validator_%s.yaml',
            $inputType->value
        );

        return ValidatorComponent::build(
            context: $context,
            configPath: $configPath
        );
    }
}
