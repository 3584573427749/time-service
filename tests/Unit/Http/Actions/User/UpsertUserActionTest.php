<?php

declare(strict_types=1);

namespace Http\Actions\User;

use App\Application\Commands\User\UpsertUserCommand;
use App\Application\Handlers\User\UpsertUserHandler;
use App\Domain\DataTransportObjects\UserDTO;
use App\Domain\Entities\User;
use App\Domain\Exception\ValidationException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\User\UpsertUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class UpsertUserActionTest extends TestCase {
    public function testUpsertUserReturns200WhenRequestBodyIsValid() : void {
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

        $handler = $this->createMock(UpsertUserHandler::class);

        $handler
            ->expects(self::once())
            ->method('handle')
            ->with(self::isInstanceOf(UpsertUserCommand::class))
            ->willReturn($dto);

        $action = new UpsertUserAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/user',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'firstName' => 'Anna',
                'lastName' => 'Andersson',
                'active' => 1,
            ]);

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(200, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $payload['data']['id'], );
        self::assertSame('Anna', $payload['data']['firstName'], );
        self::assertSame('Andersson', $payload['data']['lastName'], );
        self::assertSame(1, $payload['data']['active'], );
    }

    public function testThrowsValidationExceptionWhenRequestIsInvalid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(UpsertUserHandler::class);

        $handler
            ->expects(self::never())
            ->method('handle');

        $action = new UpsertUserAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/user',
            )
            ->withParsedBody([
                'id' => '660e8400-e29b-41d4-a716-446655440000',
                'firstName' => 'A',
                'lastName' => 'B',
                'active' => 2,
            ]);

        $response = (new ResponseFactory())->createResponse();

        self::expectException(ValidationException::class);
        self::expectExceptionMessage('Felaktig indata');

        $action($request, $response, []);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(
        ResponseInterface $response,
    ) : array {
        $body = (string) $response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode($body, true);

        self::assertIsArray($decoded);

        return $decoded;
    }
}
