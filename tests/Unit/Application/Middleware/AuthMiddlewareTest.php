<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Middleware;

use App\Application\Middleware\AuthMiddleware;
use App\Application\Security\CurrentUser;
use App\Domain\Exception\UnauthorizedException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class AuthMiddlewareTest extends TestCase {
    public function testAddsCurrentUserToRequest() : void {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/')
            ->withHeader(
                'X-User-Id',
                '550e8400-e29b-41d4-a716-446655440000',
            )
            ->withHeader(
                'X-User-Roles',
                'admin,time-attester',
            )
            ->withHeader(
                'X-Service-Permissions',
                'time.approve,time.export',
            );

        $handler = $this->createMock(RequestHandlerInterface::class);

        $response = (new ResponseFactory())
            ->createResponse();

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(function ($request) : bool {
                    $currentUser = $request->getAttribute(
                        'currentUser',
                    );

                    return $currentUser instanceof CurrentUser
                        && $currentUser
                            ->getUserId()
                            ->toString()
                        === '550e8400-e29b-41d4-a716-446655440000';
                }),
            )
            ->willReturn($response);

        $middleware = new AuthMiddleware();

        $result = $middleware->process($request, $handler);

        self::assertSame(200, $result->getStatusCode());
    }

    public function testThrowsExceptionWhenUserIdIsMissing() : void {
        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/');

        $handler = $this->createMock(RequestHandlerInterface::class);

        $this->expectException(UnauthorizedException::class);

        $this->expectExceptionMessage('Authentication required');

        $middleware = new AuthMiddleware();

        $middleware->process($request, $handler);
    }
}
