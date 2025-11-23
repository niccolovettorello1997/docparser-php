<?php

declare(strict_types=1);

namespace DocparserPhp\Tests\Integration;

use GuzzleHttp\Client;
use DocparserPhp\Model\Utils\Error\Enum\ErrorCode;
use PHPUnit\Framework\TestCase;

class ApiIntegrationTest extends TestCase
{
    private static ?int $serverPid = null;
    private Client $client;

    public static function setUpBeforeClass(): void
    {
        $publicFolder = __DIR__ . '/../../public';
        $routerScript = __DIR__ . '/../../public/router.php';

        $command = \sprintf(
            'php -S localhost:8080 -t %s %s > /dev/null 2>&1 & echo $!',
            $publicFolder,
            $routerScript
        );

        $output = [];
        exec(command: $command, output: $output);

        if (isset($output[0])) {
            self::$serverPid = (int) $output[0];

            sleep(seconds: 2);
        } else {
            throw new \RuntimeException(message: 'Cannot start PHP server.');
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (self::$serverPid !== null) {
            exec(command: 'kill ' . self::$serverPid);
        }
    }

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8080',
            'http_errors' => false,
            'timeout' => 5.0,
        ]);
    }

    public function test_invalid_route_for_api(): void
    {
        $expectedResponse = [
            'status' => 'error',
            'code' => ErrorCode::NOT_FOUND->value,
            'content' => 'Not Found',
            'details' => '',
        ];

        $response = $this->client
            ->get('/api/v1/invalid/route');

        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertEquals($expectedResponse, $data);
    }

    public function test_invalid_url(): void
    {
        $response = $this->client
            ->get('/invalid');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function test_health_check(): void
    {
        $version = '0.0.1';

        $expected = [
            'status' => 'ok',
            'version' => $version,
        ];

        $response = $this->client
            ->get('/api/v1/health');

        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, $data);
    }

    public function test_parse_file_missing_file(): void
    {
        $expected = [
            'status' => 'error',
            'code' => ErrorCode::NO_FILE_UPLOADED->value,
            'content' => 'No file uploaded',
            'details' => '',
        ];

        $body = [
            'multipart' => [
                [
                    'name' => 'type',
                    'contents' => 'html',
                ],
            ]
        ];

        $response = $this->client
            ->post('/api/v1/parse/file', $body);

        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($expected, $data);
    }

    public function test_parse_json_missing_content(): void
    {
        $expected = [
            'status' => 'error',
            'code' => ErrorCode::MISSING_REQUIRED_FIELD->value,
            'content' => "Missing required 'content' field",
            'details' => '',
        ];

        $body = ['type' => 'html'];

        $response = $this->client
            ->post('/api/v1/parse/json', ['json' => $body]);

        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($expected, $data);
    }

    public function test_parse_json_missing_type(): void
    {
        $expected = [
            'status' => 'error',
            'code' => ErrorCode::MISSING_REQUIRED_FIELD->value,
            'content' => "Missing required 'type' field",
            'details' => '',
        ];

        $fileContent = file_get_contents(__DIR__ . '/../../fixtures/tests/valid_html.html');

        if (false === $fileContent) {
            $this->fail('Failed to read file content.');
        }

        $body = ['content' => urlencode($fileContent)];

        $response = $this->client
            ->post('/api/v1/parse/json', ['json' => $body]);

        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($expected, $data);
    }

    public function test_parse_file_missing_type(): void
    {
        $expected = [
            'status' => 'error',
            'code' => ErrorCode::MISSING_REQUIRED_FIELD->value,
            'content' => "Missing required 'type' field",
            'details' => '',
        ];

        $body = [
            'multipart' => [
                [
                    'name' => 'document',
                    'contents' => fopen(__DIR__ . '/../../fixtures/tests/valid_html.html', 'r'),
                    'filename' => 'valid_html.html',
                ],
            ]
        ];

        $response = $this->client
            ->post('/api/v1/parse/file', $body);

        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($expected, $data);
    }

    public function test_parse_file(): void
    {
        $body = [
            'multipart' => [
                [
                    'name' => 'document',
                    'contents' => fopen(__DIR__ . '/../../fixtures/tests/valid_html.html', 'r'),
                    'filename' => 'valid_html.html',
                ],
                [
                    'name' => 'type',
                    'contents' => 'html',
                ]
            ]
        ];

        $response = $this->client
            ->post('/api/v1/parse/file', $body);

        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('ok', $data['status']);
        $this->assertArrayHasKey('requestId', $data);
        $this->assertArrayHasKey('validation', $data);
        $this->assertIsArray($data['validation']);
        $this->assertEquals('yes', $data['validation']['Valid']);
        $this->assertArrayHasKey('parsed', $data);
        $this->assertIsArray($data['parsed']);
        $this->assertNotEmpty($data['parsed']);
        $this->assertArrayHasKey('meta', $data);
        $this->assertIsArray($data['meta']);
        $this->assertArrayHasKey('durationMs', $data['meta']);
        $this->assertArrayHasKey('sizeBytes', $data['meta']);
        $this->assertArrayHasKey('version', $data['meta']);
    }

    public function test_parse_json(): void
    {
        $fileContent = file_get_contents(__DIR__ . '/../../fixtures/tests/valid_html.html');

        if (false === $fileContent) {
            $this->fail('Failed to read file contents.');
        }

        $body = [
            'type' => 'html',
            'content' => urlencode($fileContent),
        ];

        $response = $this->client
            ->post('/api/v1/parse/json', ['json' => $body]);

        $data = json_decode((string) $response->getBody(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('ok', $data['status']);
        $this->assertArrayHasKey('requestId', $data);
        $this->assertArrayHasKey('validation', $data);
        $this->assertIsArray($data['validation']);
        $this->assertEquals('yes', $data['validation']['Valid']);
        $this->assertArrayHasKey('parsed', $data);
        $this->assertIsArray($data['parsed']);
        $this->assertNotEmpty($data['parsed']);
        $this->assertArrayHasKey('meta', $data);
        $this->assertIsArray($data['meta']);
        $this->assertArrayHasKey('durationMs', $data['meta']);
        $this->assertArrayHasKey('sizeBytes', $data['meta']);
        $this->assertArrayHasKey('version', $data['meta']);
    }
}
