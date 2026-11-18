<?php

declare(strict_types=1);

namespace Tests\Integration\Http\TimeEntry;

use Slim\Psr7\Factory\ServerRequestFactory;
use Tests\Integration\BaseApiTestCases;

final class ExportTimeEntriesEndpointTest extends BaseApiTestCases {
    public function testReturns200WhenExportingAllUsers() : void {
        $this->loadSchema('time_entries');

        $this->seed('time_entries', [
            // Ska exporteras
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'user_id' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activity_role' => 'Ledare',
                'group_id' => null,
                'comment' => null,
                'approved_by' => '550e8400-e29b-41d4-a716-446655440999',
                'approved_at' => '2026-11-05 13:00:00',
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-05 12:00:00',
                'updated_at' => null,
            ],

            // Ska inte exporteras (redan exporterad)
            [
                'id' => '550e8400-e29b-41d4-a716-446655440002',
                'user_id' => '550e8400-e29b-41d4-a716-446655440001',
                'date' => '2026-11-06',
                'duration' => '02:00:00',
                'activity_role' => 'Assistent',
                'group_id' => null,
                'comment' => null,
                'approved_by' => '550e8400-e29b-41d4-a716-446655440999',
                'approved_at' => '2026-11-06 13:00:00',
                'exported_by' => '550e8400-e29b-41d4-a716-446655440111',
                'exported_at' => '2026-11-07 09:00:00',
                'created_at' => '2026-11-06 12:00:00',
                'updated_at' => null,
            ],

            // Ska inte exporteras (ej attesterad)
            [
                'id' => '550e8400-e29b-41d4-a716-446655440003',
                'user_id' => '550e8400-e29b-41d4-a716-446655440002',
                'date' => '2026-11-07',
                'duration' => '01:00:00',
                'activity_role' => 'Utbildare',
                'group_id' => null,
                'comment' => null,
                'approved_by' => null,
                'approved_at' => null,
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-07 12:00:00',
                'updated_at' => null,
            ],
        ]);

        $request = $this->authenticate(
            (new ServerRequestFactory())
                ->createServerRequest('POST', '/time-entry/export'),
        )->withHeader('Content-Type', 'application/json');
        $request->getBody()->write('{}');

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $csv = (string)$response->getBody();

        self::assertStringContainsString('550e8400-e29b-41d4-a716-446655440000', $csv);

        self::assertStringNotContainsString('550e8400-e29b-41d4-a716-446655440001', $csv);

        self::assertStringNotContainsString('550e8400-e29b-41d4-a716-446655440002', $csv);

        $this->validateRequest($request);

        $this->validateResponse('/time-entry/export', 'post', $response);
    }

    public function testReturns200WhenExportingSpecifiedUsers() : void {
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
                'approved_by' => '550e8400-e29b-41d4-a716-446655440999',
                'approved_at' => '2026-11-05 13:00:00',
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-05 12:00:00',
                'updated_at' => null,
            ],
            [
                'id' => '550e8400-e29b-41d4-a716-446655440002',
                'user_id' => '550e8400-e29b-41d4-a716-446655440001',
                'date' => '2026-11-06',
                'duration' => '02:00:00',
                'activity_role' => 'Assistent',
                'group_id' => null,
                'comment' => null,
                'approved_by' => '550e8400-e29b-41d4-a716-446655440999',
                'approved_at' => '2026-11-06 13:00:00',
                'exported_by' => null,
                'exported_at' => null,
                'created_at' => '2026-11-06 12:00:00',
                'updated_at' => null,
            ],
        ]);

        $payload = [
            'users' => [
                '550e8400-e29b-41d4-a716-446655440000',
            ],
        ];

        $request = $this->authenticate(
            (new ServerRequestFactory())
                ->createServerRequest(
                    'POST',
                    '/time-entry/export',
                ),
        )
                        ->withHeader(
                            'Content-Type',
                            'application/json',
                        );

        $request->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));

        $request = $request->withParsedBody($payload);

        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());

        $csv = (string)$response->getBody();

        self::assertStringContainsString('550e8400-e29b-41d4-a716-446655440000', $csv);

        self::assertStringNotContainsString('550e8400-e29b-41d4-a716-446655440001', $csv);

        $this->validateRequest($request);

        $this->validateResponse('/time-entry/export', 'post', $response);
    }

    public function testReturns422WhenUsersArrayIsEmpty() : void {
        $payload = ['users' => []];

        $request = $this->authenticate(
            (new ServerRequestFactory())
                ->createServerRequest('POST', '/time-entry/export'),
        )
                        ->withHeader(
                            'Content-Type',
                            'application/json',
                        );

        $request->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));

        $response = $this->app->handle($request);

        self::assertSame(422, $response->getStatusCode());

        $this->validateRequest($request);

        $this->validateResponse('/time-entry/export', 'post', $response);
    }

    public function testReturns401WhenAuthenticationIsMissing() : void {
        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry/export')
        ->withHeader('Content-Type', 'application/json');
        $request->getBody()->write('{}');

        $response = $this->app->handle($request);

        self::assertSame(401, $response->getStatusCode());

        $this->validateRequest($request);

        $this->validateResponse('/time-entry/export', 'post', $response);
    }

    public function testReturns403WhenUserLacksExportPermission() : void {
        $request = $this->authenticate(
            (new ServerRequestFactory())
                ->createServerRequest('POST', '/time-entry/export'),
        )->withHeader('Content-Type', 'application/json')
                        ->withoutHeader('X-Service-Permissions');
        $request->getBody()->write('{}');

        $response = $this->app->handle($request);

        self::assertSame(403, $response->getStatusCode());

        $this->validateRequest($request);

        $this->validateResponse('/time-entry/export', 'post', $response);
    }

    public function testReturns422WhenUserIdIsInvalid() : void {
        $payload = ['users' => ['invalid-user-id', ]];

        $request = $this->authenticate(
            (new ServerRequestFactory())
                ->createServerRequest('POST', '/time-entry/export'),
        )
                        ->withHeader(
                            'Content-Type',
                            'application/json',
                        );

        $request->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));

        $response = $this->app->handle($request);

        self::assertSame(422, $response->getStatusCode());

        //Requesten ska inte valideras eftersom den är felaktig
        //$this->validateRequest($request);

        $this->validateResponse(
            '/time-entry/export',
            'post',
            $response,
        );
    }
}
