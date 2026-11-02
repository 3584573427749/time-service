<?php

declare(strict_types=1);

namespace Http\Actions\User;

use App\Application\Handlers\User\GetUserHandler;
use App\Domain\DataTransportObjects\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\User\GetAllUsersAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class GetAllUsersActionTest extends TestCase {
    public function testReturnsUsers() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $user = new User(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            'Anna',
            'Andersson',
            1,
            new DateTimeValue('2026-06-10T10:00:00+00:00'),
            null,
        );

        $dto = UserDTO::fromEntity($user);

        $handler = $this->createMock(GetUserHandler::class);

        $handler
            ->expects(self::once())
            ->method('getAll')
            ->willReturn([$dto]);

        $action = new GetAllUsersAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/user');

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(200, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertCount(1, $payload['data']);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $payload['data'][0]['id'],
        );

        self::assertSame(
            'Anna',
            $payload['data'][0]['firstName'],
        );

        self::assertSame(
            'Andersson',
            $payload['data'][0]['lastName'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(
        ResponseInterface $response,
    ) : array {
        $body = (string)$response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode($body, true);

        self::assertIsArray($decoded);

        return $decoded;
    }

    public function testReturnsEmptyArrayWhenNoUsersExist() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(GetUserHandler::class);

        $handler
            ->expects(self::once())
            ->method('getAll')
            ->willReturn([]);

        $action = new GetAllUsersAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/user');

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame([], $payload['data']);
    }
}
