<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Actions\TimeEntry;

use App\Application\Handlers\TimeEntry\ApproveTimeEntryHandler;
use App\Application\Security\CurrentUser;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\ForbiddenException;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use App\Http\Actions\TimeEntry\ApproveTimeEntryAction;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

final class ApproveTimeEntryActionTest extends TestCase {
    public function testApprovesTimeEntryAndReturns200() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $timeEntry = new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001'),
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Ledare',
            new GroupId('660e8400-e29b-41d4-a716-446655440000'),
            'Testkommentar',
            new UserId('550e8400-e29b-41d4-a716-446655440999'),
            new DateTimeValue('2026-11-05T13:00:00+00:00'),
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00'),
            null,
        );

        $dto = TimeEntryDTO::fromEntity($timeEntry);

        $handler = $this->createMock(ApproveTimeEntryHandler::class);

        $handler
            ->expects($this->once())
            ->method('handle')
            ->with(
                $this->isInstanceOf(TimeEntryId::class),
                $this->isInstanceOf(CurrentUser::class),
            )
            ->willReturn($dto);

        $action = new ApproveTimeEntryAction($logger, $handler, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            [],
            ['time.approve'],
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001/approve',
            )
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440001',
            )
            ->withAttribute(
                'currentUser',
                $currentUser,
            );

        $response = (new ResponseFactory())
            ->createResponse();

        $result = $action($request, $response, []);

        self::assertSame(200, $result->getStatusCode());

        $payload = $this->decodeJsonResponse($result);

        self::assertSame(200, $payload['statusCode']);
    }

    public function testThrowsExceptionWhenUserLacksPermission() : void {
        $logger = $this->createMock(LoggerInterface::class);

        $handler = $this->createMock(ApproveTimeEntryHandler::class);

        $handler
            ->expects($this->never())
            ->method('handle');

        $action = new ApproveTimeEntryAction(
            $logger,
            $handler,
        );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            [],
            [],
        );

        $request = (new ServerRequestFactory())
            ->createServerRequest(
                'PUT',
                '/time-entry/550e8400-e29b-41d4-a716-446655440001/approve',
            )
            ->withAttribute(
                'id',
                '550e8400-e29b-41d4-a716-446655440001',
            )
            ->withAttribute(
                'currentUser',
                $currentUser,
            );

        $response = (new ResponseFactory())
            ->createResponse();

        $this->expectException(ForbiddenException::class);

        $this->expectExceptionMessage('Saknar attestbehörighet');

        $action($request, $response, []);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(ResponseInterface $response) : array {
        $body = (string)$response->getBody();

        self::assertNotSame('', $body);

        $decoded = json_decode($body, true);

        self::assertIsArray($decoded);

        return $decoded;
    }
}
