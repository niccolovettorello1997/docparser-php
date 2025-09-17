<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Config;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;
use Niccolo\DocparserPhp\Model\Core\Parser\AbstractParser;
use Niccolo\DocparserPhp\Model\Core\Parser\ParserComponent;

class ParserConfigTest extends TestCase
{
    private string $configPath = __DIR__ . '/../../config/Parser/parser_html.yaml';

    public function test_parser_root_nodes_exist_and_are_valid(): void
    {
        $config = Yaml::parseFile(filename: $this->configPath);

        foreach ($config['rootElements'] as $parserClass) {
            $this->assertTrue(condition: class_exists(class: $parserClass));
            $this->assertTrue(condition: is_subclass_of(object_or_class: $parserClass, class: AbstractParser::class));
        }
    }

    public function test_inexistent_parser_config(): void
    {
        $this->expectException(exception: ParseException::class);

        $configPath = __DIR__ . '/../../fixtures/tests/inexistent_parser_configuration.yaml';

        ParserComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_empty_parser_config(): void
    {
        $this->expectException(exception: RuntimeException::class);
        $this->expectExceptionMessage(message: 'Parser configuration is empty.');

        $configPath = __DIR__ . '/../../fixtures/tests/empty_parser_configuration.yaml';

        ParserComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_invalid_parser_config(): void
    {
        $this->expectException(exception: RuntimeException::class);
        $this->expectExceptionMessage(message: "Class not found: Niccolo\DocparserPhp\Model\Parser\HTML\Element\InexistentParser");

        $configPath = __DIR__ . '/../../fixtures/tests/invalid_parser_configuration.yaml';

        ParserComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_invalid_parser_configuration_with_duplicates(): void
    {
        $this->expectException(exception: RuntimeException::class);
        $this->expectExceptionMessage(message: 'Parser configuration contains duplicates.');

        $configPath = __DIR__ . '/../../fixtures/tests/parser_configuration_with_duplicates.yaml';

        ParserComponent::build(
            context: '',
            configPath: $configPath
        );
    }
}
