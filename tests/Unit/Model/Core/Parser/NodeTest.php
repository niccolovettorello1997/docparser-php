<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Model\Core\Parser;

use Niccolo\DocparserPhp\Model\Core\Parser\Node;
use PHPUnit\Framework\TestCase;

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
            $expected,
            $node->toArray()
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
            'a',
            $result['Name']
        );
        $this->assertSame(
            ['href' => 'https://example.com'],
            $result['Attributes']
        );
        $this->assertSame(
            'Example',
            $result['Content']
        );
        $this->assertArrayNotHasKey(
            'Children',
            $result
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

	/** @var array{Name: string, Attributes: array<string,string>, Children: array<int,array<string,string>>} $result */
        $result = $parent->toArray();

        $this->assertSame(
            'div',
            $result['Name']
        );
        $this->assertSame(
            ['class' => 'container'],
            $result['Attributes']
        );
        $this->assertArrayNotHasKey(
            'Content',
            $result
        );
        $this->assertCount(
            1,
            $result['Children']
        );
        $this->assertSame(
            'span',
            $result['Children'][0]['Name']
        );
        $this->assertSame(
            'child text',
            $result['Children'][0]['Content']
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
	
	/** @var array{Name: string, Children: array<int,array{Name: string, Children: array<int, array{Name: string, Content: string}>}>} $result */
        $result = $parent->toArray();

        $this->assertSame(
            'body',
            $result['Name']
        );
        $this->assertSame(
            'p',
            $result['Children'][0]['Name']
        );
        $this->assertSame(
            'em',
            $result['Children'][0]['Children'][0]['Name']
        );
        $this->assertSame(
            'italic',
            $result['Children'][0]['Children'][0]['Content']
        );
    }
}
