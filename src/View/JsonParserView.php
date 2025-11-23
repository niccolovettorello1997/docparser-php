<?php

declare(strict_types=1);

namespace DocparserPhp\View;

use DocparserPhp\Model\Core\Parser\Node;
use DocparserPhp\Model\Core\Validator\ElementValidationResult;
use DocparserPhp\Model\Utils\Error\InternalError;
use DocparserPhp\View\RenderableInterface;

class JsonParserView implements RenderableInterface
{
    public function __construct(
        private ?ElementValidationResult $elementValidationResult = null,
        private readonly ?Node $tree = null,
    ) {
    }

    /**
     * Render the node tree in JSON.
     *
     * @return string
     */
    public function render(): string
    {
        $result = "{\n\"success\": false,\n\"error\": \"An error occurred while rendering JSON\"\n}";

        if (null === $this->elementValidationResult) {
            $this->elementValidationResult = new ElementValidationResult();

            $this->elementValidationResult->addError(
                error: new InternalError(
                    message: 'An error occurred when displaying validation result.'
                )
            );
        }

        $encodedResult = json_encode(
            value: [
                'Validation' => $this->elementValidationResult->toArray(),
                'Parsed' => $this->tree?->toArray() ?? [],
            ],
            flags: JSON_PRETTY_PRINT
        );

        if (false !== $encodedResult) {
            $result = $encodedResult;
        }

        return $result;
    }
}
