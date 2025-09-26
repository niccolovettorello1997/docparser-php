<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Core\Parser;

use PHPUnit\Framework\TestCase;
use Niccolo\DocparserPhp\Model\Core\Parser\Node;

class NodeTest extends TestCase
{
    public function test_to_array_simple_node(): void
    {
        $node = new Node(
            tagName: 'p',
            content: 'Hello',
            attributes: [],
            children: []
        );

        $expected = [
            'Name' => 'p',
            'Content' => 'Hello',
        ];

        $this->assertSame(
            expected: $expected,
            actual: $node->toArray()
        );
    }

    public function test_to_array_with_attributes(): void
    {
        $node = new Node(
            tagName: 'a',
            content: 'Example',
            attributes: ['href' => 'https://example.com'],
            children: []
        );

        $result = $node->toArray();

        $this->assertSame(
            expected: 'a',
            actual: $result['Name']
        );
        $this->assertSame(
            expected: ['href' => 'https://example.com'],
            actual: $result['Attributes']
        );
        $this->assertSame(
            expected: 'Example',
            actual: $result['Content']
        );
        $this->assertArrayNotHasKey(
            key: 'Children',
            array: $result
        );
    }

    public function test_to_array_with_children(): void
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

        $result = $parent->toArray();

        $this->assertSame(
            expected: 'div',
            actual: $result['Name']
        );
        $this->assertSame(
            expected: ['class' => 'container'],
            actual: $result['Attributes']
        );
        $this->assertArrayNotHasKey(
            key: 'Content',
            array: $result
        );
        $this->assertCount(
            expectedCount: 1,
            haystack: $result['Children']
        );
        $this->assertSame(
            expected: 'span',
            actual: $result['Children'][0]['Name']
        );
        $this->assertSame(
            expected: 'child text',
            actual: $result['Children'][0]['Content']
        );
    }

    public function test_to_array_nested_structure(): void
    {
        $grandChild = new Node(
            tagName: 'em',
            content: 'italic',
            attributes: [],
            children: []
        );
        $child = new Node(
            tagName: 'p',
            content: null,
            attributes: [],
            children: [$grandChild]
        );
        $parent = new Node(
            tagName: 'body',
            content: null,
            attributes: [],
            children: [$child]
        );

        $array = $parent->toArray();

        $this->assertSame(
            expected: 'body',
            actual: $array['Name']
        );
        $this->assertSame(
            expected: 'p',
            actual: $array['Children'][0]['Name']
        );
        $this->assertSame(
            expected: 'em',
            actual: $array['Children'][0]['Children'][0]['Name']
        );
        $this->assertSame(
            expected: 'italic',
            actual: $array['Children'][0]['Children'][0]['Content']
        );
    }
}
