<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\AbstractError;
use Niccolo\DocparserPhp\Model\Utils\Warning\AbstractWarning;

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
        return count(value: $this->errors) === 0;
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
     * @return array<string,string|array<AbstractError>|array<AbstractWarning>>
     */
    public function toArray(): array
    {
        return [
            'valid' => ($this->isValid()) ? 'yes' : 'no',
            'errors' => $this->getErrors(),
            'warnings' => $this->getWarnings(),
        ];
    }
}
