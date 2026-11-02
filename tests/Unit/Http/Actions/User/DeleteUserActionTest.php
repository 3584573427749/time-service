<?php

declare(strict_types=1);

namespace Http\Actions\User;

use App\Application\Handlers\User\DeleteUserHandler;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\User\DeleteUserAction;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class DeleteUserActionTest extends TestCase {
    public function testDeletesUserAndReturns204() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(DeleteUserHandler::class);

        $handler
            ->expects(self::once())
            ->method('handle')
            ->with(
                self::callback(
                    fn (UserId $id) =>
                        $id->toString() === '550e8400-e29b-41d4-a716-446655440000',
                ),
            );

        $action = new DeleteUserAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/users/550e8400-e29b-41d4-a716-446655440000',
            )
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440000',
            );

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(
            204,
            $result->getStatusCode(),
        );
    }

    public function testThrowsExceptionForInvalidId() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(DeleteUserHandler::class);

        $handler
            ->expects(self::never())
            ->method('handle');

        $action = new DeleteUserAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/users/invalid',
            )
            ->withAttribute(
                'id',
                'invalid',
            );

        $response = (new ResponseFactory())->createResponse();

        self::expectException(\InvalidArgumentException::class);

        $action($request, $response, []);
    }
}
