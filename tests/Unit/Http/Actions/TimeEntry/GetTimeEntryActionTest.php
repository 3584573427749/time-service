<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\GetTimeEntryHandler;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\TimeEntry\GetTimeEntryAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class GetTimeEntryActionTest extends TestCase {
    public function testReturnsTimeEntry() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $timeEntry = new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001'),
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Ledare',
            new GroupId('660e8400-e29b-41d4-a716-446655440000'),
            'Testkommentar',
            null,
            null,
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00'),
            null,
        );

        $dto = TimeEntryDTO::fromEntity($timeEntry);

        $handler = $this->createMock(GetTimeEntryHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->isInstanceOf(TimeEntryId::class),
            )
            ->willReturn($dto);

        $action = new GetTimeEntryAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'GET',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            );

        $response = (new ResponseFactory())
            ->createResponse();

        $result = $action(
            $request,
            $response,
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
            ],
        );

        self::assertSame(
            200,
            $result->getStatusCode(),
        );

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(
            200,
            $payload['statusCode'],
        );

        self::assertArrayHasKey(
            'data',
            $payload,
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440001',
            $payload['data']['id'],
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $payload['data']['userId'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(
        ResponseInterface $response,
    ) : array {
        $body = (string) $response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode(
            $body,
            true,
        );

        self::assertIsArray($decoded);

        return $decoded;
    }
}
