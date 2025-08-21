<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Validator;

use Niccolo\DocparserPhp\Model\Utils\Error\AbstractError;
use Niccolo\DocparserPhp\Model\Utils\Warning\AbstractWarning;

class ElementValidationResult
{
    public function __construct(
        private bool $valid = true,
        private ?AbstractError $error = null,
        /** @var AbstractWarning[] */
        private array $warnings = [],
    ) {
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getError(): ?AbstractError
    {
        return $this->error;
    }

    /**
     * @return AbstractWarning[]
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function setError(AbstractError $error): void
    {
        $this->error = $error;

        if ($this->isValid()) {
            $this->valid = false;
        }
    }

    public function setWarning(AbstractWarning $warning): void
    {
        $this->warnings[] = $warning;
    }

    public function addWarnings(array $warnings): void
    {
        $this->warnings = array_merge($this->warnings, $warnings);
    }
}
