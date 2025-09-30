<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Model\Core\Validator;

class ValidatorComponentFactory
{
    /**
     * Dynamically create a new instance of ValidatorComponent, based on the provided parameters.
     * 
     * @param  string $context
     * @param  string $inputType
     * @return ValidatorComponent
     */
    public static function getValidatorComponent(string $context, string $inputType): ValidatorComponent
    {
        // Build config path
        $configPath = sprintf(
            __DIR__ . '/../../../../config/Validator/validator_%s.yaml',
            $inputType
        );

        return ValidatorComponent::build(
            context: $context,
            configPath: $configPath
        );
    }
}
