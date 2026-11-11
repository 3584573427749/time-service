<?php

declare(strict_types=1);

namespace Tests\Integration\Http\TimeEntry;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class UpdateTimeEntryEndpointTest extends BaseApiTestCases {
    public function testReturns200WhenRequestIsValid() : void {
        $this->loadSchema('time_entries');

        $this->seed('time_entries', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activity_role' => 'Ledare',
                'group_id' => null,
                'comment' => 'Gammal kommentar',
                'approved_by' => null,
                'approved_at' => null,
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-05 12:00:00',
                'updated_at' => null,
            ],
        ]);

        $requestBody = [
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-06',
            'duration' => '04:15:00',
            'activityRole' => 'Assistent',
            'comment' => 'Uppdaterad kommentar',
        ];

        $validator = new OpenApiValidator();

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            )
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(
            json_encode($requestBody, JSON_THROW_ON_ERROR),
        );

        $request = $request->withParsedBody($requestBody);

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        self::assertSame(
            200,
            $response->getStatusCode(),
        );

        $validator->validateResponse(
            '/time-entry/{id}',
            'put',
            $response,
        );
    }

    public function testReturns422WhenRequestIsInvalid() : void {
        $this->loadSchema('time_entries');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'userId' => 'invalid',
                'date' => '2026-11-06',
                'duration' => '04:15:00',
                'activityRole' => 'Assistent',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(
            422,
            $response->getStatusCode(),
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/time-entry/{id}',
            'put',
            $response,
        );
    }

    public function testReturns400WhenValidationFails() : void {
        $this->loadSchema('time_entries');

        $this->seed('time_entries', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activity_role' => 'Ledare',
                'group_id' => null,
                'comment' => 'Gammal kommentar',
                'approved_by' => null,
                'approved_at' => null,
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-05 12:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'userId' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2999-12-31',
                'duration' => '04:15:00',
                'activityRole' => 'Assistent',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(
            400,
            $response->getStatusCode(),
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/time-entry/{id}',
            'put',
            $response,
        );
    }

    public function testReturns404WhenTimeEntryDoesNotExist() : void {
        $this->loadSchema('time_entries');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'userId' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-06',
                'duration' => '04:15:00',
                'activityRole' => 'Assistent',
            ]);

        $response = $this->app->handle($request);

        self::assertSame(
            404,
            $response->getStatusCode(),
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/time-entry/{id}',
            'put',
            $response,
        );
    }
}
