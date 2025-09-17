<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Niccolo\DocparserPhp\Model\Core\Validator\AbstractValidator;
use Niccolo\DocparserPhp\Model\Core\Validator\ValidatorComponent;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\BodyValidator;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\DoctypeValidator;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\HeadingValidator;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\HeadValidator;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\HtmlValidator;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\ParagraphValidator;
use Niccolo\DocparserPhp\Model\Parser\HTML\Validator\TitleValidator;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;

class ValidatorConfigTest extends TestCase
{
    private string $configPath = __DIR__ . '/../../config/Validator/validator_html.yaml';

    public function test_validator_classes_exist_and_are_valid(): void
    {
        $config = Yaml::parseFile(filename: $this->configPath);

        foreach ($config['validators'] as $validatorClass) {
            $this->assertTrue(condition: class_exists(class: $validatorClass));
            $this->assertTrue(condition: is_subclass_of(object_or_class: $validatorClass, class: AbstractValidator::class));
        }
    }

    public function test_validator_order_is_respected(): void
    {
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
            expected: $expected,
            actual: $config['validators'],
        );
    }

    public function test_inexistent_validator_config(): void
    {
        $this->expectException(exception: ParseException::class);

        $configPath = __DIR__ . '/../../fixtures/tests/inexistent_validator_configuration.yaml';

        ValidatorComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_empty_validator_config(): void
    {
        $this->expectException(exception: RuntimeException::class);
        $this->expectExceptionMessage(message: 'Validator configuration is empty.');

        $configPath = __DIR__ . '/../../fixtures/tests/empty_validator_configuration.yaml';

        ValidatorComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_invalid_validator_config(): void
    {
        $this->expectException(exception: RuntimeException::class);
        $this->expectExceptionMessage(message: "Class not found: Niccolo\DocparserPhp\Model\Parser\HTML\Validator\InexistentValidator");

        $configPath = __DIR__ . '/../../fixtures/tests/invalid_validator_configuration.yaml';

        ValidatorComponent::build(
            context: '',
            configPath: $configPath
        );
    }

    public function test_invalid_validator_configuration_with_duplicates(): void
    {
        $this->expectException(exception: RuntimeException::class);
        $this->expectExceptionMessage(message: 'Validator configuration contains duplicates.');

        $configPath = __DIR__ . '/../../fixtures/tests/validator_configuration_with_duplicates.yaml';

        ValidatorComponent::build(
            context: '',
            configPath: $configPath
        );
    }
}
