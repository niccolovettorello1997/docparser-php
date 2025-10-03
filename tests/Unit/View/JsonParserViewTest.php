<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\View;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use Niccolo\DocparserPhp\View\Parser\JsonParserView;
use PHPUnit\Framework\TestCase;

class JsonParserViewTest extends TestCase
{
    public function test_render_as_json_simple_node(): void
    {
        $node = new Node(
            tagName: 'p',
            content: 'Hello',
            attributes: [],
            children: []
        );

        $json = json_encode(
            value: [
                'Name' => 'p',
                'Content' => 'Hello',
            ],
            flags: JSON_PRETTY_PRINT
        );

        $jsonParserView = new JsonParserView(tree: $node);

	$this->assertIsString($json);
        $this->assertJsonStringEqualsJsonString(
            $json,
            $jsonParserView->render()
        );
    }

    public function test_render_as_json_with_attributes(): void
    {
        $node = new Node(
            tagName: 'a',
            content: 'Example',
            attributes: ['href' => 'https://example.com'],
            children: []
        );

        $expected = [
            'Name' => 'a',
            'Attributes' => ['href' => 'https://example.com'],
            'Content' => 'Example',
	];

	$encodedExpectedJson = json_encode(
	    value: $expected,
	    flags: JSON_PRETTY_PRINT,
	);

        $jsonParserView = new JsonParserView(tree: $node);

	$this->assertIsString($encodedExpectedJson);
        $this->assertJsonStringEqualsJsonString(
            $encodedExpectedJson,
            $jsonParserView->render()
        );
    }

    public function test_render_as_json_nested_nodes(): void
    {
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
            'Name' => 'div',
            'Attributes' => ['class' => 'container'],
            'Children' => [
                [
                    'Name' => 'span',
                    'Content' => 'child text',
                ]
            ]
        ];

	$encodedExpectedJson = json_encode(
            value: $expected,
            flags: JSON_PRETTY_PRINT
        );

        $jsonParserView = new JsonParserView(tree: $parent);

	$this->assertIsString($encodedExpectedJson);
	$this->assertJsonStringEqualsJsonString(
	    $encodedExpectedJson,
            $jsonParserView->render()
        );
    }
}
