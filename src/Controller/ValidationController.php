<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use Niccolo\DocparserPhp\Model\Utils\Error\UnsupportedTypeError;
use Niccolo\DocparserPhp\View\ElementValidationResultView;
use Niccolo\DocparserPhp\View\RenderableInterface;

class ValidationController
{
    /**
     * Handle the form data and return the validation view.
     * 
     * @param  array $data
     * @return RenderableInterface
     */
    public function handleRequest(array $data): RenderableInterface
    {
        $result = new ElementValidationResult();

        $context = $data['context'];
        $type = $data['type'];

        try {
            $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
                context: $context,
                type: $type
            );
        } catch (\InvalidArgumentException $e) {    // Unsupported type
            $result->setError(
                error: new UnsupportedTypeError(
                    subject: $type,
                )
            );

            return new ElementValidationResultView(
                elementValidationResult: $result,
            );
        }

        $result = $validatorComponent->run();

        return new ElementValidationResultView(
            elementValidationResult: $result,
        );
    }
}
