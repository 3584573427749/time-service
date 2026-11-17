<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\TimeEntry;

use App\Application\Handlers\TimeEntry\ApproveTimeEntryHandler;
use App\Application\Security\CurrentUser;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\NotFoundException;
use App\Domain\Exception\TimeEntryAlreadyApprovedException;
use App\Domain\Repositories\TimeEntryRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class ApproveTimeEntryHandlerTest extends TestCase {
    public function testHandleApprovesTimeEntryAndReturnsDto() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $timeEntry = $this->createTimeEntry();

        $repository
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->isInstanceOf(
                    TimeEntryId::class,
                ),
            )
            ->willReturn($timeEntry);

        $repository
            ->expects($this->once())
            ->method('update')
            ->with(
                $this->isInstanceOf(
                    TimeEntry::class,
                ),
            );

        $handler = new ApproveTimeEntryHandler($repository, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440999', ),
            [],
            ['time.approve'],
        );

        $result = $handler->handle(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001', ),
            $currentUser,
        );

        self::assertInstanceOf(TimeEntryDTO::class, $result);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440999',
            $timeEntry->getApprovedBy()?->toString(),
        );

        self::assertNotNull($timeEntry->getApprovedAt());
    }

    public function testHandleThrowsExceptionWhenTimeEntryAlreadyApproved() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $timeEntry = $this->createApprovedTimeEntry();

        $repository
            ->expects($this->once())
            ->method('get')
            ->willReturn($timeEntry);

        $repository
            ->expects($this->never())
            ->method('update');

        $handler = new ApproveTimeEntryHandler($repository, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440999', ),
            [],
            ['time.approve'],
        );

        $this->expectException(TimeEntryAlreadyApprovedException::class);

        $this->expectExceptionMessage('Tidsposten är redan attesterad');

        $handler->handle(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001', ),
            $currentUser,
        );
    }

    public function testHandleThrowsNotFoundException() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->once())
            ->method('get')
            ->willThrowException(
                new NotFoundException(
                    'Tidspost saknas',
                ),
            );

        $handler = new ApproveTimeEntryHandler($repository, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440999', ),
            [],
            ['time.approve'],
        );

        $this->expectException(NotFoundException::class);

        $handler->handle(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001', ),
            $currentUser,
        );
    }

    private function createTimeEntry() : TimeEntry {
        return new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001', ),
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Ledare',
            new GroupId('660e8400-e29b-41d4-a716-446655440000', ),
            'Testkommentar',
            null,
            null,
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00', ),
            null,
        );
    }

    private function createApprovedTimeEntry() : TimeEntry {
        return new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001', ),
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Ledare',
            new GroupId('660e8400-e29b-41d4-a716-446655440000', ),
            'Testkommentar',
            new UserId('550e8400-e29b-41d4-a716-446655440555', ),
            new DateTimeValue('2026-11-05T13:00:00+00:00', ),
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00', ),
            new DateTimeValue('2026-11-05T13:00:00+00:00',),
        );
    }
}
