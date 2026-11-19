<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\TimeEntry;

use App\Application\Commands\TimeEntry\ExportTimeEntriesCommand;
use App\Application\Handlers\TimeEntry\ExportTimeEntriesHandler;
use App\Application\Security\CurrentUser;
use App\Domain\DataTransportObjects\TimeEntriesExportRow;
use App\Domain\Entities\TimeEntry;
use App\Domain\Repositories\TimeEntryRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class ExportTimeEntriesHandlerTest extends TestCase {
    public function testHandleExportsTimeEntries() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->once())
            ->method('getUsersWithExportableTimeEntries')
            ->willReturn([new UserId('550e8400-e29b-41d4-a716-446655440000', ), ]);

        $repository
            ->expects($this->once())
            ->method('getApprovedNotExportedByUser')
            ->willReturn([$this->createTimeEntry(), ]);

        $repository
            ->expects($this->once())
            ->method('update')
            ->with($this->isInstanceOf(TimeEntry::class));

        $handler = new ExportTimeEntriesHandler($repository);

        $csv = $handler->handle(
            new ExportTimeEntriesCommand(null, ),
            $this->createCurrentUser(),
        );

        self::assertStringContainsString('userId,date,duration,role,groupId', $csv);

        self::assertStringContainsString('550e8400-e29b-41d4-a716-446655440000', $csv);
    }

    public function testHandleRollsBackWhenExceptionOccurs() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->once())
            ->method('getUsersWithExportableTimeEntries')
            ->willThrowException(
                new \RuntimeException(
                    'Test',
                ),
            );

        $handler = new ExportTimeEntriesHandler($repository);

        $this->expectException(\RuntimeException::class);

        $handler->handle(
            new ExportTimeEntriesCommand(null, ),
            $this->createCurrentUser(),
        );
    }

    public function testHandleReturnsFileWithJustHeaderWhenNoUsersIsFound() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $userId = new UserId('550e8400-e29b-41d4-a716-446655440000', );

        $repository
            ->expects($this->never())
            ->method('getUsersWithExportableTimeEntries');

        $repository
            ->expects($this->once())
            ->method('getApprovedNotExportedByUser')
            ->with($userId)
            ->willReturn([]);

        $handler = new ExportTimeEntriesHandler($repository) ;

        $csv = $handler->handle(
            new ExportTimeEntriesCommand([$userId]),
            $this->createCurrentUser(),
        );

        self::assertSame(TimeEntriesExportRow::header(), $csv, );
    }

    private function createCurrentUser() : CurrentUser {
        return new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440999', ),
            [],
            ['time.export', ],
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
            'Kommentar',
            new UserId('550e8400-e29b-41d4-a716-446655440998', ),
            new DateTimeValue('2026-11-05T13:00:00+00:00', ),
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00', ),
            null,
        );
    }
}
