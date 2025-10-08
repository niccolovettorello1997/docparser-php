<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller;

use Niccolo\DocparserPhp\Controller\Responses\Response;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponentFactory;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;
use Niccolo\DocparserPhp\Model\Utils\Error\InvalidContentError;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\RenderingType;
use Niccolo\DocparserPhp\Service\ParserService;
use Niccolo\DocparserPhp\Service\Utils\Query;
use Niccolo\DocparserPhp\Service\ValidatorService;
use Niccolo\DocparserPhp\View\HtmlParserView;
use Niccolo\DocparserPhp\View\JsonParserView;
use Niccolo\DocparserPhp\View\RenderableInterface;

class ParserController
{
    public function __construct(
        private readonly ValidatorService $validatorService,
        private readonly ParserService $parserService,
    ) {
    }

    /**
     * Get rendering type. If invalid, default to JSON.
     *
     * @param array<string,string> $data
     *
     * @return RenderingType
     */
    private function getRenderingType(array $data): RenderingType
    {
        // Get rendering type
        $renderingType = RenderingType::tryFrom(value: $data['renderingType']);

        // If invalid, default to JSON
        return $renderingType ?? RenderingType::JSON;
    }

    /**
     * Build query from file or textarea.
     *
     * @param array<string,string>                                                             $data
     * @param array<string, array{name:string,type:string,tmp_name:string,error:int,size:int}> $files
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     *
     * @return Query
     */
    private function getQuery(array $data, array $files): Query
    {
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

            if (!$hasCorrectFormat) {
                throw new \InvalidArgumentException(message: 'File uploaded has the wrong extension');
            }

            $fileContent = @file_get_contents(filename: $files['file']['tmp_name']);

            if (false == $fileContent) {
                throw new \RuntimeException(message: 'Error while opening the uploaded file');
            }

            return new Query(
                context: $fileContent,
                inputType: $inputType,
            );
        } else {    // Otherwise get it from form data
            // If context is empty or not set throw an exception
            if (!isset($data['context']) || empty($data['context'])) {
                throw new \InvalidArgumentException(message: 'No context provided');
            }

            return new Query(
                context: $data['context'],
                inputType: $inputType,
            );
        }
    }

    /**
     * Handle pre-validation errors.
     *
     * @param string        $message
     * @param RenderingType $renderingType
     *
     * @return RenderableInterface
     */
    private function handlePreValidationError(string $message, RenderingType $renderingType): RenderableInterface
    {
        $preValidationError = new ElementValidationResult();

        $preValidationError->addError(
            error: new InvalidContentError(
                message: $message
            )
        );

        return match ($renderingType) {
            RenderingType::HTML => new HtmlParserView(elementValidationResult: $preValidationError, tree: null),
            RenderingType::JSON => new JsonParserView(elementValidationResult: $preValidationError, tree: null),
        };
    }

    /**
     * Handle the form data and return the validation view.
     *
     * @param array<string,string> $data
     *
     * @return RenderableInterface
     */
    public function handleRequest(array $data): RenderableInterface
    {
        /** @var array<string, array{name:string,type:string,tmp_name:string,error:int,size:int}> $files */
        $files = $_FILES;

        $renderingType = $this->getRenderingType(data: $data);

        try {
            $query = $this->getQuery(
                data: $data,
                files: $files,
            );
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            return $this->handlePreValidationError(message: $e->getMessage(), renderingType: $renderingType);
        }

        $validationResult = $this->validatorService->runValidation(query: $query);

        $parserResult = ((null !== $validationResult) && ($validationResult->isValid())) ? $this->parserService->parse(query: $query) : null;

        return  match ($renderingType) {
            RenderingType::HTML => new HtmlParserView(elementValidationResult: $validationResult, tree: $parserResult),
            RenderingType::JSON => new JsonParserView(elementValidationResult: $validationResult, tree: $parserResult),
        };
    }
}
