<?php

declare(strict_types=1);

namespace Http\Actions\User;

use App\Application\Handlers\User\GetUserHandler;
use App\Domain\DataTransportObjects\UserDTO;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\User\GetUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class GetUserActionTest extends TestCase {
    public function testReturnsUser() : void {
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
            ->method('getId')
            ->with(
                self::callback(
                    fn (UserId $id) => $id->toString() === '550e8400-e29b-41d4-a716-446655440000',
                ),
            )
            ->willReturn($dto);

        $action = new GetUserAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/user/550e8400-e29b-41d4-a716-446655440000',
            )
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440000',
            );

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(200, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $payload['data']['id']);
        self::assertSame('Anna', $payload['data']['firstName']);
        self::assertSame('Andersson', $payload['data']['lastName']);
    }

    public function testThrowsExceptionForInvalidId() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(GetUserHandler::class);

        $handler
            ->expects(self::never())
            ->method('getId');

        $action = new GetUserAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/user/invalid',
            )
            ->withAttribute(
                'id',
                'invalid',
            );

        $response = (new ResponseFactory())->createResponse();

        self::expectException(\InvalidArgumentException::class);

        $action($request, $response, []);
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
}
