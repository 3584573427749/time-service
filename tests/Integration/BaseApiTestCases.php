<?php

declare(strict_types=1);

namespace Tests\Integration;

use Cake\Core\ContainerInterface;
use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Tests\Integration\OpenApi\OpenApiValidator;
use Tests\Unit\Infrastructure\Database\DatabaseBaseTestCase;

abstract class BaseApiTestCases extends DatabaseBaseTestCase {
    /**
     * @var App<ContainerInterface>
     */
    protected App $app;

    protected function setUp() : void {
        parent::setUp();

        $dotenv = Dotenv::createImmutable(
            dirname(__DIR__, 2),
            '.env.testing',
        );

        $dotenv->load();

        $this->app = require __DIR__ . '/../../app/bootstrap.php';
    }

    protected function validateResponse(
        string $path,
        string $method,
        ResponseInterface $response,
    ) : void {
        $validator = new OpenApiValidator();

        $validator->validateResponse(
            $path,
            $method,
            $response,
        );
    }

    protected function validateRequest(ServerRequestInterface $request) : void {
        $validator = new OpenApiValidator();

        $validator->validateRequest($request);
    }

    protected function authenticate(
        ServerRequestInterface $request,
    ) : ServerRequestInterface {
        return $request
            ->withHeader(
                'X-User-Id',
                '550e8400-e29b-41d4-a716-446655440000',
            )
            ->withHeader(
                'X-User-Roles',
                'time-attester',
            )
            ->withHeader(
                'X-Service-Permissions',
                'time.approve,time.export,time.view',
            );
    }
}
