<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Commands\TimeEntry;

use App\Application\Commands\TimeEntry\SearchTimeEntriesCommand;
use App\Domain\Enums\TimeEntryStatus;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class SearchTimeEntriesCommandTest extends TestCase {
    public function testCreatesCommandWithDefaultValues() : void {
        $command = SearchTimeEntriesCommand::fromRequest([]);

        self::assertSame(0, $command->firstRecord);
        self::assertSame(50, $command->maxRecords);

        self::assertNull($command->users);
        self::assertNull($command->from);
        self::assertNull($command->to);
        self::assertNull($command->status);
        self::assertNull($command->activityRole);

        self::assertSame('date', $command->sortBy);
        self::assertSame('asc', $command->sortDirection);
    }

    public function testCreatesCommandWithUsers() : void {
        $command = SearchTimeEntriesCommand::fromRequest([
            'users' => [
                '550e8400-e29b-41d4-a716-446655440000',
                '550e8400-e29b-41d4-a716-446655440001',
            ],
        ]);

        self::assertCount(2, $command->users);

        self::assertContainsOnlyInstancesOf(UserId::class, $command->users);

        self::assertSame('550e8400-e29b-41d4-a716-446655440000', $command->users[0]->toString());

        self::assertSame('550e8400-e29b-41d4-a716-446655440001', $command->users[1]->toString());
    }

    public function testCreatesCommandWithAllValues() : void {
        $command = SearchTimeEntriesCommand::fromRequest([
            'firstRecord' => 25,
            'maxRecords' => 100,
            'users' => [
                '550e8400-e29b-41d4-a716-446655440000',
            ],
            'from' => '2026-11-01',
            'to' => '2026-11-30',
            'status' => 'approved',
            'activityRole' => 'Ledare',
            'sortBy' => 'status',
            'sortDirection' => 'desc',
        ]);

        self::assertSame(25, $command->firstRecord);
        self::assertSame(100, $command->maxRecords);

        self::assertCount(1, $command->users);

        self::assertSame('2026-11-01', $command->from?->toString());

        self::assertSame('2026-11-30', $command->to?->toString());

        self::assertSame(TimeEntryStatus::APPROVED, $command->status);

        self::assertSame('Ledare', $command->activityRole);

        self::assertSame('status', $command->sortBy);

        self::assertSame('desc', $command->sortDirection);
    }
}
