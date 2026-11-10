<?php

declare(strict_types=1);

namespace Tests\Integration\Http\TimeEntry;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class AddTimeEntryEndpointTest extends BaseApiTestCases {
    public function testReturns201WhenRequestIsValid() : void {
        $this->loadSchema('users');
        $this->loadSchema('time_entries');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Test',
                'last_name' => 'User',
                'active' => 1,
                'created_at' => '2026-01-01 10:00:00',
            ],
        ]);

        $requestBody = [
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-05',
            'duration' => '03:05:00',
            'activityRole' => 'Ledare',
            'groupId' => '660e8400-e29b-41d4-a716-446655440000',
            'comment' => 'Testkommentar',
        ];

        $validator = new OpenApiValidator();

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(
            json_encode($requestBody, JSON_THROW_ON_ERROR),
        );

        $request = $request->withParsedBody($requestBody);

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        self::assertSame(
            201,
            $response->getStatusCode(),
        );

        $validator->validateResponse(
            '/time-entry',
            'post',
            $response,
        );
    }

    public function testReturns422WhenRequestIsInvalid() : void {
        $this->loadSchema('users');
        $this->loadSchema('time_entries');

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry')
            ->withParsedBody([
                'userId' => 'invalid',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activityRole' => 'Ledare',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(
            422,
            $response->getStatusCode(),
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/time-entry',
            'post',
            $response,
        );
    }

    public function testReturns400WhenDateIsInFuture() : void {
        $this->loadSchema('users');
        $this->loadSchema('time_entries');

        $this->seed('users', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'first_name' => 'Test',
                'last_name' => 'User',
                'active' => 1,
                'created_at' => '2026-01-01 10:00:00',
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry')
            ->withParsedBody([
                'userId' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2999-12-31',
                'duration' => '03:05:00',
                'activityRole' => 'Ledare',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(
            400,
            $response->getStatusCode(),
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/time-entry',
            'post',
            $response,
        );
    }
}
