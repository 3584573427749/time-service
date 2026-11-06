<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Auth;

use App\Infrastructure\Auth\AuthServiceClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Slim\Psr7\Response;

final class AuthServiceClientTest extends TestCase {
    public function testValidateServiceTokenReturnsResponse():void {
        $response = new Response();

        $response->getBody()->write((string)json_encode(['valid' => true,]));

        $client = $this->createMock(
            Client::class,
        );

        $client
            ->expects($this->once())
            ->method('post')
            ->with(
                '/validate-service-token',
                [
                    'json' => [
                        'token' => 'token',
                        'service' => 'time-service',
                    ],
                ],
            )
            ->willReturn($response);

        $service = new AuthServiceClient(
            $client,
        );

        $result = $service->validateServiceToken(
            'token',
            'time-service',
        );

        self::assertTrue(
            $result['valid'],
        );
    }

    public function testValidateServiceTokenReturnsFalseWhenRequestFails():void {
        $client = $this->createMock(
            Client::class,
        );

        $request = $this->createMock(
            RequestInterface::class,
        );

        $client
            ->expects($this->once())
            ->method('post')
            ->willThrowException(
                new RequestException(
                    'Error',
                    $request,
                ),
            );

        $service = new AuthServiceClient(
            $client,
        );

        $result = $service->validateServiceToken(
            'token',
            'time-service',
        );

        self::assertSame(
            ['valid' => false],
            $result,
        );
    }
}
