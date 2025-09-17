<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Controller\Utils\Query;
use Niccolo\DocparserPhp\View\ParserViewFactory;
use Niccolo\DocparserPhp\View\RenderableInterface;
use Niccolo\DocparserPhp\View\ElementValidationResultView;
use Niccolo\DocparserPhp\Model\Utils\Error\UnsupportedTypeError;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;

class ParserController
{
    /**
     * Handle the form data and return the validation view.
     * 
     * @param  array $data
     * @return RenderableInterface[]
     */
    public function handleRequest(array $data): array
    {
        /** @var RenderableInterface[] */
        $result = [];

        // Get input
        $query = Query::getQuery(
            data: $data,
            files: $_FILES,
        );

        // Try to create a ValidatorComponent
        try {
            $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
                context: $query->getContext(),
                type: $query->getType(),
            );
        } catch (\InvalidArgumentException $e) {    // Unsupported type
            $validationUnsupportedType = new ElementValidationResult();

            $validationUnsupportedType->addError(
                error: new UnsupportedTypeError(
                    message: 'Unsupported type: ' . $query->getType(),
                )
            );

            $result[] = new ElementValidationResultView(
                elementValidationResult: $validationUnsupportedType,
            );

            return $result;
        }

        // Run validation
        $validationResult = $validatorComponent->run();

        $result[] = new ElementValidationResultView(
            elementValidationResult: $validationResult,
        );

        // Errors found, don't parse the content
        if (!$validationResult->isValid()) {
            return $result;
        }

        // Get ParserComponent and run parsing
        $parserComponent = ParserComponentFactory::getParserComponent(
            context: $query->getContext(),
            type: $query->getType(),
        );

        $parserResult = $parserComponent->run();

        // Get the appropriate ParserView
        $parserView = ParserViewFactory::getParserView(
            type: $query->getType(),
            parsers: $parserResult
        );

        $result[] = $parserView;

        return $result;
    }
}
