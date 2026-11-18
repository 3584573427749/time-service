<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\DataTransportObjects;

use App\Domain\DataTransportObjects\TimeEntriesExportRow;
use App\Domain\Entities\TimeEntry;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class TimeEntriesExportRowTest extends TestCase {
    public function testHeaderReturnsExpectedColumns() : void {
        self::assertSame('userId,date,duration,role,groupId' . PHP_EOL, TimeEntriesExportRow::header());
    }

    public function testToCsvStringReturnsExpectedValues() : void {
        $row = TimeEntriesExportRow::fromTimeEntry($this->createTimeEntry());

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000,2026-11-05,03:05:00,Ledare,660e8400-e29b-41d4-a716-446655440000' . PHP_EOL,
            $row->toCsvString(),
        );
    }

    public function testToCsvStringHandlesMissingGroupId() : void {
        $row = TimeEntriesExportRow::fromTimeEntry($this->createTimeEntryWithoutGroup());

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000,2026-11-05,03:05:00,Ledare,' . PHP_EOL,
            $row->toCsvString(),
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
            null,
            null,
            null,
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00', ),
            null,
        );
    }

    private function createTimeEntryWithoutGroup() : TimeEntry {
        return new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001', ),
            new UserId('550e8400-e29b-41d4-a716-446655440000', ),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
            'Ledare',
            null,
            null,
            null,
            null,
            null,
            null,
            new DateTimeValue('2026-11-05T12:00:00+00:00', ),
            null,
        );
    }
}
