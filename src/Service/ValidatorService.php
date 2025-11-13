<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Service;

use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponent;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use Niccolo\DocparserPhp\Service\Utils\Query;
use Niccolo\DocparserPhp\Service\Utils\ValidatorComponentFactoryWrapper;
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
