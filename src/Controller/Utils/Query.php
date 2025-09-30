<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Controller\Utils;

use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\InputType;
use Niccolo\DocparserPhp\Model\Utils\Parser\Enum\RenderingType;

class Query
{
    public function __construct(
        private readonly string $context,
        private readonly InputType $inputType,
        private readonly RenderingType $renderingType,
    ) {
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getInputType(): InputType
    {
        return $this->inputType;
    }

    public function getRenderingType(): RenderingType
    {
        return $this->renderingType;
    }

    /**
     * Build query from file or textarea.
     * 
     * @param  array $data
     * @param  array $files
     * @throws \InvalidArgumentException
     * @return ?Query
     */
    public static function getQuery(array $data, array $files): ?Query
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

        // Get render type
        $renderingType = RenderingType::tryFrom(value: $data['renderingType']);

        // Handle invalid rendering type
        if (null === $renderingType) {
            throw new \InvalidArgumentException(
                message: sprintf('Unsupported rendering type: %s', $data['renderingType'])
            );
        }

        // Get the file content if the files array is not empty
        if (!empty($files['file']['name']) && !empty($files['file']['tmp_name'])) {
            // Check if the format is valid
            $hasCorrectFormat = str_ends_with(
                haystack: basename(path: $files['file']['name']),
                needle: InputType::getExtension(type: $inputType),
            );

            if ($hasCorrectFormat) {
                $result = new Query(
                    context: file_get_contents(filename: $files['file']['tmp_name']),
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
                renderingType: $renderingType,
            );
        }

        return $result;
    }
}
