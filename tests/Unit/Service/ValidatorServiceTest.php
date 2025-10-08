<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Service;

use Niccolo\DocparserPhp\Service\Utils\Query;
use Niccolo\DocparserPhp\Service\Utils\ValidatorComponentFactoryWrapper;
use Niccolo\DocparserPhp\Service\ValidatorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;

class ValidatorServiceTest extends TestCase
{
    public function test_validator_service_handle_runtime_exception(): void
    {
        $queryMock = $this->createMock(Query::class);

        $validatorComponentFactoryWrapperMock = $this->getMockBuilder(ValidatorComponentFactoryWrapper::class)
            ->onlyMethods(['createValidatorComponent'])
            ->getMock();

        $validatorComponentFactoryWrapperMock->method('createValidatorComponent')
            ->will($this->throwException(new \RuntimeException()));

        $validatorService = new ValidatorService(validatorComponentFactoryWrapper: $validatorComponentFactoryWrapperMock);

        $result = $validatorService->runValidation(query: $queryMock);

        $this->assertNull($result);
    }

    public function test_validator_service_handle_parse_exception(): void
    {
        $queryMock = $this->createMock(Query::class);

        $validatorComponentFactoryWrapperMock = $this->getMockBuilder(ValidatorComponentFactoryWrapper::class)
            ->onlyMethods(['createValidatorComponent'])
            ->getMock();

        $validatorComponentFactoryWrapperMock->method('createValidatorComponent')
            ->will($this->throwException(new ParseException(rawMessage: 'test message')));

        $validatorService = new ValidatorService(validatorComponentFactoryWrapper: $validatorComponentFactoryWrapperMock);

        $result = $validatorService->runValidation(query: $queryMock);

        $this->assertNull($result);
    }
}
