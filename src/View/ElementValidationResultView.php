<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\View;

use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;

class ElementValidationResultView implements RenderableInterface
{
    public function __construct(
        private readonly ElementValidationResult $elementValidationResult
    ) {
    }

    /**
     * Render the ElementValidationResult.
     * 
     * @return string
     */
    public function render(): string
    {
        $resultHtml = '<div><strong>Validation: </strong>';

        if (!empty($this->elementValidationResult->getWarnings())) {
            $resultHtml .= "<div><strong>Warnings: </strong><ul>";

            foreach ($this->elementValidationResult->getWarnings() as $warning) {
                $warningMessage = htmlspecialchars(string: $warning->getMessage());

                $resultHtml .= "<li>{$warningMessage}</li>";
            }

            $resultHtml .= "</ul></div>";
        }

        if ($this->elementValidationResult->isValid()) {
            $resultHtml .= "<div>Your content is valid!</div></div>";

            return $resultHtml;
        }

        if (null !== $this->elementValidationResult->getErrors()) {
            $resultHtml .= "<div><strong>Errors: </strong><ul>";

            foreach ($this->elementValidationResult->getErrors() as $error) {
                $errorMessage = htmlspecialchars(string: $error->getMessage());

                $resultHtml .= "<li>{$errorMessage}</li>";
            }

            $resultHtml .= "</ul></div></div>";
        }

        return $resultHtml;
    }
}
