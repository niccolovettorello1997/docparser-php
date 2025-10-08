<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Model\Core\Validator;

use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class ValidatorComponent
{
    public function __construct(
        /** @var AbstractValidator[] */
        private array $validators = []
    ) {
    }

    /**
     * Given a context and a validator configuration path, returns a new ValidatorComponent.
     *
     * @param string $context
     * @param string $configPath
     *
     * @throws \RuntimeException
     * @throws ParseException
     *
     * @return ValidatorComponent
     */
    public static function build(string $context, string $configPath): ValidatorComponent
    {
        // Create shared context
        $sharedContext = new SharedContext(context: $context);

        /** @var array<string,array<int,string>> $config */
        $config = Yaml::parseFile(filename: $configPath);

        // If validator config is found to be empty, raise an exception
        if (!isset($config['validators']) || empty($config['validators'])) {
            throw new \RuntimeException(message: 'Validator configuration is empty.');
        }

        // If validator config contains duplicates, raise an exception
        if (count(value: $config['validators']) !== count(value: array_unique(array: $config['validators']))) {
            throw new \RuntimeException(message: 'Validator configuration contains duplicates.');
        }

        /** @var AbstractValidator[] */
        $validators = [];

        // Instantiate validator classes
        foreach ($config['validators'] as $validatorClass) {
            if (class_exists(class: $validatorClass) && is_subclass_of(object_or_class: $validatorClass, class: AbstractValidator::class)) {
                $validators[] = new $validatorClass($sharedContext);
            } else {
                throw new \RuntimeException(message: "Class not found: $validatorClass");
            }
        }

        return new ValidatorComponent(validators: $validators);
    }

    /**
     * Run the validation process.
     *
     * @return ElementValidationResult
     */
    public function run(): ElementValidationResult
    {
        $result = new ElementValidationResult();

        foreach ($this->validators as $validator) {
            $partialResult = $validator->validate();

            // Merge warnings
            if (!empty($partialResult->getWarnings())) {
                $result->addWarnings(warnings: $partialResult->getWarnings());
            }

            // Merge errors
            if (!empty($partialResult->getErrors())) {
                $result->addErrors(errors: $partialResult->getErrors());
            }
        }

        return $result;
    }
}
