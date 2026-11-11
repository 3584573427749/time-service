<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Commands\TimeEntry;

use App\Application\Commands\TimeEntry\TimeEntryCommand;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class TimeEntryCommandTest extends TestCase {
    public function testCreatesCommandFromRequest() : void {
        $command = TimeEntryCommand::fromRequest([
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-05',
            'duration' => '03:05',
            'activityRole' => 'Ledare',
            'groupId' => '660e8400-e29b-41d4-a716-446655440000',
            'comment' => 'Testkommentar',
        ]);

        self::assertInstanceOf(
            TimeEntryId::class,
            $command->id,
        );

        self::assertInstanceOf(
            UserId::class,
            $command->userId,
        );

        self::assertInstanceOf(
            DateValue::class,
            $command->date,
        );

        self::assertInstanceOf(
            TimeValue::class,
            $command->duration,
        );

        self::assertInstanceOf(
            GroupId::class,
            $command->groupId,
        );

        self::assertSame(
            'Ledare',
            $command->activityRole,
        );

        self::assertSame(
            'Testkommentar',
            $command->comment,
        );
    }

    public function testCreatesCommandWithoutId() : void {
        $command = TimeEntryCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-05',
            'duration' => '03:05',
            'activityRole' => 'Ledare',
        ]);

        self::assertNull($command->id);
    }

    public function testCreatesCommandWithoutGroupId() : void {
        $command = TimeEntryCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-05',
            'duration' => '03:05',
            'activityRole' => 'Ledare',
        ]);

        self::assertNull($command->groupId);
    }

    public function testCreatesCommandWithoutComment() : void {
        $command = TimeEntryCommand::fromRequest([
            'userId' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-05',
            'duration' => '03:05',
            'activityRole' => 'Ledare',
        ]);

        self::assertNull($command->comment);
    }
}
