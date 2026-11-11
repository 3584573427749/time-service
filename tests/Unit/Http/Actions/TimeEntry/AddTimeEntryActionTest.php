<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\TimeEntry;

use App\Application\Commands\TimeEntry\TimeEntryCommand;
use App\Application\Handlers\TimeEntry\AddTimeEntryHandler;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\ValidationException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\TimeEntry\AddTimeEntryAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class AddTimeEntryActionTest extends TestCase {
    public function testAddsTimeEntryAndReturns201WhenRequestBodyIsValid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $timeEntry = new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001'),
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new DateValue('2026-11-05'),
            new TimeValue('03:05'),
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

        $handler = $this->createMock(AddTimeEntryHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->isInstanceOf(TimeEntryCommand::class))
            ->willReturn($dto);

        $action = new AddTimeEntryAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry')
            ->withParsedBody([
                'userId' => '550e8400-e29b-41d4-a716-446655440000',
                'date' => '2026-11-05',
                'duration' => '03:05',
                'activityRole' => 'Ledare',
                'groupId' => '660e8400-e29b-41d4-a716-446655440000',
                'comment' => 'Testkommentar',
            ]);

        $response = (new ResponseFactory())->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(201, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(201, $payload['statusCode']);

        self::assertArrayHasKey('data', $payload);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440001',
            $payload['data']['id'],
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $payload['data']['userId'],
        );
    }

    public function testAddsTimeEntryAndThrowsExceptionWhenRequestBodyIsInvalid() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(AddTimeEntryHandler::class);

        $handler
            ->expects($this->never())
            ->method('handle');

        $action = new AddTimeEntryAction(
            $logger,
            $handler,
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest('POST', '/time-entry')
            ->withParsedBody([
                'userId' => 'not-a-uuid',
                'date' => '2026-11-05',
                'duration' => '03:05',
                'activityRole' => 'Ledare',
            ]);

        $response = (new ResponseFactory())->createResponse();

        self::expectException(ValidationException::class);
        self::expectExceptionMessage('Felaktig indata');

        $action($request, $response, []);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(ResponseInterface $response) : array {
        $body = (string) $response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode($body, true);

        self::assertIsArray($decoded);

        return $decoded;
    }
}
