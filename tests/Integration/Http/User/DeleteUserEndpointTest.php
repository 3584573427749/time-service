<?php

declare(strict_types=1);

namespace Tests\Integration\Http\User;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class DeleteUserEndpointTest extends BaseApiTestCases {
    public function testReturns204WhenUserExists() : void {
        $this->loadSchema('users');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Anna',
                'last_name' => 'Andersson',
                'active' => 1,
                'created_at' => '2026-01-01 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/users/550e8400-e29b-41d4-a716-446655440000',
            )
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440000',
            );

        $response = $this->app->handle($request);

        self::assertSame(204, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}',
            'delete',
            $response,
        );
    }

    public function testReturns404WhenUserDoesNotExist() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/users/550e8400-e29b-41d4-a716-446655440000',
            )
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440000',
            );

        $response = $this->app->handle($request);

        self::assertSame(404, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}',
            'delete',
            $response,
        );
    }

    public function testReturns400WhenIdIsInvalid() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/users/invalid',
            )
            ->withAttribute(
                'id',
                'invalid',
            );

        $response = $this->app->handle($request);

        self::assertSame(400, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users/{id}',
            'delete',
            $response,
        );
    }
}
