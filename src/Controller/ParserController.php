<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Controller\Utils\Query;
use Niccolo\DocparserPhp\Controller\Utils\Response;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;
use Niccolo\DocparserPhp\View\Parser\JsonParserView;
use Niccolo\DocparserPhp\View\RenderableInterface;
use Niccolo\DocparserPhp\View\ElementValidationResultView;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\RenderingType;

class ParserController
{
    /**
     * Handle pre-validation errors.
     *
     * @param  string $message
     * @return ElementValidationResultView[]
     */
    private function handlePreValidationError(string $message): array
    {
        $preValidationError = new ElementValidationResult();

        $preValidationError->addError(
            error: new InvalidContentError(
                message: $message
            )
        );

        return [new ElementValidationResultView(
            elementValidationResult: $preValidationError,
        )];
    }

    /**
     * Perform validation.
     * 
     * @param  Query $query
     * @throws \InvalidArgumentException
     * @return ElementValidationResult
     */
    private function runValidation(Query $query): ElementValidationResult
    {
        $validatorComponent = ValidatorComponentFactory::getValidatorComponent(
            context: $query->getContext(),
            inputType: $query->getInputType()->value,
        );

        return $validatorComponent->run();
    }

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

        try {
            $query = Query::getQuery(
                data: $data,
                files: $_FILES,
            );

            $validationResult = $this->runValidation(query: $query);
        } catch (\InvalidArgumentException $e) {
            return $this->handlePreValidationError(message: $e->getMessage());
        }

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
            inputType: $query->getInputType()->value,
        );

        $parserResult = $parserComponent->run();

        $result[] = match ($query->getRenderingType()) {
            RenderingType::HTML => new HtmlParserView(tree: $parserResult),
            RenderingType::JSON => new JsonParserView(tree: $parserResult),
        };

        return $result;
    }

    /**
     * Get parsing result as downloadable JSON.
     * 
     * @param  RenderableInterface[] $views
     * @return Response
     */
    public function getJsonResult(array $views): Response
    {
        $filteredViews = array_values(
            array: array_filter(
                array: $views,
                callback: fn (RenderableInterface $view): bool => $view instanceof JsonParserView
            )
        );

        if (count(value: $filteredViews) === 1) {
            return new Response(
                statusCode: 200,
                content: $filteredViews[0]->render(),
            );
        }

        return new Response(
            statusCode: 500,
            content: json_encode(value: ['error' => 'An error occurred while rendering JSON'])
        );
    }
}
