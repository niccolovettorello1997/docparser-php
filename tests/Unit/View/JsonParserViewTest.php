<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\View;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\Model\Core\Validator\ElementValidationResult;
use Niccolo\DocparserPhp\View\JsonParserView;
use PHPUnit\Framework\TestCase;

class JsonParserViewTest extends TestCase
{
    public function test_render_as_json_simple_node(): void
    {
        $elementValidationResult = new ElementValidationResult();

        $node = new Node(
            tagName: 'p',
            content: 'Hello',
            attributes: [],
            children: []
        );

        $json = json_encode(
            value: [
                'Validation' => [
                    'Valid' => 'yes',
                    'Errors' => [],
                    'Warnings' => [],
                ],
                'Parsed' => [
                    'Name' => 'p',
                    'Content' => 'Hello',
                ]
            ],
            flags: JSON_PRETTY_PRINT
        );

        $jsonParserView = new JsonParserView(
            elementValidationResult: $elementValidationResult,
            tree: $node
        );

        $this->assertIsString($json);
        $this->assertJsonStringEqualsJsonString(
            $json,
            $jsonParserView->render()
        );
    }

    public function test_render_validation_result_display_error(): void
    {
        $json = json_encode(
            value: [
                'Validation' => [
                    'Valid' => 'no',
                    'Errors' => [
                        [
                            'message' => 'An error occurred when displaying validation result.'
                        ]
                    ],
                    'Warnings' => [],
                ],
                'Parsed' => []
            ],
            flags: JSON_PRETTY_PRINT
        );

        $jsonParserView = new JsonParserView();

        $this->assertIsString($json);
        $this->assertJsonStringEqualsJsonString(
            $json,
            $jsonParserView->render()
        );
    }

    public function test_render_as_json_with_attributes(): void
    {
        $elementValidationResult = new ElementValidationResult();

        $node = new Node(
            tagName: 'a',
            content: 'Example',
            attributes: ['href' => 'https://example.com'],
            children: []
        );

        $expected = [
            'Validation' => [
                'Valid' => 'yes',
                'Errors' => [],
                'Warnings' => [],
            ],
            'Parsed' => [
                'Name' => 'a',
                'Attributes' => ['href' => 'https://example.com'],
                'Content' => 'Example',
            ]
        ];

        $encodedExpectedJson = json_encode(
            value: $expected,
            flags: JSON_PRETTY_PRINT,
        );

        $jsonParserView = new JsonParserView(
            elementValidationResult: $elementValidationResult,
            tree: $node
        );

        $this->assertIsString($encodedExpectedJson);
        $this->assertJsonStringEqualsJsonString(
            $encodedExpectedJson,
            $jsonParserView->render()
        );
    }

    public function test_render_as_json_nested_nodes(): void
    {
        $elementValidationResult = new ElementValidationResult();

        $child = new Node(
            tagName: 'span',
            content: 'child text',
            attributes: [],
            children: []
        );
        $parent = new Node(
            tagName: 'div',
            content: null,
            attributes: ['class' => 'container'],
            children: [$child]
        );

        $expected = [
            'Validation' => [
                'Valid' => 'yes',
                'Errors' => [],
                'Warnings' => [],
            ],
            'Parsed' => [
                'Name' => 'div',
                'Attributes' => ['class' => 'container'],
                'Children' => [
                    [
                        'Name' => 'span',
                        'Content' => 'child text',
                    ]
                ]
            ]
        ];

        $encodedExpectedJson = json_encode(
            value: $expected,
            flags: JSON_PRETTY_PRINT
        );

        $jsonParserView = new JsonParserView(
            elementValidationResult: $elementValidationResult,
            tree: $parent
        );

        $this->assertIsString($encodedExpectedJson);
        $this->assertJsonStringEqualsJsonString(
            $encodedExpectedJson,
            $jsonParserView->render()
        );
    }
}
