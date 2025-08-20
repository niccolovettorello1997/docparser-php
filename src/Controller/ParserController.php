<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use Niccolo\DocparserPhp\Model\Utils\Error\UnsupportedTypeError;
use Niccolo\DocparserPhp\View\ElementValidationResultView;
use Niccolo\DocparserPhp\View\ParserViewFactory;
use Niccolo\DocparserPhp\View\RenderableInterface;

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
        $context = $data['context'];
        $type = $data['type'];

        // Try to create a ValidatorComponent
        try {
            $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
                context: $context,
                type: $type
            );
        } catch (\InvalidArgumentException $e) {    // Unsupported type
            $validationUnsupportedType = new ElementValidationResult();

            $validationUnsupportedType->setError(
                error: new UnsupportedTypeError(
                    subject: $type,
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
            context: $context,
            type: $type
        );

        $parserResult = $parserComponent->run();

        // Get the appropriate ParserView
        $parserView = ParserViewFactory::getParserView(
            type: $type,
            parsers: $parserResult
        );

        $result[] = $parserView;

        return $result;
    }
}
