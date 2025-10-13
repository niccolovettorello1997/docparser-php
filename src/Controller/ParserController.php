<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Service\Utils\Query;
use Niccolo\DocparserPhp\Service\ValidatorService;
use Niccolo\DocparserPhp\Service\ParserService;
use Niccolo\DocparserPhp\View\RenderableInterface;
use Niccolo\DocparserPhp\View\Parser\HtmlParserView;
use Niccolo\DocparserPhp\View\Parser\JsonParserView;
use Niccolo\DocparserPhp\Controller\Responses\Response;
use Niccolo\DocparserPhp\View\ElementValidationResultView;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\RenderingType;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;

class ParserController
{
    public function __construct(
        private readonly ValidatorService $validatorService,
        private readonly ParserService $parserService,
    ) {
    }

    /**
     * Get rendering type.
     *
     * @param  array<string,string> $data
     *
     * @throws \InvalidArgumentException
     *
     * @return RenderingType
     */
    private function getRenderingType(array $data): RenderingType
    {
        // Get rendering type
        $renderingType = RenderingType::tryFrom(value: $data['renderingType']);

        // Handle invalid rendering type
        if (null === $renderingType) {
            throw new \InvalidArgumentException(
                message: sprintf('Unsupported rendering type: %s', $data['renderingType'])
            );
        }

        return $renderingType;
    }

    /**
     * Build query from file or textarea.
     * 
     * @param array<string,string>                                                             $data
     * @param array<string, array{name:string,type:string,tmp_name:string,error:int,size:int}> $files
     *
     * @throws \InvalidArgumentException
     *
     * @return Query|null
     */
    private function getQuery(array $data, array $files): ?Query
    {
        $result = null;

        // Get type
        $inputType = InputType::tryFrom(value: $data['type']);

        // Handle invalid input type
        if (null === $inputType) {
            throw new \InvalidArgumentException(
                message: sprintf('Unsupported input type: %s', $data['type'])
            );
        }

        // Get the file content if the files array is not empty
        if (!empty($files['file']['name']) && !empty($files['file']['tmp_name'])) {
            // Check if the format is valid
            $hasCorrectFormat = str_ends_with(
                haystack: basename(path: $files['file']['name']),
                needle: InputType::getExtension(type: $inputType),
            );

            $fileContent = file_get_contents(filename: $files['file']['tmp_name']);

            if ($hasCorrectFormat && false !== $fileContent) {
                $result = new Query(
                    context: $fileContent,
                    inputType: $inputType,
                    renderingType: $renderingType,
                );
            }
        } else {    // Otherwise get it from form data
            // If context is empty or not set throw an exception
            if (!isset($data['context']) || empty($data['context'])) {
                throw new \InvalidArgumentException(message: 'No context provided');
            }

            $result = new Query(
                context: $data['context'],
                inputType: $inputType,
            );
        }

        return $result;
    }

    /**
     * Handle pre-validation errors.
     *
     * @param string $message
     *
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
     * Handle the form data and return the validation view.
     * 
     * @param array<string,string> $data
     *
     * @return RenderableInterface[]
     */
    public function handleRequest(array $data): array
    {
        /** @var RenderableInterface[] */
        $result = [];

        /** @var array<string, array{name:string,type:string,tmp_name:string,error:int,size:int}> $files */
        $files = $_FILES;

        try {
            $query = $this->getQuery(
                data: $data,
                files: $files,
            );

            $renderingType = $this->getRenderingType(data: $data);

            $validationResult = $this->validatorService->runValidation(query: $query);
        } catch (\InvalidArgumentException $e) {
            return $this->handlePreValidationError(message: $e->getMessage());
        }

        $result[] = new ElementValidationResultView(
            elementValidationResult: $validationResult,
        );

        // Errors happened, don't parse the content
        if (!$validationResult->isValid() || null === $query) {
            return $result;
        }

        // Get ParserComponent and run parsing
        $parserComponent = ParserComponentFactory::getParserComponent(
            context: $query->getContext(),
            inputType: $query->getInputType()->value,
        );

        $parserResult = $parserComponent->run();

        $result[] = match ($renderingType) {
            RenderingType::HTML => new HtmlParserView(tree: $parserResult),
            RenderingType::JSON => new JsonParserView(tree: $parserResult),
        };

        return $result;
    }

    /**
     * Get parsing result as downloadable JSON.
     * 
     * @param RenderableInterface[] $views
     *
     * @return string|null
     */
    public function getJsonResult(array $views): ?string
    {
        $filteredViews = array_values(
            array: array_filter(
                array: $views,
                callback: fn (RenderableInterface $view): bool => $view instanceof JsonParserView
            )
        );

        // Something went wrong
        if (count(value: $filteredViews) !== 1) {
            return null;
        }

        return $filteredViews[0]->render();
    }
}
