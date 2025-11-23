<?php

declare(strict_types=1);

namespace DocparserPhp\Service;

use DocparserPhp\Model\Core\Validator\ElementValidationResult;
use DocparserPhp\Model\Core\Validator\ValidatorComponent;
use DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use DocparserPhp\Service\Utils\Query;
use DocparserPhp\Service\Utils\ValidatorComponentFactoryWrapper;
use Symfony\Component\Yaml\Exception\ParseException;

class ValidatorService
{
    public function __construct(
        private readonly ?ValidatorComponentFactoryWrapper $validatorComponentFactoryWrapper = null
    ) {
    }

    /**
     * Perform validation.
     *
     * @param Query $query
     *
     * @return ElementValidationResult|null
     */
    public function runValidation(Query $query): ?ElementValidationResult
    {
        $validatorComponent = null;

        try {
            if (null !== $this->validatorComponentFactoryWrapper) {
                $validatorComponent = $this->validatorComponentFactoryWrapper->createValidatorComponent(
                    context: $query->getContext(),
                    inputType: $query->getInputType()->value,
                );
            } else {
                $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
                    context: $query->getContext(),
                    inputType: $query->getInputType()->value,
                );
            }
        } catch (\RuntimeException|ParseException $e) {
            return null;
        }

        return $validatorComponent->run();
    }
}
