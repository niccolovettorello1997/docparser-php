<?php

declare(strict_types=1);

namespace Niccolo\DocparserPhp\Tests\Unit\Controller\Responses;

use Niccolo\DocparserPhp\Controller\Responses\BaseResponse;
use PHPUnit\Framework\TestCase;

class BaseResponseTest extends TestCase
{
    public function test_base_response_constructor(): void
    {
        $response = new BaseResponse();

        $this->assertInstanceOf(BaseResponse::class, $response);
    }

    public function test_base_response_custom_status_code(): void
    {
        $response = new BaseResponse(statusCode: 400);

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function test_base_response_default_status_code(): void
    {
        $response = new BaseResponse();

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_base_response_custom_content(): void
    {
        $response = new BaseResponse(content: 'Custom content');

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertEquals('Custom content', $response->getContent());
    }

    public function test_base_response_default_content(): void
    {
        $response = new BaseResponse();

        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertEquals('ok', $response->getContent());
    }
}
