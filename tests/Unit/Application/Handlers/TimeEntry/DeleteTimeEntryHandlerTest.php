<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\TimeEntry;

use App\Application\Handlers\TimeEntry\DeleteTimeEntryHandler;
use App\Application\Security\CurrentUser;
use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\ForbiddenException;
use App\Domain\Exception\TimeEntryAlreadyApprovedException;
use App\Domain\Repositories\TimeEntryRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class DeleteTimeEntryHandlerTest extends TestCase {
    public function testHandleDeletesTimeEntry() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createTimeEntry(),
            );

        $repository
            ->expects($this->once())
            ->method('delete')
            ->with(
                $this->isInstanceOf(
                    TimeEntryId::class,
                ),
            );

        $handler = new DeleteTimeEntryHandler($repository, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [],
        );

        $handler->handle(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001', ),
            $currentUser,
        );
    }

    public function testHandleThrowsExceptionWhenTimeEntryIsApproved() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createApprovedTimeEntry(),
            );

        $repository
            ->expects($this->never())
            ->method('delete');

        $handler = new DeleteTimeEntryHandler($repository, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            [],
            [],
        );

        $this->expectException(TimeEntryAlreadyApprovedException::class);

        $handler->handle(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001', ),
            $currentUser,
        );
    }

    public function testHandleThrowsExceptionWhenUserCannotDeleteForeignTimeEntry() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->once())
            ->method('get')
            ->willReturn(
                $this->createTimeEntry(),
            );

        $repository
            ->expects($this->never())
            ->method('delete');

        $handler = new DeleteTimeEntryHandler($repository, );

        $currentUser = new CurrentUser(
            new UserId('550e8400-e29b-41d4-a716-446655440999', ),
            [],
            [],
        );

        $this->expectException(ForbiddenException::class);

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
            null,
            'Testkommentar',
            null,
            null,
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00', ),
            new DateTimeValue('2026-11-05T13:00:00+00:00', ),
        );
    }

    private function createApprovedTimeEntry() : TimeEntry {
        return new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001', ),
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Ledare',
            null,
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
