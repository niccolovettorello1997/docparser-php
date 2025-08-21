<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Tests\View;

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
            expected: $expectedResult,
            actual: $result,
        );
    }

    public function test_validation_view_valid_html_with_warnings(): void
    {
        $expectedResult = "<div><strong>Validation: </strong><div><strong>Warnings:</strong><ul><li>The attribute 'lang' is recommended.</li></ul></div><div>Your content is valid!</div></div>";
        $elementValidationResult = new ElementValidationResult();
        $elementValidationResult->setWarning(
            warning: new RecommendedAttributeWarning(
                subject: 'lang'
            )
        );
        $elementValidationResultView = new ElementValidationResultView(
            elementValidationResult: $elementValidationResult,
        );

        $result = $elementValidationResultView->render();

        $this->assertEquals(
            expected: $expectedResult,
            actual: $result,
        );
    }

    public function test_validation_view_invalid_html(): void
    {
        $expectedResult = "<div><strong>Validation: </strong><div><strong>Error: </strong>The element 'title' is present multiple times.</div></div>";
        $elementValidationResult = new ElementValidationResult();
        $elementValidationResult->setError(
            error: new NotUniqueElementError(
                subject: 'title'
            )
        );
        $elementValidationResultView = new ElementValidationResultView(
            elementValidationResult: $elementValidationResult,
        );

        $result = $elementValidationResultView->render();

        $this->assertEquals(
            expected: $expectedResult,
            actual: $result,
        );
    }

    public function test_validation_view_invalid_html_with_warnings(): void
    {
        $expectedResultWarnings = "<div><strong>Validation: </strong><div><strong>Warnings:</strong><ul><li>The attribute 'lang' is recommended.</li></ul></div>";
        $expectedResultError = "<div><strong>Error: </strong>The element 'title' is present multiple times.</div></div>";
        $elementValidationResult = new ElementValidationResult();
        $elementValidationResult->setError(
            error: new NotUniqueElementError(
                subject: 'title'
            )
        );
        $elementValidationResult->setWarning(
            warning: new RecommendedAttributeWarning(
                subject: 'lang'
            )
        );
        $elementValidationResultView = new ElementValidationResultView(
            elementValidationResult: $elementValidationResult,
        );

        $result = $elementValidationResultView->render();

        $this->assertEquals(
            expected: $expectedResultWarnings . $expectedResultError,
            actual: $result,
        );
    }
}
