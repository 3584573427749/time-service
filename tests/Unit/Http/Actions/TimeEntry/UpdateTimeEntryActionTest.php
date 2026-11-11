<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\UpdateTimeEntryHandler;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\ValidationException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\TimeEntry\UpdateTimeEntryAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class UpdateTimeEntryActionTest extends TestCase {
    public function testUpdatesTimeEntryAndReturns200() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $timeEntry = new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001'),
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Assistent',
            new GroupId('660e8400-e29b-41d4-a716-446655440000'),
            'Uppdaterad kommentar',
            null,
            null,
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00'),
            new DateTimeValue('2026-11-05T13:00:00+00:00'),
        );

        $dto = TimeEntryDTO::fromEntity($timeEntry);

        $handler = $this->createMock(
            UpdateTimeEntryHandler::class,
        );

        $handler
            ->expects($this->once())
            ->method('handle')
            ->willReturn($dto);

        $action = new UpdateTimeEntryAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            )
            ->withParsedBody([
                'id' => '550e8400-e29b-41d4-a716-446655440001',
                'userId' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activityRole' => 'Assistent',
                'groupId' => '660e8400-e29b-41d4-a716-446655440000',
                'comment' => 'Uppdaterad kommentar',
            ]);

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

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440001',
            $payload['data']['id'],
        );
    }

    public function testThrowsExceptionWhenRequestBodyIsInvalid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(
            UpdateTimeEntryHandler::class,
        );

        $handler
            ->expects($this->never())
            ->method('handle');

        $action = new UpdateTimeEntryAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001',
            )
            ->withParsedBody([
                'userId' => 'not-a-uuid',
                'date' => '2026-11-05',
                'duration' => '03:05:00',
                'activityRole' => 'Ledare',
            ]);

        $response = (new ResponseFactory())
            ->createResponse();

        $this->expectException(
            ValidationException::class,
        );

        $this->expectExceptionMessage(
            'Felaktig indata',
        );

        $action(
            $request,
            $response,
            [
                'id' => '550e8400-e29b-41d4-a716-446655440001',
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(
        ResponseInterface $response,
    ) : array {
        $body = (string)$response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode($body, true);

        self::assertIsArray($decoded);

        return $decoded;
    }
}
