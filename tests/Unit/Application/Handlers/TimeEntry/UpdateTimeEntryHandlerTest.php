<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\TimeEntry;

use App\Application\Commands\TimeEntry\TimeEntryCommand;
use App\Application\Handlers\TimeEntry\UpdateTimeEntryHandler;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\FutureDateNotAllowedException;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\TimeEntryRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class UpdateTimeEntryHandlerTest extends TestCase {
    public function testHandleUpdatesTimeEntryAndReturnsDto() : void {
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

        $handler = new UpdateTimeEntryHandler($repository, );

        $command = TimeEntryCommand::fromRequest([
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-06',
            'duration' => '04:15:00',
            'activityRole' => 'Assistent',
            'groupId' => '660e8400-e29b-41d4-a716-446655440000',
            'comment' => 'Uppdaterad kommentar',
        ]);

        $result = $handler->handle($command);

        self::assertInstanceOf(TimeEntryDTO::class, $result);

        self::assertSame('Assistent', $timeEntry->getActivityRole());
        self::assertSame('2026-11-06', $timeEntry->getDate()->toString());
        self::assertSame('04:15:00', $timeEntry->getDuration()->toString());
        self::assertSame('Uppdaterad kommentar', $timeEntry->getComment());
        self::assertNotNull($timeEntry->getUpdatedAt());
    }

    public function testHandleThrowsExceptionForFutureDate() : void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->never())
            ->method('get');

        $handler = new UpdateTimeEntryHandler($repository, );

        $command = TimeEntryCommand::fromRequest([
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2999-12-31',
            'duration' => '03:05:00',
            'activityRole' => 'Ledare',
        ]);

        $this->expectException(FutureDateNotAllowedException::class);

        $handler->handle($command);
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

        $handler = new UpdateTimeEntryHandler($repository, );

        $command = TimeEntryCommand::fromRequest([
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-05',
            'duration' => '03:05:00',
            'activityRole' => 'Ledare',
        ]);

        $this->expectException(NotFoundException::class);

        $handler->handle($command);
    }

    private function createTimeEntry() : TimeEntry {
        return new TimeEntry(
            new TimeEntryId(
                '550e8400-e29b-41d4-a716-446655440001',
            ),
            new UserId(
                '550e8400-e29b-41d4-a716-446655440000',
            ),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Ledare',
            new GroupId(
                '660e8400-e29b-41d4-a716-446655440000',
            ),
            'Testkommentar',
            null,
            null,
            null,
            null,
            new DateTimeValue(
                '2026-11-05T12:00:00+00:00',
            ),
            null,
        );
    }
}
