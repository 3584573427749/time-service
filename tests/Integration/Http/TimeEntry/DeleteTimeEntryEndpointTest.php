<?php

declare(strict_types=1);

namespace Tests\Integration\Http\TimeEntry;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;
use Tests\Integration\OpenApi\OpenApiValidator;

final class DeleteTimeEntryEndpointTest extends BaseApiTestCases {
    public function testReturns204WhenTimeEntryExists() : void {
        $this->loadSchema('time_entries');

        $this->seed('time_entries', [
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activity_role' => 'Ledare',
                'group_id' => null,
                'comment' => null,
                'approved_by' => null,
                'approved_at' => null,
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-05 12:00:00',
                'updated_at' => null,
            ],
        ]);

        $validator = new OpenApiValidator();

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            );

        $validator->validateRequest($request);

        $response = $this->app->handle($request);

        self::assertSame(
            204,
            $response->getStatusCode(),
        );

        $validator->validateResponse(
            '/time-entry/{id}',
            'delete',
            $response,
        );
    }

    public function testReturns400WhenIdIsInvalid() : void {
        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/time-entry/invalid',
            );

        $response = $this->app->handle($request);

        self::assertSame(
            400,
            $response->getStatusCode(),
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/time-entry/{id}',
            'delete',
            $response,
        );
    }

    public function testReturns404WhenTimeEntryDoesNotExist() : void {
        $this->loadSchema('time_entries');

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'DELETE',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            );

        $response = $this->app->handle($request);

        self::assertSame(
            404,
            $response->getStatusCode(),
        );

        $validator = new OpenApiValidator();

        $validator->validateResponse(
            '/time-entry/{id}',
            'delete',
            $response,
        );
    }
}
