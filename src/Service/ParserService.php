<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Service;

use Niccolo\DocparserPhp\Controller\Utils\Query;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponentFactory;

class ParserService
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

    public function parseUploadedFile(Query $query): ?Node
    {
        $validationResult = $this->runValidation(query: $query);

        return $this->parseText(text: $content);
    }

    public function parseText(string $text): array
    {
        // Logica di parsing del testo (esistente)
        return ['length' => strlen($text), 'preview' => substr($text, 0, 100)];
    }

    public function parseJson(array $data): array
    {
        return ['keys' => array_keys($data), 'count' => count($data)];
    }
}
