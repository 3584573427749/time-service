<?php

declare(strict_types=1);

namespace App\Application\Middleware;

use App\Domain\Exception\UnauthorizedException;
use App\Infrastructure\Auth\AuthServiceClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

final class AuthServiceMiddleware implements MiddlewareInterface {
    public function __construct(
        private AuthServiceClient $client,
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function process(Request $request, Handler $handler) : Response {
        $token = $request->getHeaderLine('X-Service-Token');
        $serviceName = $_ENV['SERVICE_NAME'];

        if (!$token) {
            throw new UnauthorizedException('Missing service token');
        }

        $result = $this->client->validateServiceToken($token, $serviceName);

        if (!$result['valid']) {
            throw new UnauthorizedException('Invalid service token');
        }

        return $handler->handle($request);
    }
}
