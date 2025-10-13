<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Service;

use Niccolo\DocparserPhp\Service\Utils\Query;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponent;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;

class ValidatorService
{
    /**
     * Perform validation.
     * 
     * @param Query|null $query
     *
     * @throws \InvalidArgumentException
     *
     * @return ElementValidationResult
     */
    public function runValidation(?Query $query): ElementValidationResult
    {
        // Query could not be created
        if (null === $query) {
            throw new \InvalidArgumentException(
                message: 'There was an error processing the input.'
            );
        }

        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $query->getContext(),
            inputType: $query->getInputType()->value,
        );

        return $validatorComponent->run();
    }
}
