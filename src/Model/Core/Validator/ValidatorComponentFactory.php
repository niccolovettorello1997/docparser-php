<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Core\Validator;

use Symfony\Component\Yaml\Exception\ParseException;

class ValidatorComponentFactory
{
    /**
     * Dynamically create a new instance of ValidatorComponent, based on the provided parameters.
     *
     * @param string $context
     * @param string $inputType
     *
     * @throws \RuntimeException
     * @throws ParseException
     *
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
