<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Handlers\TimeEntry;

use App\Application\Handlers\TimeEntry\GetTimeEntryHandler;
use App\Domain\DataTransportObjects\TimeEntryDTO;
use App\Domain\Entities\TimeEntry;
use App\Domain\Exception\NotFoundException;
use App\Domain\Repositories\TimeEntryRepository;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class GetTimeEntryHandlerTest extends TestCase {
    public function testHandleReturnsTimeEntryDto():void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->isInstanceOf(
                    TimeEntryId::class,
                ),
            )
            ->willReturn(
                $this->createTimeEntry(),
            );

        $handler = new GetTimeEntryHandler($repository,);

        $result = $handler->handle(
            new TimeEntryId(
                '550e8400-e29b-41d4-a716-446655440001',
            ),
        );

        self::assertInstanceOf(TimeEntryDTO::class, $result);
    }

    public function testHandleThrowsNotFoundException():void {
        $repository = $this->createMock(TimeEntryRepository::class);

        $repository
            ->expects($this->once())
            ->method('get')
            ->willThrowException(
                new NotFoundException(
                    'Tidspost saknas',
                ),
            );

        $handler = new GetTimeEntryHandler($repository,);

        $this->expectException(NotFoundException::class);

        $handler->handle(
            new TimeEntryId(
                '550e8400-e29b-41d4-a716-446655440001',
            ),
        );
    }

    private function createTimeEntry():TimeEntry {
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
