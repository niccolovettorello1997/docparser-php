<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Service\Utils;

use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponent;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use Symfony\Component\Yaml\Exception\ParseException;

class ValidatorComponentFactoryWrapper
{
    /**
     * Wrapper function needed in order to make static function testable.
     * Returns the correct instance of ValidatorComponent.
     *
     * @param string $context
     * @param string $inputType
     *
     * @throws \RuntimeException
     * @throws ParseException
     *
     * @return ValidatorComponent
     *
     * @codeCoverageIgnore
     */
    public function createValidatorComponent(string $context, string $inputType): ValidatorComponent
    {
        return ValidatorComponentFactory::getValidatorComponent(
            context: $context,
            inputType: $inputType
        );
    }
}
