<?php

declare(strict_types=1);

namespace DocparserPhp\Model\Core\Validator;

use DocparserPhp\Model\Utils\Error\AbstractError;
use DocparserPhp\Model\Utils\Warning\AbstractWarning;

class ElementValidationResult
{
    public function __construct(
        /** @var AbstractError[] */
        private array $errors = [],
        /** @var AbstractWarning[] */
        private array $warnings = [],
    ) {
    }

    public function isValid(): bool
    {
        return 0 === count($this->getErrors());
    }

    /**
     * @return AbstractError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return AbstractWarning[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function addError(AbstractError $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @param array<AbstractError> $errors
      */
    public function addErrors(array $errors): void
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    public function addWarning(AbstractWarning $warning): void
    {
        $this->warnings[] = $warning;
    }

    /**
     * @param array<AbstractWarning> $warnings
     */
    public function addWarnings(array $warnings): void
    {
        $this->warnings = array_merge($this->warnings, $warnings);
    }

    /**
     * @return array{Valid: 'yes'|'no', Errors: array<array<string, string>>, Warnings: array<array<string, string>>}
     */
    public function toArray(): array
    {
        return [
            'Valid' => ($this->isValid()) ? 'yes' : 'no',
            'Errors' => array_map(
                callback: fn (AbstractError $error): array => $error->toArray(),
                array: $this->getErrors()
            ),
            'Warnings' => array_map(
                callback: fn (AbstractWarning $warning): array => $warning->toArray(),
                array: $this->getWarnings()
            ),
        ];
    }
}
