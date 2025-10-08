<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Controller;

use Niccolo\DocparserPhp\Controller\ApiController;
use Niccolo\DocparserPhp\Controller\Responses\ErrorResponse;
use Niccolo\DocparserPhp\Controller\Responses\ParseResponse;
use Niccolo\DocparserPhp\Model\Utils\Error\Enum\ErrorCode;
use Niccolo\DocparserPhp\Service\ParserService;
use Niccolo\DocparserPhp\Service\ValidatorService;
use PHPUnit\Framework\TestCase;

class TestInputStreamWrapper
{
    /** @var resource $handle */
    private $handle;
    /** @var resource $context */
    public $context;
    public static string $inputFile;

    public function stream_open(
        string $path,
        string $mode,
        int $options,
        ?string &$opened_path
    ): bool {
        $resource = fopen(self::$inputFile, $mode);

        if (false !== $resource) {
            $this->handle = $resource;

            return true;
        }

        return false;
    }

    /**
     * @param int<1, max> $count
     */
    public function stream_read(int $count): string|false
    {
        return fread($this->handle, $count);
    }

    public function stream_eof(): bool
    {
        return feof($this->handle);
    }

    /**
     * @return array<int, mixed>
     */
    public function stream_stat(): array
    {
        return [];
    }

    public function stream_close(): void
    {
        fclose($this->handle);
    }
}

class ApiControllerTest extends TestCase
{
    private static function registerStreamWrapper(string $inputFile): void
    {
        TestInputStreamWrapper::$inputFile = $inputFile;

        stream_wrapper_unregister('php');
        stream_wrapper_register('php', TestInputStreamWrapper::class);
    }

    private static function restoreStreamWrapper(): void
    {
        stream_wrapper_restore('php');
    }

    public function test_constructor_correct_instantiation(): void
    {
        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $this->assertInstanceOf(ApiController::class, $apiController);
    }

    public function test_parse_file_without_type_field(): void
    {
        $_POST = [];

        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::MISSING_REQUIRED_FIELD->value,
            'content' => "Missing required 'type' field",
            'details' => '',
        ];

