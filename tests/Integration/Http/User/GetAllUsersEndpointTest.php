<?php

declare(strict_types=1);

namespace Tests\Integration\Http\User;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class GetAllUsersEndpointTest extends BaseApiTestCases {
    public function testReturns200AndEmptyArrayWhenNoUsersExist() : void {
        $this->loadSchema('users');

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/users');

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users',
            'get',
            $response,
        );

        $payload = json_decode(
            (string)$response->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        self::assertSame([], $payload['data']);
    }

    public function testReturns200AndUsers() : void {
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
            [
                'id' => '660e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Bertil',
                'last_name' => 'Bengtsson',
                'active' => 1,
                'created_at' => '2026-01-01 10:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest('GET', '/users');

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/users',
            'get',
            $response,
        );

        $payload = json_decode(
            (string)$response->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        self::assertCount(2, $payload['data']);

        $ids = array_column($payload['data'], 'id');

        self::assertContains('550e8400-e29b-41d4-a716-446655440000', $ids);
        self::assertContains('660e8400-e29b-41d4-a716-446655440000', $ids);
    }
}
