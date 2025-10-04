<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Config;

use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponent;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserInterface;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ParserConfigTest extends TestCase
{
    private string $configPath = __DIR__ . '/../../config/Parser/parser_html.yaml';

    public function test_parser_root_nodes_exist_and_are_valid(): void
    {
	/** @var array<string,array<int,string>> $config */
        $config = Yaml::parseFile(filename: $this->configPath);

        foreach ($config['rootElements'] as $parserClass) {
            $this->assertTrue(class_exists(class: $parserClass));
            $this->assertTrue(is_subclass_of(object_or_class: $parserClass, class: ParserInterface::class));
        }
    }

    public function test_inexistent_parser_config(): void
    {
        $this->expectException(ParseException::class);

        $configPath = __DIR__ . '/../../fixtures/tests/inexistent_parser_configuration.yaml';

        ParserComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_empty_parser_config(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Parser configuration is empty.');

        $configPath = __DIR__ . '/../../fixtures/tests/empty_parser_configuration.yaml';

        ParserComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_invalid_parser_config(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Class not found: Niccolo\DocparserPhp\Model\Parser\HTML\Element\InexistentParser");

        $configPath = __DIR__ . '/../../fixtures/tests/invalid_parser_configuration.yaml';

        ParserComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_invalid_parser_configuration_with_duplicates(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Parser configuration contains duplicates.');

        $configPath = __DIR__ . '/../../fixtures/tests/parser_configuration_with_duplicates.yaml';

        ParserComponent::build(
            context: '',
            configPath: $configPath
        );
    }
}
