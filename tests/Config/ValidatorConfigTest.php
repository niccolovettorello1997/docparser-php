<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Config;

use DocparserPhp\Model\Core\Validator\AbstractValidator;
use DocparserPhp\Model\Core\Validator\ValidatorComponent;
use DocparserPhp\Model\Parser\HTML\Validator\BodyValidator;
use DocparserPhp\Model\Parser\HTML\Validator\DoctypeValidator;
use DocparserPhp\Model\Parser\HTML\Validator\HeadingValidator;
use DocparserPhp\Model\Parser\HTML\Validator\HeadValidator;
use DocparserPhp\Model\Parser\HTML\Validator\HtmlValidator;
use DocparserPhp\Model\Parser\HTML\Validator\ParagraphValidator;
use DocparserPhp\Model\Parser\HTML\Validator\TitleValidator;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ValidatorConfigTest extends TestCase
{
    private string $configPath = __DIR__ . '/../../config/Validator/validator_html.yaml';

    public function test_validator_classes_exist_and_are_valid(): void
    {
        /** @var array<string,array<int,string>> $config */
        $config = Yaml::parseFile(filename: $this->configPath);

        foreach ($config['validators'] as $validatorClass) {
            $this->assertTrue(class_exists(class: $validatorClass));
            $this->assertTrue(is_subclass_of(object_or_class: $validatorClass, class: AbstractValidator::class));
        }
    }

    public function test_validator_order_is_respected(): void
    {
        /** @var array<string,array<int,string>> $config */
        $config = Yaml::parseFile(filename: $this->configPath);

        $expected = [
            DoctypeValidator::class,
            HtmlValidator::class,
            HeadValidator::class,
            BodyValidator::class,
            TitleValidator::class,
            HeadingValidator::class,
            ParagraphValidator::class,
        ];

        $this->assertSame(
            $expected,
            $config['validators'],
        );
    }

    public function test_inexistent_validator_config(): void
    {
        $this->expectException(ParseException::class);

        $configPath = __DIR__ . '/../../fixtures/tests/inexistent_validator_configuration.yaml';

        ValidatorComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_empty_validator_config(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Validator configuration is empty.');

        $configPath = __DIR__ . '/../../fixtures/tests/empty_validator_configuration.yaml';

        ValidatorComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_invalid_validator_config(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Class not found: DocparserPhp\Model\Parser\HTML\Validator\InexistentValidator");

        $configPath = __DIR__ . '/../../fixtures/tests/invalid_validator_configuration.yaml';

        ValidatorComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_invalid_validator_configuration_with_duplicates(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Validator configuration contains duplicates.');

        $configPath = __DIR__ . '/../../fixtures/tests/validator_configuration_with_duplicates.yaml';

        ValidatorComponent::build(
            context: '',
            configPath: $configPath
        );
    }
}
