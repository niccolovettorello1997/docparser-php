<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Unit\Service;

use DocparserPhp\Config\Config;
use DocparserPhp\Service\ParserService;
use DocparserPhp\Service\Utils\ParserComponentFactoryWrapper;
use DocparserPhp\Service\Utils\Query;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;

class ParserServiceTest extends TestCase
{
    public function test_parser_service_handle_runtime_exception(): void
    {
        $queryMock = $this->createMock(Query::class);

        $parserComponentFactoryWrapperMock = $this->getMockBuilder(ParserComponentFactoryWrapper::class)
            ->onlyMethods(['createParserComponent'])
            ->getMock();

        $parserComponentFactoryWrapperMock->method('createParserComponent')
            ->will($this->throwException(new \RuntimeException()));

        $parserService = new ParserService(parserComponentFactoryWrapper: $parserComponentFactoryWrapperMock);

        $result = $parserService->parse(query: $queryMock);

        $this->assertNull($result);
    }

    public function test_parser_service_handle_parse_exception(): void
    {
        $queryMock = $this->createMock(Query::class);

        $parserComponentFactoryWrapperMock = $this->getMockBuilder(ParserComponentFactoryWrapper::class)
            ->onlyMethods(['createParserComponent'])
            ->getMock();

        $parserComponentFactoryWrapperMock->method('createParserComponent')
            ->will($this->throwException(new ParseException(rawMessage: 'test message')));

        $parserService = new ParserService(parserComponentFactoryWrapper: $parserComponentFactoryWrapperMock);

        $result = $parserService->parse(query: $queryMock);

        $this->assertNull($result);
    }

    public function test_parser_service_get_version(): void
    {
        $configMock = $this->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get'])
            ->getMock();

        $configMock->expects($this->once())
            ->method('get')
            ->willReturn('0.0.1');

        $parserService = new ParserService(config: $configMock);

        $version = $parserService->getVersion();

        $this->assertEquals('0.0.1', $version);
    }
}
