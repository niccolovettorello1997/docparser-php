<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\AbstractError;
use Niccolo\DocparserPhp\Model\Utils\Warning\AbstractWarning;

class ElementValidationResult
{
    public function __construct(
        private bool $valid = true,
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

    public function addErrors(array $errors): void
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    public function addWarning(AbstractWarning $warning): void
    {
        $this->warnings[] = $warning;
    }

    public function addWarnings(array $warnings): void
    {
        $this->warnings = array_merge($this->warnings, $warnings);
    }
}
