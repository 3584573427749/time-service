<?php

declare(strict_types=1);

namespace Tests\Integration\Http\User;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class UpsertUserEndpointTest extends BaseApiTestCases {
    public function testReturns200WhenRequestIsValidUserCreates() : void {
        $this->loadSchema('users');

        $requestBody = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'Bengt',
            'lastName' => 'Bertilsson',
            'active' => 0,
        ];

        $validator = new OpenApiValidator();

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/users',
            )
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(
            json_encode($requestBody, JSON_THROW_ON_ERROR),
        );

        $request = $request
            ->withParsedBody($requestBody);

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $validator->validateResponse(
            '/users',
            'post',
            $response,
        );

        $payload = json_decode(
            (string) $response->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        self::assertSame(
            'Bengt',
            $payload['data']['firstName'],
        );

        self::assertSame(
            0,
            $payload['data']['active'],
        );

        self::assertNull($payload['data']['updatedAt']);
    }

    public function testReturns200WhenRequestIsValidUserUpdates() : void {
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

        $requestBody = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'firstName' => 'Bengt',
            'lastName' => 'Bertilsson',
            'active' => 0,
        ];

        $validator = new OpenApiValidator();

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/users',
            )
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(
            json_encode($requestBody, JSON_THROW_ON_ERROR),
        );

        $request = $request
            ->withParsedBody($requestBody);

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $validator->validateResponse(
            '/users',
            'post',
            $response,
        );

        $payload = json_decode(
            (string) $response->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        self::assertSame(
            'Bengt',
            $payload['data']['firstName'],
        );

        self::assertSame(
            0,
            $payload['data']['active'],
        );

        self::assertNotNull($payload['data']['updatedAt']);
    }

    public function testReturns422WhenValidationFails() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/users',
            )
            ->withParsedBody([
                'id' => '660e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'A',
                'last_name' => 'B',
                'active' => '-2',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(422, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users',
            'post',
            $response,
        );
    }

    public function testReturns400WhenIdIsInvalid() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'POST',
                '/users',
            )
            ->withParsedBody([
                'id' => 'invalid',
                'firstName' => 'Pingvinen',
                'lastName' => 'Pingvinen',
                'active' => 1,
            ]);

        $response = $this->app->handle($request);

        self::assertSame(400, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users',
            'post',
            $response,
        );
    }
}