        $response = $apiController->parseFile();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());
    }

    public function test_parse_file_with_unsupported_type_field(): void
    {
        $_POST = ['type' => 'xml'];

        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::UNSUPPORTED_TYPE->value,
            'content' => 'Input type not supported',
            'details' => '',
        ];

        $response = $apiController->parseFile();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());

        $_POST = [];
    }

    public function test_parse_file_no_uploaded_file(): void
    {
        $_POST = ['type' => 'html'];
        $_FILES = [];

        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::NO_FILE_UPLOADED->value,
            'content' => 'No file uploaded',
            'details' => '',
        ];

        $response = $apiController->parseFile();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());

        $_POST = [];
    }

    public function test_parse_file_upload_error(): void
    {
        $_POST = ['type' => 'html'];
        $_FILES = [
            'document' => [
                'error' => 1
            ]
        ];

        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::UPLOAD_ERROR->value,
            'content' => 'Upload error',
            'details' => '',
        ];

        $response = $apiController->parseFile();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());

        $_POST = [];
        $_FILES = [];
    }

    public function test_parse_file_read_error(): void
    {
        $_POST = ['type' => 'html'];
        $_FILES = [
            'document' => [
                'error' => 0,
                'tmp_name' => 'xyz',
            ]
        ];

        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::INTERNAL_SERVER_ERROR->value,
            'content' => 'Could not read request content',
            'details' => '',
        ];

        $response = $apiController->parseFile();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());

        $_POST = [];
        $_FILES = [];
    }

    public function test_parse_file_valid_file(): void
    {
        $path = __DIR__ . '/../../../fixtures/tests/valid_html.html';

        $_POST = ['type' => 'html'];
        $_FILES = [
            'document' => [
                'name' => 'valid_html.html',
                'type' => 'text/html',
                'tmp_name' => $path,
                'error' => 0,
                'size' => 901,
            ]
        ];

        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $apiController = new ApiController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        $response = $apiController->parseFile();

        $responseContent = $response->getContent();

        $this->assertInstanceOf(ParseResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString("\"status\":\"ok\"", $responseContent);
        $this->assertStringContainsString("\"Valid\":\"yes\"", $responseContent);
        $this->assertStringContainsString("\"Content\":\"This is the first section of the page.\"", $responseContent);
        $this->assertStringContainsString("\"sizeBytes\":901", $responseContent);

        $_POST = [];
        $_FILES = [];
    }

    public function test_parse_json_invalid_request(): void
    {
        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::INTERNAL_SERVER_ERROR->value,
            'content' => 'Could not read request content',
            'details' => '',
        ];

        $response = $apiController->parseJson();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());
    }

    public function test_parse_json_missing_content_field(): void
    {
        $jsonRequest = json_encode([]);
        $tempInput = tempnam(sys_get_temp_dir(), 'phpunit_input');
        file_put_contents($tempInput, $jsonRequest);

        self::registerStreamWrapper($tempInput);

        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::MISSING_REQUIRED_FIELD->value,
            'content' => "Missing required 'content' field",
            'details' => '',
        ];

        $response = $apiController->parseJson();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());

        self::restoreStreamWrapper();
        unlink($tempInput);
    }

    public function test_parse_json_empty_content_field(): void
    {
        $jsonRequest = json_encode(['content' => '']);
        $tempInput = tempnam(sys_get_temp_dir(), 'phpunit_input');
        file_put_contents($tempInput, $jsonRequest);

        self::registerStreamWrapper($tempInput);

        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::MISSING_REQUIRED_FIELD->value,
            'content' => "Missing required 'content' field",
            'details' => '',
        ];

        $response = $apiController->parseJson();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());

        self::restoreStreamWrapper();
        unlink($tempInput);
    }

    public function test_parse_json_missing_type_field(): void
    {
        $jsonRequest = json_encode(['content' => 'abc']);
        $tempInput = tempnam(sys_get_temp_dir(), 'phpunit_input');
        file_put_contents($tempInput, $jsonRequest);

        self::registerStreamWrapper($tempInput);

        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::MISSING_REQUIRED_FIELD->value,
            'content' => "Missing required 'type' field",
            'details' => '',
        ];

        $response = $apiController->parseJson();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());

        self::restoreStreamWrapper();
        unlink($tempInput);
    }

    public function test_parse_json_input_type_not_supported(): void
    {
        $jsonRequest = json_encode([
            'content' => 'abc',
            'type' => 'xml',
        ]);
        $tempInput = tempnam(sys_get_temp_dir(), 'phpunit_input');
        file_put_contents($tempInput, $jsonRequest);

        self::registerStreamWrapper($tempInput);

        $validatorServiceMock = $this->createMock(ValidatorService::class);
        $parserServiceMock = $this->createMock(ParserService::class);

        $apiController = new ApiController(
            validatorService: $validatorServiceMock,
            parserService: $parserServiceMock
        );

        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::UNSUPPORTED_TYPE->value,
            'content' => 'Input type not supported',
            'details' => '',
        ];

        $response = $apiController->parseJson();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $response->getContent());

        self::restoreStreamWrapper();
        unlink($tempInput);
    }

    public function test_parse_json_valid_input(): void
    {
        self::registerStreamWrapper(__DIR__ . '/../../../fixtures/requests/valid_json_request.json');

        $validatorService = new ValidatorService();
        $parserService = new ParserService();

        $apiController = new ApiController(
            validatorService: $validatorService,
            parserService: $parserService
        );

        $response = $apiController->parseJson();

        $responseContent = $response->getContent();

        $this->assertInstanceOf(ParseResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString("\"status\":\"ok\"", $responseContent);
        $this->assertStringContainsString("\"Valid\":\"yes\"", $responseContent);
        $this->assertStringContainsString("\"Content\":\"This is the first section of the page.\"", $responseContent);
        $this->assertStringContainsString("\"sizeBytes\":900", $responseContent);

        self::restoreStreamWrapper();
    }
}
