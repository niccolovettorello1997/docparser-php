<?php

declare(strict_types= 1);

namespace Niccolo\DocparserPhp\Model\Core\Validator;

use Niccolo\DocparserPhp\Model\Utils\Parser\SharedContext;
use Symfony\Component\Yaml\Yaml;

class ValidatorComponent
{
    public function __construct(
        private readonly SharedContext $sharedContext,
        /** @var AbstractValidator[] */
        private array $validators = []
    ) {
    }

    /**
     * Given a context and a validator configuration path, returns a new ValidatorComponent.
     * 
     * @param  string $context
     * @param  string $configPath
     * @throws \RuntimeException
     * @return ValidatorComponent
     */
    public static function build(string $context, string $configPath): ValidatorComponent
    {
        // Create shared context
        $sharedContext = new SharedContext(context: $context);

        // Parse configuration file
        $config = Yaml::parseFile(filename: $configPath);

        /** @var AbstractValidator[] */
        $validators = [];

        foreach ($config['validators'] as $validatorClass) {
            if (class_exists(class: $validatorClass)) {
                $validators[] = new $validatorClass($sharedContext);
            } else {
                throw new \RuntimeException(message: "Class not found: $validatorClass");
            }
        }

        return new ValidatorComponent(sharedContext: $sharedContext, validators: $validators);
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

            // Merge eventual warnings
            if (!empty($partialResult->getWarnings())) {
                $result->addWarnings(warnings: $partialResult->getWarnings());
            }

            if (!$partialResult->isValid()) {
                $result->setError(error: $partialResult->getError());

                return $result;
            }
        }

        return $result;
    }
}
