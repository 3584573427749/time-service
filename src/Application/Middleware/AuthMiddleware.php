<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Application\Security\CurrentUser;
use App\Domain\Exception\UnauthorizedException;
use App\Domain\ValueObjects\UserId;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

final class AuthMiddleware implements MiddlewareInterface {
    public function process(
        Request $request,
        Handler $handler,
    ) : Response {
        // Hämta variabler från headern
        $userId = $request->getHeaderLine('X-User-Id');
        $roles = $request->getHeaderLine('X-User-Roles');
        $permissions = $request->getHeaderLine('X-Service-Permissions');

        if (empty($userId)) {
            throw new UnauthorizedException('Authentication required', );
        }

        $currentUser = new CurrentUser(
            new UserId($userId),
            $roles ? explode(',', $roles) : [],
            $permissions ? explode(',', $permissions) : [],
        );

        return $handler->handle(
            $request->withAttribute('currentUser', $currentUser),
        );
    }
}
