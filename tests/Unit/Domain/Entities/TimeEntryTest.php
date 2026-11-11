<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Entities;

use App\Domain\Entities\TimeEntry;
use App\Domain\ValueObjects\DateTimeValue;
use App\Domain\ValueObjects\DateValue;
use App\Domain\ValueObjects\GroupId;
use App\Domain\ValueObjects\TimeEntryId;
use App\Domain\ValueObjects\TimeValue;
use App\Domain\ValueObjects\UserId;
use PHPUnit\Framework\TestCase;

final class TimeEntryTest extends TestCase {
    public function testCanBeCreatedFromDatabaseRow() : void {
        $entity = TimeEntry::fromDBRow([
            'id' => '550e8400-e29b-41d4-a716-446655440001',
            'user_id' => '550e8400-e29b-41d4-a716-446655440000',
            'date' => '2026-11-05',
            'duration' => '03:05:00',
            'activity_role' => 'Ledare',
            'group_id' => '660e8400-e29b-41d4-a716-446655440000',
            'comment' => 'Testkommentar',
            'approved_by' => null,
            'approved_at' => null,
            'exported_by' => null,
            'exported_at' => null,
            'created_at' => '2026-11-05 12:00:00',
            'updated_at' => null,
        ]);

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440001',
            $entity->getId()->toString(),
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $entity->getUserId()->toString(),
        );

        self::assertSame(
            'Ledare',
            $entity->getActivityRole(),
        );
    }

    public function testCanBeApproved() : void {
        $entity = $this->createTimeEntry();

        $entity->approve(
            new UserId('550e8400-e29b-41d4-a716-446655440002'),
            new DateTimeValue('2026-11-05T13:00:00+00:00'),
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440002',
            $entity->getApprovedBy()?->toString(),
        );

        self::assertNotNull(
            $entity->getApprovedAt(),
        );
    }

    public function testCanBeMarkedAsExported() : void {
        $entity = $this->createTimeEntry();

        $entity->markAsExported(
            new UserId('550e8400-e29b-41d4-a716-446655440003'),
            new DateTimeValue('2026-11-05T14:00:00+00:00'),
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440003',
            $entity->getExportedBy()?->toString(),
        );

        self::assertNotNull(
            $entity->getExportedAt(),
        );
    }

    public function testCanBeConvertedToDatabaseRow() : void {
        $entity = $this->createTimeEntry();

        $row = $entity->asDBRow();

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440001',
            $row['id'],
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $row['user_id'],
        );

        self::assertSame(
            '2026-11-05',
            $row['date'],
        );

        self::assertSame(
            '03:05:00',
            $row['duration'],
        );

        self::assertSame(
            'Ledare',
            $row['activity_role'],
        );
    }

    public function testCanBeSerializedToJson() : void {
        $entity = $this->createTimeEntry();

        $json = $entity->jsonSerialize();

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440001',
            $json['id'],
        );

        self::assertSame(
            '550e8400-e29b-41d4-a716-446655440000',
            $json['userId'],
        );

        self::assertSame(
            '2026-11-05',
            $json['date'],
        );

        self::assertSame(
            '03:05:00',
            $json['duration'],
        );

        self::assertSame(
            'Ledare',
            $json['activityRole'],
        );
    }

    private function createTimeEntry() : TimeEntry {
        return new TimeEntry(
            new TimeEntryId('550e8400-e29b-41d4-a716-446655440001'),
            new UserId('550e8400-e29b-41d4-a716-446655440000'),
            new DateValue('2026-11-05'),
            new TimeValue('03:05:00'),
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
    }
}
