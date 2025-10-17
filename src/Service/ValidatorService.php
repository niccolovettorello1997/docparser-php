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
     * @param  Query $query
     *
     * @return ElementValidationResult|null
     */
    public function runValidation(Query $query): ?ElementValidationResult
    {
        try {
            $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
                context: $query->getContext(),
                inputType: $query->getInputType()->value,
            );
        } catch (\RuntimeException|ParseException $e) {
            return null;
        }

        return $validatorComponent->run();
    }
}
