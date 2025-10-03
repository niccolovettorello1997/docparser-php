<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Tests\Unit\View;

use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\Model\Utils\Error\NotUniqueElementError;
use Niccolo\DocparserPhp\Model\Utils\Warning\RecommendedAttributeWarning;
use Niccolo\DocparserPhp\View\ElementValidationResultView;
use PHPUnit\Framework\TestCase;

class ElementValidationResultViewTest extends TestCase
{
    public function test_validation_view_valid_html_no_warnings(): void
    {
        $expectedResult = "<div><strong>Validation: </strong><div>Your content is valid!</div></div>";
        $elementValidationResult = new ElementValidationResult();
        $elementValidationResultView = new ElementValidationResultView(
            elementValidationResult: $elementValidationResult,
        );

        $result = $elementValidationResultView->render();

        $this->assertEquals(
            $expectedResult,
            $result,
        );
    }

    public function test_validation_view_valid_html_with_warnings(): void
    {
        $expectedResult = "<div><strong>Validation: </strong><div><strong>Warnings: </strong><ul><li>head element should have a lang attribute.</li></ul></div><div>Your content is valid!</div></div>";
        $elementValidationResult = new ElementValidationResult();
        $elementValidationResult->addWarning(
            warning: new RecommendedAttributeWarning(
                message: 'head element should have a lang attribute.'
            )
        );
        $elementValidationResultView = new ElementValidationResultView(
            elementValidationResult: $elementValidationResult,
        );

        $result = $elementValidationResultView->render();

        $this->assertEquals(
            $expectedResult,
            $result,
        );
    }

    public function test_validation_view_invalid_html(): void
    {
        $expectedResult = "<div><strong>Validation: </strong><div><strong>Errors: </strong><ul><li>The title element must be unique in the HTML document.</li></ul></div></div>";
        $elementValidationResult = new ElementValidationResult();
        $elementValidationResult->addError(
            error: new NotUniqueElementError(
                message: 'The title element must be unique in the HTML document.'
            )
        );
        $elementValidationResultView = new ElementValidationResultView(
            elementValidationResult: $elementValidationResult,
        );

        $result = $elementValidationResultView->render();

        $this->assertEquals(
            $expectedResult,
            $result,
        );
    }

    public function test_validation_view_invalid_html_with_warnings(): void
    {
        $expectedResultWarnings = "<div><strong>Validation: </strong><div><strong>Warnings: </strong><ul><li>head element should have a lang attribute.</li></ul></div>";
        $expectedResultError = "<div><strong>Errors: </strong><ul><li>The title element must be unique in the HTML document.</li></ul></div></div>";
        $elementValidationResult = new ElementValidationResult();
        $elementValidationResult->addError(
            error: new NotUniqueElementError(
                message: 'The title element must be unique in the HTML document.'
            )
        );
        $elementValidationResult->addWarning(
            warning: new RecommendedAttributeWarning(
                message: 'head element should have a lang attribute.'
            )
        );
        $elementValidationResultView = new ElementValidationResultView(
            elementValidationResult: $elementValidationResult,
        );

        $result = $elementValidationResultView->render();

        $this->assertEquals(
            $expectedResultWarnings . $expectedResultError,
            $result,
        );
    }
}
